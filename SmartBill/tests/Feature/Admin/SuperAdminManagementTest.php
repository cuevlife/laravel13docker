<?php

namespace Tests\Feature\Admin;

use App\Models\Merchant;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SuperAdminManagementTest extends TestCase
{
    use RefreshDatabase;

    private function adminUrl(string $path = ''): string
    {
        return 'http://admin.localhost' . $path;
    }

    public function test_super_admin_dashboard_can_be_rendered(): void
    {
        $superAdmin = User::factory()->create([
            'role' => User::ROLE_SUPER_ADMIN,
        ]);

        $response = $this
            ->actingAs($superAdmin)
            ->withServerVariables(['HTTP_HOST' => 'admin.localhost'])
            ->get($this->adminUrl('/dashboard'));

        $response->assertOk();
    }

    public function test_super_admin_dashboard_can_be_rendered_in_path_mode(): void
    {
        $superAdmin = User::factory()->create([
            'role' => User::ROLE_SUPER_ADMIN,
        ]);

        $response = $this
            ->actingAs($superAdmin)
            ->withServerVariables(['HTTP_HOST' => '192.168.9.113'])
            ->get('http://192.168.9.113/admin/dashboard');

        $response->assertOk();
    }

    public function test_super_admin_can_create_user_from_admin_console(): void
    {
        $superAdmin = User::factory()->create([
            'role' => User::ROLE_SUPER_ADMIN,
        ]);

        $response = $this
            ->actingAs($superAdmin)
            ->withServerVariables(['HTTP_HOST' => 'admin.localhost'])
            ->post($this->adminUrl('/users'), [
                'name' => 'Portal Manager',
                'username' => 'portal.manager',
                'email' => 'portal.manager@example.com',
                'password' => 'password123',
                'password_confirmation' => 'password123',
                'role' => User::ROLE_TENANT_ADMIN,
                'tokens' => 75,
            ]);

        $response->assertSessionHasNoErrors();
        $response->assertRedirect();

        $this->assertDatabaseHas('users', [
            'username' => 'portal.manager',
            'email' => 'portal.manager@example.com',
            'role' => User::ROLE_TENANT_ADMIN,
            'tokens' => 75,
        ]);
    }

    public function test_super_admin_can_create_project_and_assign_owner(): void
    {
        $superAdmin = User::factory()->create([
            'role' => User::ROLE_SUPER_ADMIN,
        ]);

        $owner = User::factory()->create();

        $response = $this
            ->actingAs($superAdmin)
            ->withServerVariables(['HTTP_HOST' => 'admin.localhost'])
            ->post($this->adminUrl('/projects'), [
                'name' => 'Acme Holdings',
                'user_id' => $owner->id,
                'address' => 'Bangkok HQ',
                'tax_id' => 'ACME-001',
                'phone' => '0800000000',
            ]);

        $response->assertSessionHasNoErrors();
        $response->assertRedirect();

        $merchant = Merchant::query()->firstWhere('name', 'Acme Holdings');

        $this->assertNotNull($merchant);
        $this->assertSame($owner->id, $merchant->user_id);
        $this->assertDatabaseHas('merchant_user', [
            'merchant_id' => $merchant->id,
            'user_id' => $owner->id,
            'role' => 'owner',
        ]);
    }

    public function test_non_super_admin_cannot_access_admin_dashboard(): void
    {
        $user = User::factory()->create([
            'role' => User::ROLE_USER,
        ]);

        $response = $this
            ->actingAs($user)
            ->withServerVariables(['HTTP_HOST' => 'admin.localhost'])
            ->get($this->adminUrl('/dashboard'));

        $response->assertForbidden();
    }

    public function test_non_super_admin_cannot_access_admin_dashboard_in_path_mode(): void
    {
        $user = User::factory()->create([
            'role' => User::ROLE_USER,
        ]);

        $response = $this
            ->actingAs($user)
            ->withServerVariables(['HTTP_HOST' => '192.168.9.113'])
            ->get('http://192.168.9.113/admin/dashboard');

        $response->assertForbidden();
    }

    public function test_super_admin_can_set_user_token_balance(): void
    {
        $superAdmin = User::factory()->create([
            'role' => User::ROLE_SUPER_ADMIN,
        ]);
        $managedUser = User::factory()->create([
            'tokens' => 20,
        ]);

        $response = $this
            ->actingAs($superAdmin)
            ->withServerVariables(['HTTP_HOST' => 'admin.localhost'])
            ->post($this->adminUrl('/users/' . $managedUser->id . '/tokens'), [
                'operation' => 'set',
                'tokens' => 150,
                'note' => 'Stabilize launch balance',
            ]);

        $response->assertSessionHasNoErrors();
        $this->assertDatabaseHas('users', [
            'id' => $managedUser->id,
            'tokens' => 150,
        ]);
        $this->assertDatabaseHas('token_logs', [
            'user_id' => $managedUser->id,
            'delta' => 130,
            'type' => 'manual_settlement',
            'description' => 'Stabilize launch balance',
        ]);
    }

    public function test_super_admin_can_deduct_user_tokens(): void
    {
        $superAdmin = User::factory()->create([
            'role' => User::ROLE_SUPER_ADMIN,
        ]);
        $managedUser = User::factory()->create([
            'tokens' => 90,
        ]);

        $response = $this
            ->actingAs($superAdmin)
            ->withServerVariables(['HTTP_HOST' => 'admin.localhost'])
            ->post($this->adminUrl('/users/' . $managedUser->id . '/tokens'), [
                'operation' => 'deduct',
                'tokens' => 30,
            ]);

        $response->assertSessionHasNoErrors();
        $this->assertDatabaseHas('users', [
            'id' => $managedUser->id,
            'tokens' => 60,
        ]);
        $this->assertDatabaseHas('token_logs', [
            'user_id' => $managedUser->id,
            'delta' => -30,
            'type' => 'manual_debit',
        ]);
    }

    public function test_super_admin_can_suspend_user(): void
    {
        $superAdmin = User::factory()->create([
            'role' => User::ROLE_SUPER_ADMIN,
        ]);
        $managedUser = User::factory()->create([
            'status' => 'active',
        ]);

        $response = $this
            ->actingAs($superAdmin)
            ->withServerVariables(['HTTP_HOST' => 'admin.localhost'])
            ->patch($this->adminUrl('/users/' . $managedUser->id . '/status'), [
                'status' => 'suspended',
            ]);

        $response->assertSessionHasNoErrors();
        $this->assertDatabaseHas('users', [
            'id' => $managedUser->id,
            'status' => 'suspended',
        ]);
    }

    public function test_super_admin_can_archive_project(): void
    {
        $superAdmin = User::factory()->create([
            'role' => User::ROLE_SUPER_ADMIN,
        ]);
        $project = Merchant::create([
            'name' => 'Archive Me',
            'subdomain' => 'archive-me',
            'status' => 'active',
        ]);

        $response = $this
            ->actingAs($superAdmin)
            ->withServerVariables(['HTTP_HOST' => 'admin.localhost'])
            ->patch($this->adminUrl('/projects/' . $project->id . '/status'), [
                'status' => 'archived',
            ]);

        $response->assertSessionHasNoErrors();
        $this->assertDatabaseHas('merchants', [
            'id' => $project->id,
            'status' => 'archived',
        ]);
    }

    public function test_archived_project_blocks_regular_workspace_access(): void
    {
        $user = User::factory()->create();
        $project = Merchant::create([
            'user_id' => $user->id,
            'name' => 'Blocked Workspace',
            'subdomain' => 'blocked-workspace',
            'status' => 'archived',
        ]);

        $project->users()->syncWithoutDetaching([
            $user->id => ['role' => 'owner'],
        ]);

        $response = $this
            ->actingAs($user)
            ->withServerVariables(['HTTP_HOST' => '192.168.9.113'])
            ->get('http://192.168.9.113/projects/open/' . $project->id);

        $response->assertStatus(423);
    }
}
