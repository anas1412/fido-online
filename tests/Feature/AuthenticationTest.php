<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Tenant;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use Laravel\Socialite\Facades\Socialite;
use Laravel\Socialite\Two\User as SocialiteUser;
use Mockery;

class AuthenticationTest extends TestCase
{
    use RefreshDatabase;

    protected Tenant $tenant;

    protected function setUp(): void
    {
        parent::setUp();

        // Ensure a tenant exists for users to be associated with
        $this->tenant = Tenant::factory()->create([
            'name' => 'Test Company',
            'slug' => 'test-company',
            'type' => 'accounting',
        ]);
    }

    /** @test */
    public function new_user_is_redirected_to_tenant_registration_after_google_login()
    {
        $abstractUser = Mockery::mock(SocialiteUser::class);
        $abstractUser->shouldReceive('getId')->andReturn(uniqid());
        $abstractUser->shouldReceive('getName')->andReturn('John Doe');
        $abstractUser->shouldReceive('getEmail')->andReturn('john@example.com');
        $abstractUser->shouldReceive('getAvatar')->andReturn('https://example.com/avatar.jpg');

        Socialite::shouldReceive('driver')->with('google')->andReturnSelf();
        Socialite::shouldReceive('user')->andReturn($abstractUser);

        $response = $this->get(route('socialite.google.callback'));

        $this->assertAuthenticated();
        $response->assertRedirect(route('filament.dashboard.tenant-registration'));
        $this->assertDatabaseHas('users', [
            'email' => 'john@example.com',
        ]);
    }

    /** @test */
    public function existing_user_with_tenant_is_redirected_to_dashboard_after_google_login()
    {
        $existingUser = User::factory()->create([
            'email' => 'existing@example.com',
        ]);
        $existingUser->tenants()->attach($this->tenant);

        $abstractUser = Mockery::mock(SocialiteUser::class);
        $abstractUser->shouldReceive('getId')->andReturn(uniqid());
        $abstractUser->shouldReceive('getName')->andReturn('Existing User');
        $abstractUser->shouldReceive('getEmail')->andReturn('existing@example.com');
        $abstractUser->shouldReceive('getAvatar')->andReturn('https://example.com/avatar.jpg');

        Socialite::shouldReceive('driver')->with('google')->andReturnSelf();
        Socialite::shouldReceive('user')->andReturn($abstractUser);

        $response = $this->get(route('socialite.google.callback'));

        $this->assertAuthenticatedAs($existingUser);
        $response->assertRedirect(route('filament.dashboard.dashboard'));
    }

    /** @test */
    public function user_cannot_login_with_email_and_password()
    {
        $user = User::factory()->create();
        $user->tenants()->attach($this->tenant);

        $response = $this->post('/login', [
            'email' => $user->email,
            'password' => 'password',
        ]);

        $this->assertGuest();
        $response->assertSessionHasErrors('email'); // Or appropriate error message
    }

    /** @test */
    public function admin_can_access_admin_panel()
    {
        $admin = User::factory()->create(['is_admin' => true]);
        $admin->tenants()->attach($this->tenant);

        $response = $this->actingAs($admin)->get(route('filament.admin.dashboard')); // Updated route

        $response->assertOk();
    }

    /** @test */
    public function regular_user_cannot_access_admin_panel()
    {
        $user = User::factory()->create(['is_admin' => false]);
        $user->tenants()->attach($this->tenant);

        $response = $this->actingAs($user)->get(route('filament.admin.dashboard')); // Updated route

        $response->assertForbidden(); // Or appropriate redirect/status code
    }

    /** @test */
    public function regular_user_can_access_dashboard_panel()
    {
        $user = User::factory()->create(['is_admin' => false]);
        $user->tenants()->attach($this->tenant);

        $response = $this->actingAs($user)->get(route('filament.dashboard.dashboard')); // Updated route

        $response->assertOk();
    }
}
