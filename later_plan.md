# Project Plan: Fido Multi-Tenant Web Application (v10 - Final)

## 1. Project Vision & Core Requirements

- **Vision:** Create a web-based accounting and invoicing application that can serve multiple, isolated clients (tenants) from a single codebase.
- **Architecture:** A pure web-hosted application, accessible via a browser, with a dual-panel interface for administrators and tenants.
- **Database:** A robust and scalable single MySQL database.
- **Tenancy Model:** A single-database, multi-tenant architecture where each tenant's data is segregated by a `tenant_id`.
- **Localization:** The entire user interface will be in French, with a hybrid French/English naming convention for the code.

## 2. Detailed Architectural Plan

### 2.1. Dual-Panel Architecture

-   **Admin Panel (Super Admin):** For Fido administrators to manage the platform (`admin.fido.tn`).
-   **Dashboard Panel (Tenant):** The main application for tenant users (`client-a.fido.tn`).

### 2.2. Multi-Tenancy Model (filament tenancy)

-   **Strategy:** A single database with a `tenant_id` column on all tenant-specific tables.
-   **Filament Consistency:** This single-database approach is fully compatible with Filament panels, relying on model scoping and panel middleware for data isolation.
-   **User-Tenant Relationship (Filament):** The `User` model will implement the `Filament\Models\Contracts\HasTenants` interface, including `getTenants()` and `canAccessTenant()` methods, to manage user access to multiple tenants and allow switching between them.
-   **Tenant Model:** The `Tenant` model will store `type`, `features` (JSON), and `settings` (JSON).
-   **Automatic Scoping:** All tenant-specific models will use the `BelongsToTenant` trait.

### 2.3. Authentication & Routing

-   **Authentication:** Handled by Google (e.g., via Laravel Socialite) and integrated with Filament.
-   **Central Routes (`routes/web.php`):** For public pages and Super Admin login.
-   **Tenant Routes (`routes/tenant.php`):** For the main tenant application, including PDF routes.

### 2.4. Tenant-Specific Customization (PDFs)

-   **Data Storage:** Tenant branding information (`company_name`, `logo_path`, etc.) will be stored in the tenant's `settings`.
-   **Dynamic Generation:** PDFs will dynamically display the current tenant's branding.

### 2.5. Naming Conventions

-   **French Domain Code:** Models (`Facture`) and tables (`factures`).
-   **English Framework Code:** Resources (`FactureResource.php`) and views (`show.blade.php`).
-   **Exception:** `User` model and `users` table remain in English.

## 3. Step-by-Step Implementation Guide

### Phase 1: Setup
1.  **Create Project:** `laravel new fido_online`
2.  **Enter Directory:** `cd fido_online`
3.  **Initialize Git:** `git init`
4.  **Install PHP Dependencies:** `composer require laravel/framework filament/filament livewire/livewire livewire/volt barryvdh/laravel-dompdf bezhansalleh/filament-language-switch`
5.  **Install JS Dependencies:** `npm install -D vite laravel-vite-plugin tailwindcss postcss autoprefixer @tailwindcss/forms`
6.  **Configure Environment:** Edit the `.env` file to set `DB_CONNECTION=mysql`, `DB_DATABASE=fido_db`, and other necessary credentials.
7.  **Run Installers:** `php artisan tenancy:install` and then `php artisan filament:install --panels`.

### Phase 2: Core Scaffolding
1.  **Admin Panel (Super Admin) Exists:** The `Admin` panel is already created in `app/Providers/Filament/AdminPanelProvider.php`.
2.  **Create Dashboard Panel (Tenant):** `php artisan make:filament-panel Dashboard`
3.  **Create Core Migrations:**
    - `php artisan make:migration create_tenants_table` (and add `name`, `type`, `features`, `settings` columns)
    - `php artisan make:migration create_domains_table`
    - `php artisan make:migration create_tenant_user_table` (for the Tenant-User pivot relationship)
    - `php artisan make:migration add_is_super_admin_to_users_table`
3.  **Create Business Migrations:**
    - `php artisan make:migration create_clients_table`
    - `php artisan make:migration create_factures_table`
    - `php artisan make:migration create_honoraires_table`
    - `php artisan make:migration create_note_de_debits_table`
    - *In each business migration, add the required columns and ensure the `tenant_id` foreign key is present.*
4.  **Run Migrations:** `php artisan migrate`


### Phase 3: Model & Resource Implementation (Definitive Definitions)

#### 3.1. `Tenant` Model
```php
class Tenant extends BaseTenant implements TenantWithDatabase
{
    

    protected $fillable = ['name', 'type', 'features', 'settings'];
    protected $casts = ['features' => 'array', 'settings' => 'array'];

    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'tenant_user', 'tenant_id', 'user_id');
    }
}
```

#### 3.2. `User` Model & Tenant Relationship
```php
class User extends Authenticatable
{
    // ... existing User model content (fillable, hidden, casts)

    public function tenants(): BelongsToMany
    {
        return $this->belongsToMany(Tenant::class, 'tenant_user', 'user_id', 'tenant_id');
    }
}
```

#### 3.3. `Client` Model & Resource
- **Model:**
  ```php
  class Client extends Model {
      
      protected $fillable = ['nom', 'adresse', 'ville', 'code_postal', 'pays', 'email', 'telephone', 'identifiant_fiscal'];
  }
  ```
- **Resource:**
  - **Form:** Use `TextInput` for all fields. Group address fields in a `Card`.
  - **Table:** Display `nom`, `email`, `telephone` as searchable `TextColumn`s.

#### 3.4. `Facture` Model & Resource
- **Model:**
  ```php
  class Facture extends Model {
      
      protected $fillable = ['client_id', 'numero', 'date_emission', 'date_echeance', 'montant_ht', 'montant_tva', 'montant_ttc', 'status'];
      public function client(): BelongsTo { return $this->belongsTo(Client::class); }
  }
  ```
- **Resource:**
  - **Form:** Use `Select` with `relationship('client', 'nom')`, `DatePicker`, `TextInput`, and a `Select` for `status`.
  - **Table:** Display `numero`, `client.nom`, `date_emission`, `status`, `montant_ttc`.
  - **Relations:** `FactureItemRelationManager`.

#### 3.5. `Honoraire` Model & Resource
- **Model:**
  ```php
  class Honoraire extends Model {
      
      protected $fillable = ['client_id', 'numero', 'date', 'montant_ht', 'tva', 'retenue_source', 'montant_net'];
      public function client(): BelongsTo { return $this->belongsTo(Client::class); }
  }
  ```
- **Resource:**
  - **Form:** Use `Select` for `client_id`, `DatePicker` for `date`, and `TextInput` with `numeric()` for amounts.
  - **Table:** Display `numero`, `client.nom`, `date`, `montant_net`.

#### 3.6. `NoteDeDebit` Model & Resource
- **Model:**
  ```php
  class NoteDeDebit extends Model {
      
      protected $fillable = ['client_id', 'numero', 'date', 'montant', 'description'];
      public function client(): BelongsTo { return $this->belongsTo(Client::class); }
  }
  ```
- **Resource:**
  - **Form:** Use `Select` for `client_id`, `DatePicker` for `date`, `TextInput` for `numero`, `RichEditor` for `description`, `TextInput` for `montant`.
  - **Table:** Display `numero`, `client.nom`, `date`, `montant`.

### Phase 4: Business Logic & Customization

#### 4.1. Self-Service Tenant Creation & Invitation System
1.  **New User Self-Registration with Tenant Creation:**
    *   Modify the user registration process to allow new users (without an invitation) to create a new tenant.
    *   The user will provide a tenant name and select a tenant type (e.g., "commercial" or "accounting").
    *   The registering user will automatically become the first administrator of this new tenant.
    *   Implement logic to provision the new `Tenant` record and associate the user.
2.  **Invitation-Based User Registration:**
    *   Implement an invitation system allowing existing tenant users (with appropriate permissions) to invite new users to their specific tenant.
    *   An invitation link will contain a unique token.
    *   Upon registration via an invitation link, the new user will be automatically associated with the inviting tenant.
    *   Create an `invitations` table to store invitation details (token, invited email, tenant_id, inviter_id, status).

#### 4.2. Other Business Logic & Customization
1.  **Tenant Creation Logic:** Implement logic to pre-populate the `features` JSON array based on the chosen tenant `type`.
2.  **Feature Gating:** In `TenantPanelProvider`, use `tenant()->features` to conditionally register resources.
3.  **Settings Page (`app/Filament/Tenant/Pages/Settings.php`):
    -   **File Creation:** `php artisan make:filament-page Settings`.
    -   **Form:** Build a form with `TextInput` for `company_name`, `tax_id`, etc., and `FileUpload` for the logo.
    -   **Logic:** Use `mount()` to populate the form from `tenant()->settings` and `submit()` to save the data back.
4.  **Configure Tenant Panel for Multi-Tenancy:**
    - In `app/Providers/Filament/TenantPanelProvider.php`, add the tenancy middleware to the panel configuration:
    ```php
    ->tenantMiddleware([
        
    ])
    ```
    - In `config/tenancy.php`, ensure the central domains are set:
    ```php
    'central_domains' => [
        'fido-online.test', // your main app domain
    ],
    ```
5.  **PDF Controllers:** Create controllers that fetch branding data from `tenant()->settings` to pass to PDF Blade views.

### Phase 5: Finalization
1.  **Localization:** Translate all UI strings to French.
2.  **Testing:** Thoroughly test data isolation, feature access, and PDF branding for both tenant types.
3.  **Deployment:** Deploy to a standard web server with MySQL.

## Operational Mandates

- **Dependency Verification:** Before executing any installation or setup command, always verify which dependencies are already installed (e.g., by checking `composer.json` or `package.json`) to avoid unnecessary re-installation or conflicts.
- **Task Completion:** After completing a task, check `to-do.md` and mark the corresponding task as done if it exists.