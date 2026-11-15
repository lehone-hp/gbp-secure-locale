## Admin Back-office

### Overview
The admin panel uses Bootstrap 4 and a separate template from the main website.

**Admin Assets:** `public/admin_assets`  
**Admin Views:** `resources/views/admin`  
**Admin Controllers:** `app/Http/Controllers/Admin`  
**Admin Sidebar:** `resources/views/admin/layouts/sidebar.blade.php`

---

### Existing Features
- Dashboard
- Settings Management
- Profile Management
- FAQ Management
- Staff Management
- Authentication System

---

### Planned Features
See detailed implementation plan in `@documentation/admin-features-plan.md`

#### Phase 1: Core CRUD Operations
1. **Services Management**
   - Create, edit, delete services
   - Bilingual support (EN/FR)
   - Slug management

2. **Service Addons Management**
   - Create, edit, delete addons
   - Bilingual support (EN/FR)
   - Price and type management

3. **Orders Management**
   - View all orders
   - View order details and items
   - Filter by status/date

#### Phase 2: Advanced Features
4. **Translation Management**
   - Edit service translations
   - Edit addon translations
   - Side-by-side EN/FR editing

---

### Design Principles
- Keep UI simple with Bootstrap 4 components
- Use standard tables, forms, and buttons
- Minimal JavaScript
- Server-side rendering
- Consistent patterns across all pages

---

### Quick Links
- **Full Implementation Plan:** `documentation/admin-features-plan.md`
- **Translation System:** `documentation/translation.md`
- **Admin Routes:** `routes/web.php` (admin prefix)

---

### Getting Started
1. Review the implementation plan: `admin-features-plan.md`
2. Start with Services Management (Phase 1)
3. Follow the provided code patterns and UI components
4. Test each feature before moving to the next
