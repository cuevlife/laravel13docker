<?php

namespace Tests\Feature\Admin;

use App\Models\Merchant;
use App\Models\Slip;
use App\Models\SlipBatch;
use App\Models\SlipTemplate;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SlipWorkflowTest extends TestCase
{
    use RefreshDatabase;

    public function test_workspace_users_can_create_batches_from_the_slip_registry(): void
    {
        [$user, $merchant] = $this->createWorkspaceFixture();

        $response = $this
            ->actingAs($user)
            ->withSession(['active_project_id' => $merchant->id])
            ->postJson('/workspace/slips/batches', [
                'name' => 'April Inbox',
                'note' => 'First review queue',
            ]);

        $response
            ->assertOk()
            ->assertJsonPath('batch.name', 'April Inbox');

        $this->assertDatabaseHas('slip_batches', [
            'merchant_id' => $merchant->id,
            'name' => 'April Inbox',
        ]);
    }

    public function test_workspace_users_can_filter_slips_by_batch_and_workflow_status(): void
    {
        [$user, $merchant, $template] = $this->createWorkspaceFixture(withTemplate: true);

        $reviewBatch = SlipBatch::create([
            'merchant_id' => $merchant->id,
            'created_by' => $user->id,
            'name' => 'Review Queue',
            'status' => 'open',
            'scanned_at' => now(),
        ]);

        $approvedBatch = SlipBatch::create([
            'merchant_id' => $merchant->id,
            'created_by' => $user->id,
            'name' => 'Approved Queue',
            'status' => 'open',
            'scanned_at' => now(),
        ]);

        Slip::create([
            'user_id' => $user->id,
            'slip_template_id' => $template->id,
            'slip_batch_id' => $reviewBatch->id,
            'image_path' => 'slips/reviewed.png',
            'extracted_data' => ['shop_name' => 'Review Shop', 'date' => '2026-04-01', 'total' => '100.00'],
            'workflow_status' => Slip::WORKFLOW_REVIEWED,
            'processed_at' => now()->subDay(),
            'reviewed_at' => now()->subDay(),
        ]);

        Slip::create([
            'user_id' => $user->id,
            'slip_template_id' => $template->id,
            'slip_batch_id' => $approvedBatch->id,
            'image_path' => 'slips/approved.png',
            'extracted_data' => ['shop_name' => 'Approved Shop', 'date' => '2026-04-02', 'total' => '200.00'],
            'workflow_status' => Slip::WORKFLOW_APPROVED,
            'processed_at' => now(),
            'reviewed_at' => now(),
            'approved_at' => now(),
        ]);

        $response = $this
            ->actingAs($user)
            ->withSession(['active_project_id' => $merchant->id])
            ->get('/workspace/slips?batch_id=' . $approvedBatch->id . '&workflow_status=' . Slip::WORKFLOW_APPROVED);

        $response->assertOk();
        $response->assertSee('Collection Focus');
        $response->assertSee('Approved Queue');
        $response->assertSee('Approved Shop');
        $response->assertDontSee('Review Shop');
    }

    public function test_workspace_users_can_filter_slips_by_label(): void
    {
        [$user, $merchant, $template] = $this->createWorkspaceFixture(withTemplate: true);

        Slip::create([
            'user_id' => $user->id,
            'slip_template_id' => $template->id,
            'image_path' => 'slips/priority.png',
            'extracted_data' => ['shop_name' => 'Priority Shop', 'date' => '2026-04-03', 'total' => '300.00'],
            'workflow_status' => Slip::WORKFLOW_REVIEWED,
            'processed_at' => now(),
            'reviewed_at' => now(),
            'labels' => ['priority', 'branch-a'],
        ]);

        Slip::create([
            'user_id' => $user->id,
            'slip_template_id' => $template->id,
            'image_path' => 'slips/normal.png',
            'extracted_data' => ['shop_name' => 'Normal Shop', 'date' => '2026-04-03', 'total' => '150.00'],
            'workflow_status' => Slip::WORKFLOW_REVIEWED,
            'processed_at' => now(),
            'reviewed_at' => now(),
            'labels' => ['branch-b'],
        ]);

        $response = $this
            ->actingAs($user)
            ->withSession(['active_project_id' => $merchant->id])
            ->get('/workspace/slips?label=priority');

        $response->assertOk();
        $response->assertSee('Priority Shop');
        $response->assertDontSee('Normal Shop');
    }

    public function test_workspace_users_can_bulk_update_labels_and_workflow(): void
    {
        [$user, $merchant, $template] = $this->createWorkspaceFixture(withTemplate: true);

        $slipOne = Slip::create([
            'user_id' => $user->id,
            'slip_template_id' => $template->id,
            'image_path' => 'slips/bulk-1.png',
            'extracted_data' => ['shop_name' => 'Bulk One', 'date' => '2026-04-04', 'total' => '111.00'],
            'workflow_status' => Slip::WORKFLOW_REVIEWED,
            'processed_at' => now(),
            'reviewed_at' => now(),
        ]);

        $slipTwo = Slip::create([
            'user_id' => $user->id,
            'slip_template_id' => $template->id,
            'image_path' => 'slips/bulk-2.png',
            'extracted_data' => ['shop_name' => 'Bulk Two', 'date' => '2026-04-04', 'total' => '222.00'],
            'workflow_status' => Slip::WORKFLOW_REVIEWED,
            'processed_at' => now(),
            'reviewed_at' => now(),
        ]);

        $this
            ->actingAs($user)
            ->withSession(['active_project_id' => $merchant->id])
            ->post('/workspace/slips/bulk', [
                'slip_ids' => [$slipOne->id, $slipTwo->id],
                'bulk_action' => 'add_label',
                'bulk_label' => 'priority, branch-a',
            ])
            ->assertRedirect();

        $slipOne->refresh();
        $slipTwo->refresh();

        $this->assertSame(['priority', 'branch-a'], $slipOne->labels);
        $this->assertSame(['priority', 'branch-a'], $slipTwo->labels);

        $this
            ->actingAs($user)
            ->withSession(['active_project_id' => $merchant->id])
            ->post('/workspace/slips/bulk', [
                'slip_ids' => [$slipOne->id, $slipTwo->id],
                'bulk_action' => 'mark_approved',
            ])
            ->assertRedirect();

        $slipOne->refresh();
        $slipTwo->refresh();

        $this->assertSame(Slip::WORKFLOW_APPROVED, $slipOne->workflow_status);
        $this->assertSame(Slip::WORKFLOW_APPROVED, $slipTwo->workflow_status);
        $this->assertNotNull($slipOne->approved_at);
        $this->assertNotNull($slipTwo->approved_at);
    }

    public function test_workspace_users_can_archive_and_restore_slips(): void
    {
        [$user, $merchant, $template] = $this->createWorkspaceFixture(withTemplate: true);

        $slip = Slip::create([
            'user_id' => $user->id,
            'slip_template_id' => $template->id,
            'image_path' => 'slips/archive-me.png',
            'extracted_data' => ['shop_name' => 'Archive Me', 'date' => '2026-04-01', 'total' => '55.00'],
            'workflow_status' => Slip::WORKFLOW_REVIEWED,
            'processed_at' => now(),
            'reviewed_at' => now(),
        ]);

        $archiveResponse = $this
            ->actingAs($user)
            ->withSession(['active_project_id' => $merchant->id])
            ->patch('/workspace/slips/' . $slip->id . '/archive', [
                'archive' => 1,
            ]);

        $archiveResponse->assertRedirect();
        $this->assertDatabaseHas('slips', [
            'id' => $slip->id,
            'workflow_status' => Slip::WORKFLOW_ARCHIVED,
        ]);

        $restoreResponse = $this
            ->actingAs($user)
            ->withSession(['active_project_id' => $merchant->id])
            ->patch('/workspace/slips/' . $slip->id . '/archive', [
                'archive' => 0,
            ]);

        $restoreResponse->assertRedirect();
        $this->assertDatabaseHas('slips', [
            'id' => $slip->id,
            'workflow_status' => Slip::WORKFLOW_REVIEWED,
            'archived_at' => null,
        ]);
    }

    public function test_workspace_users_can_open_export_center(): void
    {
        [$user, $merchant, $template] = $this->createWorkspaceFixture(withTemplate: true);

        $batch = SlipBatch::create([
            'merchant_id' => $merchant->id,
            'created_by' => $user->id,
            'name' => 'Export Queue',
            'status' => 'open',
            'scanned_at' => now(),
        ]);

        Slip::create([
            'user_id' => $user->id,
            'slip_template_id' => $template->id,
            'slip_batch_id' => $batch->id,
            'image_path' => 'slips/export-preview.png',
            'extracted_data' => ['shop_name' => 'Export Shop', 'date' => '2026-04-05', 'total' => '420.00'],
            'workflow_status' => Slip::WORKFLOW_APPROVED,
            'processed_at' => now(),
            'approved_at' => now(),
        ]);

        $response = $this
            ->actingAs($user)
            ->withSession(['active_project_id' => $merchant->id])
            ->get('/workspace/exports?batch_id=' . $batch->id);

        $response->assertOk();
        $response->assertSee('Export Center');
        $response->assertSee('Export Shop');
        $response->assertSee('Download Workbook');
    }

    public function test_workspace_users_can_update_collection_details(): void
    {
        [$user, $merchant] = $this->createWorkspaceFixture();

        $batch = SlipBatch::create([
            'merchant_id' => $merchant->id,
            'created_by' => $user->id,
            'name' => 'Old Collection Name',
            'note' => 'Old note',
            'status' => 'open',
            'scanned_at' => now(),
        ]);

        $response = $this
            ->actingAs($user)
            ->withSession(['active_project_id' => $merchant->id])
            ->patchJson('/workspace/slips/batches/' . $batch->id, [
                'name' => 'Updated Collection Name',
                'note' => 'Updated note for export queue',
            ]);

        $response
            ->assertOk()
            ->assertJsonPath('collection.name', 'Updated Collection Name')
            ->assertJsonPath('collection.note', 'Updated note for export queue');

        $this->assertDatabaseHas('slip_batches', [
            'id' => $batch->id,
            'name' => 'Updated Collection Name',
            'note' => 'Updated note for export queue',
        ]);
    }

    private function createWorkspaceFixture(bool $withTemplate = false): array
    {
        $user = User::factory()->create(['tokens' => 30]);
        $merchant = Merchant::create([
            'user_id' => $user->id,
            'name' => 'Slip Ops Workspace',
            'subdomain' => 'slip-ops-workspace',
            'status' => 'active',
        ]);

        $merchant->users()->syncWithoutDetaching([
            $user->id => ['role' => 'owner'],
        ]);

        if (!$withTemplate) {
            return [$user, $merchant];
        }

        $template = SlipTemplate::create([
            'user_id' => $user->id,
            'merchant_id' => $merchant->id,
            'name' => 'General Receipt',
            'main_instruction' => 'Extract fields accurately.',
            'ai_fields' => [
                ['key' => 'date', 'label' => 'Date', 'type' => 'text'],
                ['key' => 'total', 'label' => 'Total', 'type' => 'text'],
            ],
            'export_layout' => [
                ['key' => 'date', 'label' => 'Date', 'type' => 'text'],
                ['key' => 'total', 'label' => 'Total', 'type' => 'text'],
            ],
        ]);

        return [$user, $merchant, $template];
    }
}