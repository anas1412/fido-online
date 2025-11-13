# Fido Online: Laravel + Filament Multi-Tenant App

## Project Description

Fido Online is a multi-tenant application built with Laravel and Filament, designed to provide a robust platform with Google-only authentication and a flexible tenant management system. It features separate admin and user dashboards, a smooth onboarding process for new users, and an invite system for tenant collaboration.

## Tech Stack

*   **Laravel Framework:** 12.37.0
*   **PHP:** 8.3.24
*   **Filament Admin Panel Framework:** v4.1
*   **Database:** MySQL (single database for all tenants)
*   **Authentication:** Google-only (via Socialite)
*   **Multi-tenancy:** Handled with a many-to-many relationship between users and tenants, utilizing Filament's `HasTenants` contract and global query scopes.

## Architecture

1.  **Two Filament Panels:**
    *   **Admin Panel:** For system administrators to manage tenants, users, and invites.
    *   **User Dashboard Panel:** For normal users, with data restricted to their assigned tenant(s).

2.  **Tenant Management:**
    *   `tenants` table stores tenant information, including `type` (`accounting` or `commercial`).
    *   A `tenant_user` pivot table manages the many-to-many relationship between users and tenants.
    *   The `User` model implements `Filament\Models\Contracts\HasTenants` for automatic tenant-level data access enforcement.

3.  **Google-only Login:**
    *   Users authenticate exclusively via Google OAuth.
    *   Customized Filament login view to show only "Login with Google".

4.  **Tenant Onboarding Flow:**
    *   Utilizes Filament's built-in `tenantRegistration()` feature.
    *   After login, users without an assigned tenant are redirected to a page to create a new tenant or join an existing one.

5.  **Invite System:**
    *   Admins generate unique per-user invite codes.
    *   `tenant_invites` table stores codes with a `used_by` field.
    *   Invite links (`/invite/{code}`) automatically assign users to a tenant after login, ensuring one invite per user.

## Key Features

*   Automatic tenant context applied to all tenant-owned data.
*   Admin panel provides super-admin access without tenant restrictions.
*   Seamless onboarding for users via dynamic tenant creation or invite joining.
*   Clear separation of admin and tenant user interfaces.
*   Tenant types (`accounting` or `commercial`) enable role-specific dashboards and features.

## Installation

To get Fido Online up and running on your local machine, follow these steps:

1.  **Clone the repository:**
    ```bash
    git clone <repository_url>
    cd fido-online
    ```

2.  **Install PHP dependencies:**
    ```bash
    composer install
    ```

3.  **Install Node.js dependencies:**
    ```bash
    npm install
    ```

4.  **Copy the environment file:**
    ```bash
    cp .env.example .env
    ```

5.  **Generate an application key:**
    ```bash
    php artisan key:generate
    ```

6.  **Configure your database:**
    Edit the `.env` file and set your MySQL database credentials.

7.  **Run database migrations:**
    ```bash
    php artisan migrate
    ```

8.  **Set up Google OAuth:**
    *   Create a Google OAuth client ID and secret in the Google API Console.
    *   Add `GOOGLE_CLIENT_ID` and `GOOGLE_CLIENT_SECRET` to your `.env` file.
    *   Ensure your Google OAuth redirect URI is correctly configured to `YOUR_APP_URL/oauth/google/callback`.

9.  **Serve the application:**
    ```bash
    php artisan serve
    ```
    The application will typically be available at `http://127.0.0.1:8000`.

## Usage

*   **Admin Panel:** Access the admin panel at `/admin` (after creating an admin user manually or via a seeder).
*   **User Dashboard Panel:** Users will be redirected to their dashboard after logging in with Google.

## Contributing

Contributions are welcome! Please feel free to fork the repository, make your changes, and submit a pull request.

## License

The Laravel framework is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).