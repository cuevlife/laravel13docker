<?php

namespace Tests\Feature\Auth;

use App\Models\Merchant;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class InternalAccessTest extends TestCase
{
    use RefreshDatabase;

    public function test_authenticated_internal_users_can_access_the_folder_selector_without_email_verification(): void
    {
        $user = User::factory()->unverified()->create();

        $response = $this->actingAs($user)->get('/dashboard');

        $response->assertOk();
    }

    public function test_single_workspace_users_still_see_the_folder_selector_in_ip_mode(): void
    {
        $user = User::factory()->create();
        $merchant = Merchant::create([
            'user_id' => $user->id,
            'name' => 'LAN Workspace',
            'subdomain' => 'lan-workspace',
            'status' => 'active',
        ]);

        $merchant->users()->syncWithoutDetaching([
            $user->id => ['role' => 'owner'],
        ]);

        $response = $this
            ->actingAs($user)
            ->withServerVariables(['HTTP_HOST' => '192.168.9.113'])
            ->get('http://192.168.9.113/dashboard');

        $response->assertOk();
        $response->assertSee('Folder Hub');
        $response->assertSee('LAN Workspace');
    }

    public function test_users_can_open_a_folder_and_access_workspace_routes_in_ip_mode(): void
    {
        $user = User::factory()->create([
            'tokens' => 25,
        ]);
        $merchant = Merchant::create([
            'user_id' => $user->id,
            'name' => 'LAN Workspace',
            'subdomain' => 'lan-workspace',
            'status' => 'active',
        ]);

        $merchant->users()->syncWithoutDetaching([
            $user->id => ['role' => 'owner'],
        ]);

        $openResponse = $this
            ->actingAs($user)
            ->withServerVariables(['HTTP_HOST' => '192.168.9.113'])
            ->get('http://192.168.9.113/folders/open/' . $merchant->id);

        $openResponse->assertRedirect('http://192.168.9.113/workspace/slips');

        $workspaceResponse = $this
            ->actingAs($user)
            ->withSession(['active_folder_id' => $merchant->id])
            ->withServerVariables(['HTTP_HOST' => '192.168.9.113'])
            ->get('http://192.168.9.113/workspace/slips');

        $workspaceResponse->assertOk();
    }

    public function test_folder_owner_can_delete_a_folder_after_confirming_the_name(): void
    {
        $user = User::factory()->create();
        $merchant = Merchant::create([
            'user_id' => $user->id,
            'name' => 'Delete Me',
            'subdomain' => 'delete-me',
            'status' => 'active',
        ]);

        $merchant->users()->syncWithoutDetaching([
            $user->id => ['role' => 'owner'],
        ]);

        $response = $this
            ->actingAs($user)
            ->deleteJson('/folders/' . $merchant->id, [
                'confirmation' => 'Delete Me',
            ]);

        $response->assertOk();
        $this->assertDatabaseMissing('merchants', [
            'id' => $merchant->id,
        ]);
    }

    public function test_non_owner_cannot_delete_a_folder_even_with_the_correct_name(): void
    {
        $owner = User::factory()->create();
        $member = User::factory()->create();
        $merchant = Merchant::create([
            'user_id' => $owner->id,
            'name' => 'Protected Folder',
            'subdomain' => 'protected-folder',
            'status' => 'active',
        ]);

        $merchant->users()->syncWithoutDetaching([
            $owner->id => ['role' => 'owner'],
            $member->id => ['role' => 'employee'],
        ]);

        $response = $this
            ->actingAs($member)
            ->deleteJson('/folders/' . $merchant->id, [
                'confirmation' => 'Protected Folder',
            ]);

        $response->assertForbidden();
        $this->assertDatabaseHas('merchants', [
            'id' => $merchant->id,
        ]);
    }
}