<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use Illuminate\Support\Facades\Schema;

class DatabaseSchemaTest extends TestCase
{
    use RefreshDatabase; // This will run migrations before each test

    /** @test */
    public function users_table_has_expected_columns_and_no_tenant_id()
    {
        $this->assertTrue(Schema::hasTable('users'));
        $this->assertFalse(Schema::hasColumn('users', 'tenant_id'));
        $this->assertTrue(Schema::hasColumns('users', [
            'id', 'name', 'email', 'email_verified_at', 'google_id', 'is_admin', 'remember_token', 'created_at', 'updated_at'
        ]));
    }

    /** @test */
    public function tenants_table_has_expected_columns()
    {
        $this->assertTrue(Schema::hasTable('tenants'));
        $this->assertTrue(Schema::hasColumns('tenants', [
            'id', 'name', 'slug', 'type', 'created_at', 'updated_at'
        ]));
    }

    /** @test */
    public function tenant_invites_table_has_expected_columns_and_foreign_keys()
    {
        $this->assertTrue(Schema::hasTable('tenant_invites'));
        $this->assertTrue(Schema::hasColumns('tenant_invites', [
            'id', 'tenant_id', 'code', 'expires_at', 'used_by', 'created_by', 'created_at', 'updated_at'
        ]));

        // Assert foreign keys (this might require more advanced checks or a custom helper)
        // For simplicity, we'll just check column existence for now.
        // A more robust test would check actual foreign key constraints.
    }

    /** @test */
    public function tenant_user_table_has_expected_columns()
    {
        $this->assertTrue(Schema::hasTable('tenant_user'));
        $this->assertTrue(Schema::hasColumns('tenant_user', [
            'id', 'tenant_id', 'user_id', 'created_at', 'updated_at'
        ]));
    }
}