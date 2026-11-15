## Achievements and Clients Implementation Plan

## Overview
We need to add a new page on the website where we can show our achievements. This page will have two main sections:
- **Our Certifications**: Display certification images (similar to our-story section)
- **Our Clients**: Display client logos with names and optional links (4 per row)

Both sections will be manageable from the admin panel.

---

## Phase 1: Database Architecture

### 1.1 Certifications Table

**Migration**: `create_certifications_table.php`

```php
Schema::create('certifications', function (Blueprint $table) {
    $table->id();
    $table->json('name'); // Translatable: en, fr
    $table->string('image'); // Path to certification image
    $table->integer('display_order')->default(0); // For sorting
    $table->boolean('is_active')->default(true);
    $table->timestamps();
});
```

**Fields**:
- `id`: Primary key
- `name`: JSON field for translatable certification name
- `image`: Image path (stored in `storage/app/public/certifications/`)
- `display_order`: Integer for manual sorting
- `is_active`: Toggle visibility
- `timestamps`: Created/updated dates

---

### 1.2 Clients Table

**Migration**: `create_clients_table.php`

```php
Schema::create('clients', function (Blueprint $table) {
    $table->id();
    $table->string('name');
    $table->string('logo'); // Path to client logo
    $table->string('website_url')->nullable(); // Optional client website
    $table->integer('display_order')->default(0); // For sorting
    $table->boolean('is_active')->default(true);
    $table->timestamps();
});
```

**Fields**:
- `id`: Primary key
- `name`: Client name (not translatable)
- `logo`: Logo path (stored in `storage/app/public/clients/`)
- `website_url`: Optional URL to client website
- `display_order`: Integer for manual sorting
- `is_active`: Toggle visibility
- `timestamps`: Created/updated dates

---

## Phase 2: Models

### 2.1 Certification Model

**File**: `app/Models/Certification.php`

```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Translatable\HasTranslations;

class Certification extends Model
{
    use HasFactory, HasTranslations;

    public $translatable = ['name'];

    protected $fillable = [
        'name',
        'image',
        'display_order',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    // Scope for active certifications
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    // Scope for ordered certifications
    public function scopeOrdered($query)
    {
        return $query->orderBy('display_order', 'asc');
    }
}
```

---

### 2.2 Client Model

**File**: `app/Models/Client.php`

```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Client extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'logo',
        'website_url',
        'display_order',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    // Scope for active clients
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    // Scope for ordered clients
    public function scopeOrdered($query)
    {
        return $query->orderBy('display_order', 'asc');
    }
}
```

---

## Phase 3: Admin Controllers

### 3.1 CertificationController

**File**: `app/Http/Controllers/Admin/Achievement/CertificationController.php`

**Methods**:
- `index()`: List all certifications with pagination
- `create()`: Show create form
- `store()`: Save new certification (with image upload)
- `edit($id)`: Show edit form
- `update($id)`: Update certification (with image upload)
- `destroy($id)`: Delete certification (and image file)

**Features**:
- Image upload validation (jpg, jpeg, png, max 2MB)
- Image storage in `storage/app/public/certifications/`
- Multi-language name input (tabs for EN/FR)
- Display order field
- Active/inactive toggle
- Delete confirmation

---

### 3.2 ClientController

**File**: `app/Http/Controllers/Admin/Achievement/ClientController.php`

**Methods**:
- `index()`: List all clients with pagination
- `create()`: Show create form
- `store()`: Save new client (with logo upload)
- `edit($id)`: Show edit form
- `update($id)`: Update client (with logo upload)
- `destroy($id)`: Delete client (and logo file)

**Features**:
- Logo upload validation (jpg, jpeg, png, svg, max 2MB)
- Logo storage in `storage/app/public/clients/`
- Optional website URL field
- Display order field
- Active/inactive toggle
- Delete confirmation

---

## Phase 4: Admin Views

### 4.1 Certifications Views

**Directory**: `resources/views/admin/certifications/`

#### `index.blade.php`
- Table with columns: Image (thumbnail), Name (EN/FR), Display Order, Status, Actions
- "Add Certification" button
- Edit/Delete action buttons
- Pagination
- Search functionality (optional)

#### `create.blade.php`
- Language tabs (EN/FR) for name
- Image upload field with preview
- Display order input
- Active checkbox (default: checked)
- Submit/Cancel buttons

#### `edit.blade.php`
- Same as create but pre-filled
- Show current image with option to replace
- Update/Cancel buttons

---

### 4.2 Clients Views

**Directory**: `resources/views/admin/clients/`

#### `index.blade.php`
- Table with columns: Logo (thumbnail), Name, Website, Display Order, Status, Actions
- "Add Client" button
- Edit/Delete action buttons
- Pagination
- Search functionality (optional)

#### `create.blade.php`
- Name input field
- Logo upload field with preview
- Website URL input (optional)
- Display order input
- Active checkbox (default: checked)
- Submit/Cancel buttons

#### `edit.blade.php`
- Same as create but pre-filled
- Show current logo with option to replace
- Update/Cancel buttons

---

## Phase 5: Frontend Implementation

### 5.1 Achievements Page Controller

**File**: `app/Http/Controllers/Web/AchievementController.php`

```php
<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Certification;
use App\Models\Client;

class AchievementController extends Controller
{
    public function index()
    {
        $certifications = Certification::active()->ordered()->get();
        $clients = Client::active()->ordered()->get();

        return view('web.achievements.index', compact('certifications', 'clients'));
    }
}
```

---

### 5.2 Achievements Page View

**File**: `resources/views/web/achievements/index.blade.php`

**Structure**:
```html
<x-app-layout>
    <!-- Page Header -->
    <section class="page-header section-padding">
        <div class="container">
            <h1>{{ __('achievements.title') }}</h1>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li><a href="/">Home</a></li>
                    <li class="active">Achievements</li>
                </ol>
            </nav>
        </div>
    </section>

    <!-- Certifications Section -->
    <section class="section-padding">
        <div class="container">
            <h2>{{ __('achievements.certifications_title') }}</h2>
            <div class="row stagger-animation">
                @foreach($certifications as $certification)
                    <div class="col-3 col-lg-2 mb-4 stagger-item" data-animation="zoomIn">
                        <img src="{{ Storage::url($certification->image) }}" 
                             alt="{{ $certification->name }}" 
                             class="img-fluid">
                    </div>
                @endforeach
            </div>
        </div>
    </section>

    <!-- Clients Section -->
    <section class="section-padding bg-light">
        <div class="container">
            <h2>{{ __('achievements.clients_title') }}</h2>
            <div class="row stagger-animation">
                @foreach($clients as $client)
                    <div class="col-6 col-md-3 mb-4 stagger-item" data-animation="fadeInUp">
                        @if($client->website_url)
                            <a href="{{ $client->website_url }}" target="_blank" rel="noopener">
                                <img src="{{ Storage::url($client->logo) }}" 
                                     alt="{{ $client->name }}" 
                                     class="img-fluid client-logo">
                            </a>
                        @else
                            <img src="{{ Storage::url($client->logo) }}" 
                                 alt="{{ $client->name }}" 
                                 class="img-fluid client-logo">
                        @endif
                    </div>
                @endforeach
            </div>
        </div>
    </section>
</x-app-layout>
```

---

## Phase 6: Routes

### 6.1 Admin Routes

**File**: `routes/web.php`

```php
// Admin - Achievements Management
Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    // Certifications
    Route::resource('certifications', CertificationController::class);
    
    // Clients
    Route::resource('clients', ClientController::class);
});
```

---

### 6.2 Frontend Routes

**File**: `routes/web.php`

```php
// Achievements Page
Route::get('/achievements', [AchievementController::class, 'index'])->name('achievements.index');
```

---

## Phase 7: Translations

### 7.1 English Translations

**File**: `lang/en/achievements.php`

```php
<?php

return [
    'title' => 'Our Achievements',
    'certifications_title' => 'Our Certifications',
    'clients_title' => 'Our Clients',
    'breadcrumb' => 'Achievements',
];
```

---

### 7.2 French Translations

**File**: `lang/fr/achievements.php`

```php
<?php

return [
    'title' => 'Nos Réalisations',
    'certifications_title' => 'Nos Certifications',
    'clients_title' => 'Nos Clients',
    'breadcrumb' => 'Réalisations',
];
```

---

### 7.3 Admin Translations

**Files**: `lang/en/admin.php` and `lang/fr/admin.php`

Add keys for:
- Certification management (CRUD labels)
- Client management (CRUD labels)
- Form labels and validation messages

---

## Phase 8: Admin Sidebar Menu

**File**: `resources/views/admin/layouts/sidebar.blade.php`

Add menu items:
```html
<li class="nav-item">
    <a href="#" class="nav-link" data-bs-toggle="collapse" data-bs-target="#achievements-menu">
        <i class="bi bi-trophy"></i>
        <span>Achievements</span>
    </a>
    <ul id="achievements-menu" class="collapse">
        <li><a href="{{ route('admin.certifications.index') }}">Certifications</a></li>
        <li><a href="{{ route('admin.clients.index') }}">Clients</a></li>
    </ul>
</li>
```

---

## Phase 9: Form Requests (Validation)

### 9.1 CertificationRequest

**File**: `app/Http/Requests/CertificationRequest.php`

**Validation Rules**:
- `name.en`: required, string, max:255
- `name.fr`: required, string, max:255
- `image`: required (create), image, mimes:jpg,jpeg,png, max:2048
- `display_order`: required, integer, min:0
- `is_active`: boolean

---

### 9.2 ClientRequest

**File**: `app/Http/Requests/ClientRequest.php`

**Validation Rules**:
- `name`: required, string, max:255
- `logo`: required (create), image, mimes:jpg,jpeg,png,svg, max:2048
- `website_url`: nullable, url, max:255
- `display_order`: required, integer, min:0
- `is_active`: boolean

---

## Phase 10: Styling

### 10.1 Client Logo Styling

**File**: `public/assets/css/custom.css` (or existing stylesheet)

```css
.client-logo {
    filter: grayscale(100%);
    opacity: 0.7;
    transition: all 0.3s ease;
    padding: 20px;
}

.client-logo:hover {
    filter: grayscale(0%);
    opacity: 1;
    transform: scale(1.05);
}
```

---

## Phase 11: Seeder (Optional)

### 11.1 CertificationSeeder

**File**: `database/seeders/CertificationSeeder.php`

Migrate existing certifications from `our-story` section to database.

---

### 11.2 ClientSeeder

**File**: `database/seeders/ClientSeeder.php`

Add sample clients for testing.

---

## Implementation Order

### Step 1: Database & Models
1. Create migrations for `certifications` and `clients` tables
2. Run migrations
3. Create `Certification` and `Client` models
4. Test models in tinker

### Step 2: Admin Backend
1. Create `CertificationController` and `ClientController`
2. Create form request validators
3. Test CRUD operations

### Step 3: Admin Views
1. Create admin views for certifications (index, create, edit)
2. Create admin views for clients (index, create, edit)
3. Add menu items to sidebar
4. Test admin functionality

### Step 4: Frontend
1. Create `AchievementController`
2. Create achievements page view
3. Add route
4. Add navigation link (if needed)
5. Test frontend display

### Step 5: Translations
1. Add translation files
2. Update all views with translation keys
3. Test both languages

### Step 6: Styling & Polish
1. Add custom CSS for client logos
2. Add animations (using existing animation system)
3. Test responsive design
4. Add breadcrumbs

### Step 7: Migration (Optional)
1. Create seeder to migrate existing certifications
2. Run seeder
3. Verify data

---

## File Structure Summary

```
app/
├── Http/
│   ├── Controllers/
│   │   ├── Admin/
│   │   │   └── Achievement/
│   │   │       ├── CertificationController.php
│   │   │       └── ClientController.php
│   │   └── Web/
│   │       └── AchievementController.php
│   └── Requests/
│       ├── CertificationRequest.php
│       └── ClientRequest.php
├── Models/
│   ├── Certification.php
│   └── Client.php
database/
├── migrations/
│   ├── xxxx_create_certifications_table.php
│   └── xxxx_create_clients_table.php
└── seeders/
    ├── CertificationSeeder.php
    └── ClientSeeder.php
resources/
├── views/
│   ├── admin/
│   │   ├── certifications/
│   │   │   ├── index.blade.php
│   │   │   ├── create.blade.php
│   │   │   └── edit.blade.php
│   │   └── clients/
│   │       ├── index.blade.php
│   │       ├── create.blade.php
│   │       └── edit.blade.php
│   └── web/
│       └── achievements/
│           └── index.blade.php
lang/
├── en/
│   └── achievements.php
└── fr/
    └── achievements.php
routes/
└── web.php (updated)
```

---

## Testing Checklist

### Admin Panel
- [ ] Create certification with image upload
- [ ] Edit certification and replace image
- [ ] Delete certification (image file also deleted)
- [ ] Reorder certifications by display_order
- [ ] Toggle certification active/inactive
- [ ] Create client with logo upload
- [ ] Edit client and replace logo
- [ ] Delete client (logo file also deleted)
- [ ] Reorder clients by display_order
- [ ] Toggle client active/inactive
- [ ] Add optional website URL to client
- [ ] Multi-language certification names work

### Frontend
- [ ] Achievements page displays correctly
- [ ] Certifications show in correct order
- [ ] Clients show 4 per row on desktop
- [ ] Client logos are responsive (2 per row on mobile)
- [ ] Client website links work (open in new tab)
- [ ] Animations work on scroll
- [ ] Images load correctly from storage
- [ ] Inactive items don't show
- [ ] Both languages work correctly
- [ ] Breadcrumbs work

---

## Estimated Time

- **Phase 1-2** (Database & Models): 1-2 hours
- **Phase 3** (Admin Controllers): 2-3 hours
- **Phase 4** (Admin Views): 2-3 hours
- **Phase 5** (Frontend): 1-2 hours
- **Phase 6-8** (Routes, Translations, Menu): 1 hour
- **Phase 9** (Validation): 1 hour
- **Phase 10** (Styling): 1 hour
- **Phase 11** (Seeder): 1 hour (optional)
- **Testing & Polish**: 2 hours

**Total**: 12-16 hours

---

## Notes

- Use existing animation system (animate-on-scroll, stagger-animation)
- Follow existing admin panel patterns (similar to Services/Addons)
- Use Laravel's file storage system (Storage facade)
- Implement soft deletes if needed (optional)
- Add image optimization/resizing if needed (optional)
- Consider adding pagination for admin lists if many items expected
- Client logos should be grayscale by default, color on hover
- Certifications display similar to our-story section (col-3 col-lg-2)
- Clients display 4 per row (col-6 col-md-3)

---

## Future Enhancements (Optional)

- Drag-and-drop reordering in admin panel
- Bulk actions (activate/deactivate multiple items)
- Image cropping/resizing tool
- Client categories/filtering
- Certification expiry dates
- Analytics tracking for client link clicks
- API endpoints for external use.
