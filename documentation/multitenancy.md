# Multitenancy Payment Gateway Configuration

## Overview

This application supports multitenancy, allowing multiple domains to point to the same codebase while each domain uses a different payment gateway. You can deploy the application on 4-5 different domain names, with each domain configured to use Braintree, Stripe, or PayPal.

**Key Concept:** All payment gateway credentials are stored in your `.env` file. Tenant configurations only specify **which gateway** each domain should use, not the credentials themselves.

---

## Quick Start

### 1. Run Migrations

```bash
php artisan migrate
```

This creates:
- `tenant_configs` table - Stores domain-to-gateway mappings
- Adds `domain` column to `orders` table - Tracks which domain each order came from

### 2. Configure Environment

Add to your `.env` file:

```env
# Enable multitenancy
TENANT_MODE_ENABLED=true
TENANT_CACHE_TTL=3600

# Default gateway (for unconfigured domains)
PAYMENT_GATEWAY=braintree

# Braintree Configuration
BRAINTREE_ENVIRONMENT=sandbox
BRAINTREE_MERCHANT_ID=your_merchant_id
BRAINTREE_PUBLIC_KEY=your_public_key
BRAINTREE_PRIVATE_KEY=your_private_key

# Stripe Configuration
STRIPE_KEY=pk_test_your_key
STRIPE_SECRET=sk_test_your_secret
STRIPE_WEBHOOK_SECRET=whsec_your_webhook_secret

# PayPal Configuration
PAYPAL_MODE=sandbox
PAYPAL_CLIENT_ID=your_client_id
PAYPAL_SECRET=your_secret
```

### 3. Configure Domains

**Via Admin Panel (Recommended):**
1. Go to `/admin/tenants`
2. Click "Add New Tenant"
3. Enter domain (e.g., "domain1.com" without http:// or www)
4. Select payment gateway
5. Check "Active" and save

**Via Seeder (For Testing):**
```bash
php artisan db:seed --class=TenantConfigSeeder
```

### 4. Point Domains to Server

Configure DNS:
```
domain1.com     A     your.server.ip
domain2.com     A     your.server.ip
domain3.com     A     your.server.ip
```

---

## Architecture

### Components

1. **TenantConfig Model** - Stores domain-to-gateway mappings
2. **TenantResolver Service** - Detects current domain and loads configuration
3. **ResolveTenant Middleware** - Runs on every request to resolve tenant
4. **PaymentGatewayManager** - Routes to correct gateway based on tenant
5. **Admin Interface** - CRUD interface at `/admin/tenants`

### How It Works

```
User visits domain1.com
    ↓
ResolveTenant Middleware detects domain
    ↓
TenantResolver checks cache for tenant config
    ↓
If found: Use tenant's gateway (e.g., Stripe)
If not found: Use default gateway from .env
    ↓
PaymentGatewayManager initializes correct gateway
    ↓
Order created with domain information
```

### Database Schema

**tenant_configs table:**
- `id` - Primary key
- `domain` - Unique domain name (e.g., "domain1.com")
- `payment_gateway` - Gateway type: braintree, stripe, or paypal
- `is_active` - Boolean flag to enable/disable tenant
- `settings` - Additional tenant-specific settings (JSON)
- `created_at`, `updated_at` - Timestamps

**orders table (updated):**
- Added `domain` column to track which domain each order came from

---

## Admin Interface

### Viewing Tenants

Navigate to **Admin > Tenants** (`/admin/tenants`) to see:
- Domain name
- Assigned payment gateway
- Active status
- Creation date
- Edit/Delete actions

### Creating a Tenant

1. Click "Add New Tenant"
2. **Domain**: Enter without http:// or www (e.g., "domain1.com")
3. **Payment Gateway**: Select Braintree, Stripe, or PayPal
4. **Active**: Check to enable
5. Click "Create Tenant"

### Editing a Tenant

1. Click "Edit" next to the tenant
2. Modify domain, gateway, or active status
3. Click "Update Tenant"
4. Cache is automatically cleared

### Deleting a Tenant

1. Click "Delete" next to the tenant
2. Confirm deletion
3. Domain will now use the default gateway from `.env`

---

## Configuration Examples

### Example 1: Three Domains, Three Gateways

**Environment (.env):**
- All three gateways configured with credentials

**Tenant Configurations:**
- `domain1.com` → Braintree
- `domain2.com` → Stripe
- `domain3.com` → PayPal

**Result:**
- Customer visits `domain1.com` → Pays via Braintree
- Customer visits `domain2.com` → Pays via Stripe
- Customer visits `domain3.com` → Pays via PayPal
- Customer visits `domain4.com` (not configured) → Pays via default (Braintree)

### Example 2: All Domains Use Same Gateway

**Use Case:** Different brands, same payment processor

**Configuration:**
- `brand1.com` → Stripe
- `brand2.com` → Stripe
- `brand3.com` → Stripe

All use the same Stripe credentials from `.env`

### Example 3: Testing and Production

**Configuration:**
- `test.domain.com` → Braintree (with sandbox credentials in .env)
- `www.domain.com` → Braintree (with production credentials in .env)

---

## Web Server Configuration

### Apache (XAMPP)

No changes needed. The application automatically detects the domain from the request.

### Nginx

Add all domains to `server_name`:

```nginx
server {
    listen 80;
    server_name domain1.com domain2.com domain3.com;
    
    root /path/to/gbp-securelocal.com/public;
    index index.php;
    
    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }
    
    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php8.2-fpm.sock;
        fastcgi_index index.php;
        fastcgi_param SCRIPT_FILENAME $document_root$fastcgi_script_name;
        include fastcgi_params;
    }
}
```

---

## Testing

### Local Testing

1. Add test domains to hosts file:
   - **Windows**: `C:\Windows\System32\drivers\etc\hosts`
   - **Mac/Linux**: `/etc/hosts`

```
127.0.0.1   test-domain1.local
127.0.0.1   test-domain2.local
```

2. Create tenant configurations for these domains
3. Visit `http://test-domain1.local/services/checkout`
4. Complete a test checkout
5. Verify correct gateway is used

### Verify Configuration

Check which gateway a domain is using:

```php
// In any controller or view
$tenant = request()->attributes->get('tenant');
if ($tenant) {
    echo "Domain: {$tenant->domain}";
    echo "Gateway: {$tenant->payment_gateway}";
} else {
    echo "Using default gateway from .env";
}
```

### Test Commands

```bash
# Check migrations
php artisan migrate:status

# Clear caches
php artisan cache:clear
php artisan config:clear

# Verify tenant mode
php artisan tinker
>>> config('payment.tenant_mode_enabled')
```

---

## Monitoring

### View Orders by Domain

```sql
SELECT domain, payment_gateway, COUNT(*) as total, SUM(total) as revenue
FROM orders
WHERE deleted_at IS NULL
GROUP BY domain, payment_gateway
ORDER BY domain;
```

### Check Active Tenants

```sql
SELECT domain, payment_gateway, is_active, created_at
FROM tenant_configs
WHERE is_active = 1
ORDER BY domain;
```

### Recent Orders

```sql
SELECT id, domain, payment_gateway, total, payment_status, created_at
FROM orders
ORDER BY created_at DESC
LIMIT 20;
```

---

## Troubleshooting

### Issue: Tenant configuration not working

**Solutions:**
1. Verify `TENANT_MODE_ENABLED=true` in `.env`
2. Check domain matches exactly (no http://, no www, case-insensitive)
3. Ensure tenant is active in admin panel
4. Clear cache: `php artisan cache:clear`
5. Check middleware is registered in `bootstrap/app.php`

### Issue: Payment gateway errors

**Solutions:**
1. Verify gateway credentials in `.env` are correct
2. Check if gateway is in correct mode (sandbox vs production)
3. Review logs: `storage/logs/laravel.log`
4. Test with default .env configuration first
5. Verify gateway is properly configured in `config/payment.php`

### Issue: Wrong gateway being used

**Solutions:**
1. Check tenant configuration in admin panel
2. Verify domain name matches exactly
3. Check if tenant is active
4. Clear cache: `php artisan cache:clear`
5. Check logs for tenant resolution messages

### Issue: Domain not detected correctly

**Solutions:**
1. Check web server configuration
2. Verify DNS is pointing correctly
3. Test domain detection: `request()->getHost()`
4. Ensure middleware is running

### Issue: Changes not taking effect

```bash
php artisan cache:clear
php artisan config:clear
php artisan view:clear
```

---

## API Reference

### TenantConfig Model

```php
// Find tenant by domain
$tenant = TenantConfig::findByDomain('domain1.com');

// Check if gateway is configured
$tenant->isGatewayConfigured(); // returns bool

// Get gateway display name
$tenant->getGatewayName(); // returns "Braintree", "Stripe", or "PayPal"

// Scopes
TenantConfig::active()->get(); // Get all active tenants
TenantConfig::byDomain('domain1.com')->first(); // Find by domain
```

### TenantResolver Service

```php
$resolver = app(TenantResolver::class);

// Resolve tenant for current request
$tenant = $resolver->resolve();

// Resolve specific domain
$tenant = $resolver->resolve('domain1.com');

// Get current domain
$domain = $resolver->getCurrentDomain();

// Get payment gateway for current tenant
$gateway = $resolver->getPaymentGateway();

// Check if tenant config should be used
if ($resolver->shouldUseTenantConfig()) {
    // Tenant-specific gateway will be used
}

// Clear cache
$resolver->clearCache('domain1.com');
TenantResolver::clearAllTenantCache();
```

### PaymentGatewayManager

```php
$manager = app(PaymentGatewayManager::class);

// Get gateway for current tenant (auto-resolved)
$gateway = $manager->driver();

// Get gateway for specific domain
$gateway = $manager->driverForTenant('domain1.com');

// Get current gateway name
$gatewayName = $manager->getCurrentGateway();

// Check if in tenant mode
if ($manager->isTenantMode()) {
    // Using tenant-specific gateway
}
```

---

## Security

### Credentials Storage
- All gateway credentials stored in `.env` file
- Never commit `.env` to version control
- Use different credentials for sandbox/production
- Rotate credentials regularly

### Admin Access
- Only authenticated admin users can manage tenants
- Admin panel protected by authentication middleware
- Consider adding IP restrictions for admin panel

### HTTPS
- Always use HTTPS in production
- Protects payment information and API credentials
- Required by most payment gateways

### Database Backups
- Regular backups of tenant configurations
- Test restore procedures
- Keep backups secure and encrypted

---

## Best Practices

1. **Configure All Gateways in .env** - Even if not using all immediately
2. **Use Sandbox Mode for Testing** - Test thoroughly before production
3. **Monitor Logs** - Watch for tenant resolution issues
4. **Document Domain Mappings** - Keep external record of which domain uses which gateway
5. **Test Each Domain** - After configuration changes
6. **Set Up Alerts** - For failed payments per domain
7. **Regular Cache Clearing** - After tenant configuration changes
8. **Keep Default Gateway** - As reliable fallback

---

## Limitations & Considerations

### Shared Credentials
- All domains using the same gateway share the same credentials
- Each gateway type (Braintree, Stripe, PayPal) uses one account
- If you need different credentials per domain, you'll need separate gateway accounts

### Cache Management
- Tenant configurations are cached for performance
- Cache automatically cleared when configurations change
- Manual clearing may be needed in some cases

### Domain Matching
- Domain matching is case-insensitive
- "www" prefix is automatically stripped
- Exact match required (subdomains are treated as separate domains)

---

## Fallback Behavior

If a domain is not configured:
1. System uses default gateway from `.env` (`PAYMENT_GATEWAY`)
2. Default credentials from `.env` are used
3. Order is still created with domain information
4. No errors or interruptions to checkout process

This ensures the application continues to work even if tenant configuration is missing.

---

## Migration from Single-Tenant

If migrating from single-tenant setup:

1. **Backup database** before making changes
2. **Run migrations** to add new tables/columns
3. **Keep existing .env** configuration as default
4. **Set `TENANT_MODE_ENABLED=false`** initially
5. **Test thoroughly** with tenant mode disabled
6. **Create tenant configurations** for your domains
7. **Enable tenant mode** with `TENANT_MODE_ENABLED=true`
8. **Test each domain** individually

---

## Support & Maintenance

### Log Files
- Check `storage/logs/laravel.log` for errors
- Look for "Tenant resolved" messages
- Monitor "No tenant configuration found" warnings

### Cache Management
```bash
# Clear all caches
php artisan cache:clear
php artisan config:clear
php artisan view:clear

# Clear specific tenant cache
php artisan tinker
>>> app(\App\Services\TenantResolver::class)->clearCache('domain1.com')
```

### Database Maintenance
```sql
-- Check tenant configurations
SELECT * FROM tenant_configs ORDER BY domain;

-- Check orders by domain
SELECT domain, COUNT(*) as orders, SUM(total) as revenue
FROM orders
GROUP BY domain;

-- Find inactive tenants
SELECT * FROM tenant_configs WHERE is_active = 0;
```

---

## Quick Reference

| Task | Command/URL |
|------|-------------|
| Admin Interface | `/admin/tenants` |
| Run Migrations | `php artisan migrate` |
| Seed Test Data | `php artisan db:seed --class=TenantConfigSeeder` |
| Clear Cache | `php artisan cache:clear` |
| Check Tenant Mode | `config('payment.tenant_mode_enabled')` |
| View Logs | `storage/logs/laravel.log` |

### Environment Variables

```env
TENANT_MODE_ENABLED=true          # Enable multitenancy
TENANT_CACHE_TTL=3600             # Cache duration in seconds
PAYMENT_GATEWAY=braintree         # Default gateway
```

---

**Last Updated:** December 28, 2025

**Version:** 1.0 (Simplified - Gateway Selection Only)
