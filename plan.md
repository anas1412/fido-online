# Plan for Filament Multi-Tenancy Onboarding

The goal is to implement Filament's multi-tenancy features, specifically focusing on the `HasTenants` contract and the `tenantRegistration()` feature for onboarding, while integrating with the existing Google-only authentication and invite system.

## Phase 1: Core Multi-Tenancy Setup

1.  **Implement `HasTenants` Interface on `User` Model:**
    *   Modify `app/Models/User.php` to implement `Filament\Models\Contracts\HasTenants`.
    *   Add the `tenants()` many-to-many relationship to `User` model, linking to `Tenant` via the `tenant_user` pivot table.
    *   Implement `getTenants()` method in `User` to return the collection of associated tenants.
    *   Implement `canAccessTenant()` method in `User` to control tenant access.

2.  **Configure Filament Dashboard Panel for Tenancy:**
    *   In `app/Providers/Filament/DashboardPanelProvider.php`, configure the panel to use the `Tenant` model and enable `tenantRegistration()`. The page specified for `tenantRegistration()` will be a custom page that offers both tenant creation and joining options.

3.  **Define Tenant Model and Relationship:**
    *   Ensure `app/Models/Tenant.php` exists with necessary fields (e.g., `name`, `type`).
    *   Define the `users()` many-to-many relationship in `Tenant` model, linking to `User`.

4.  **Global Query Scopes (Verification/Implementation):**
    *   Review models that store tenant-specific data. Filament's `HasTenants` often handles this, but verify or implement global query scopes/tenant-aware middleware for automatic data filtering.

## Phase 2: Onboarding Flow Integration

1.  **Customize `tenantRegistration()` Page (Combined Create/Join):**
    *   Filament will redirect users without a tenant to a custom registration page (e.g., `RegisterTenant.php`).
    *   This page will be a standard `Filament\Pages\Page` (not extending `Filament\Pages\Tenancy\RegisterTenant`).
    *   It will implement `HasForms` and `HasActions`.
    *   It will present two distinct options to the user:
        *   **Create a new tenant:** A form to create a `Tenant` record and associate the user.
        *   **Join existing tenant via invite code/link:** A form field for an invite code to join an existing tenant.
    *   The page's `mount()` method will handle automatic processing of invite codes stored in the session.

2.  **Invite System Integration:**
    *   **Invite Code Redemption:** Implement logic to validate invite codes from `tenant_invites` table. If valid and unused, associate the user with the tenant and mark the invite as used.
    *   **Invite Link Handling:** Create a route (`/invite/{code}`) that stores the code in the session, redirects to Google login if unauthenticated, and processes the invite after successful login (handled by the `tenantRegistration()` page's `mount()` method).

## Phase 3: Google-only Authentication Alignment

1.  **Post-Login Redirection:**
    *   Ensure that after Google login, users without associated tenants are correctly redirected to the Filament `tenantRegistration()` page.

## Phase 4: Testing and Refinement

1.  **Unit/Feature Tests:**
    *   Write tests for tenant creation, user-tenant association, invite code redemption, and invite link functionality.
    *   Test `canAccessTenant()` method.
2.  **User Acceptance Testing:**
    *   Manually test the entire onboarding flow for new and existing users, including invite links.
3.  **Security Review:**
    *   Verify global scopes prevent cross-tenant data access.
    *   Ensure `scopedUnique()` and `scopedExists()` are used for validation where appropriate.