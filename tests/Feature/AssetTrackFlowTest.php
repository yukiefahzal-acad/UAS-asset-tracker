<?php

namespace Tests\Feature;

use App\Models\Asset;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AssetTrackFlowTest extends TestCase
{
    use RefreshDatabase;

    protected User $superAdmin;

    protected function setUp(): void
    {
        parent::setUp();

        $this->superAdmin = User::create([
            'name' => 'sadmin',
            'email' => 'sadmin@assettrack.com',
            'password' => Hash::make('yukisadmin'),
            'role' => 'superadmin',
            'status' => 'approved',
        ]);
    }

    /** Test Super Admin Login with username and password */
    public function test_super_admin_can_login_with_username_and_password()
    {
        $response = $this->post(route('login'), [
            'login' => 'sadmin',
            'password' => 'yukisadmin',
        ]);

        $response->assertRedirect(route('assets.index'));
        $this->assertAuthenticatedAs($this->superAdmin);
    }

    /** Test Other Admin Registration and Pending Status */
    public function test_new_admin_registers_and_gets_pending_status()
    {
        $response = $this->post(route('register'), [
            'name' => 'Alex Pratama',
            'email' => 'alex@company.com',
            'password' => 'secret123',
            'password_confirmation' => 'secret123',
        ]);

        $response->assertRedirect(route('login'));
        $response->assertSessionHas('warning');

        $this->assertDatabaseHas('users', [
            'name' => 'Alex Pratama',
            'email' => 'alex@company.com',
            'status' => 'pending',
        ]);

        // Attempting to login while pending fails
        $loginAttempt = $this->post(route('login'), [
            'login' => 'alex@company.com',
            'password' => 'secret123',
        ]);
        $loginAttempt->assertSessionHasErrors('login');
        $this->assertGuest();
    }

    /** Test Super Admin Approving Pending Admin */
    public function test_super_admin_can_approve_pending_admin()
    {
        $pendingUser = User::create([
            'name' => 'Anex Santoso',
            'email' => 'anex@company.com',
            'password' => Hash::make('secret123'),
            'role' => 'admin',
            'status' => 'pending',
        ]);

        $this->actingAs($this->superAdmin);

        $response = $this->post(route('admin.users.approve', $pendingUser->id));
        $response->assertRedirect(route('admin.users.index'));

        $pendingUser->refresh();
        $this->assertEquals('approved', $pendingUser->status);
        $this->assertEquals('sadmin', $pendingUser->approved_by);

        // Now approved admin can log in
        $this->post(route('logout'));
        $loginSuccess = $this->post(route('login'), [
            'login' => 'anex@company.com',
            'password' => 'secret123',
        ]);
        $loginSuccess->assertRedirect(route('assets.index'));
        $this->assertAuthenticatedAs($pendingUser);
    }

    /** [AT-101] Test Asset registration and auto UUID */
    public function test_can_register_new_asset_with_auto_uuid()
    {
        $this->actingAs($this->superAdmin);

        $response = $this->post(route('assets.store'), [
            'name' => 'Lenovo ThinkPad X1 Carbon',
            'category' => 'Elektronik & Komputer',
            'location' => 'Ruang IT',
            'purchase_price' => 25000000,
            'purchase_date' => '2026-01-01',
            'notes' => 'Testing registration flow',
        ]);

        $this->assertDatabaseHas('assets', [
            'name' => 'Lenovo ThinkPad X1 Carbon',
            'status' => 'available',
        ]);

        $asset = Asset::where('name', 'Lenovo ThinkPad X1 Carbon')->first();
        $this->assertNotNull($asset->uuid);
        $this->assertNotNull($asset->code);

        $response->assertRedirect(route('assets.show', $asset->id));
    }

    /** Test Asset Update / Edit Details */
    public function test_admin_can_update_asset_details()
    {
        $this->actingAs($this->superAdmin);

        $asset = Asset::create([
            'name' => 'Monitor Dell 24"',
            'category' => 'Elektronik & Komputer',
            'location' => 'Lantai 1',
            'purchase_price' => 3000000,
            'purchase_date' => '2026-01-01',
            'status' => 'available',
        ]);

        $response = $this->put(route('assets.update', $asset->id), [
            'name' => 'Monitor Dell 27" 4K',
            'category' => 'Elektronik & Komputer',
            'location' => 'Lantai 2 Lab Komputer',
            'purchase_price' => 5500000,
            'purchase_date' => '2026-01-01',
            'status' => 'available',
            'notes' => 'Upgrade monitor display',
        ]);

        $response->assertRedirect(route('assets.show', $asset->id));

        $asset->refresh();
        $this->assertEquals('Monitor Dell 27" 4K', $asset->name);
        $this->assertEquals('Lantai 2 Lab Komputer', $asset->location);
        $this->assertDatabaseHas('asset_logs', [
            'asset_id' => $asset->id,
            'action' => 'updated',
            'actor_name' => 'sadmin',
        ]);
    }

    /** Test Pagination 10 items per page */
    public function test_assets_table_is_paginated_to_10_items_per_page()
    {
        $this->actingAs($this->superAdmin);

        for ($i = 1; $i <= 15; $i++) {
            Asset::create([
                'name' => "Asset Unit {$i}",
                'category' => 'Elektronik & Komputer',
                'location' => 'Gudang',
                'purchase_price' => 1000000,
                'purchase_date' => '2026-01-01',
                'status' => 'available',
            ]);
        }

        $response = $this->get(route('assets.index'));
        $response->assertStatus(200);
        $response->assertViewHas('assets', function ($paginatedAssets) {
            return $paginatedAssets->perPage() === 10 && $paginatedAssets->total() === 15;
        });
    }

    /** [AT-104 Flow 2] Test borrowing approval */
    public function test_admin_approves_borrowing()
    {
        $this->actingAs($this->superAdmin);

        $asset = Asset::create([
            'name' => 'Projector Meeting',
            'category' => 'Peralatan Kantor',
            'location' => 'Meeting Room A',
            'purchase_price' => 5000000,
            'purchase_date' => '2026-02-01',
            'status' => 'available',
        ]);

        $response = $this->post(route('borrowings.borrow', $asset->uuid), [
            'borrower_name' => 'Siti Nurhaliza',
            'notes' => 'Presentasi Q1',
        ]);

        $response->assertRedirect(route('assets.show', $asset->id));

        $asset->refresh();
        $this->assertEquals('borrowed', $asset->status);
        $this->assertEquals('Siti Nurhaliza', $asset->borrowed_by);
        $this->assertEquals('sadmin', $asset->approved_by);
    }

    /** [AT-104 Flow 2] Test asset return acceptance */
    public function test_admin_accepts_return()
    {
        $this->actingAs($this->superAdmin);

        $asset = Asset::create([
            'name' => 'Microphone Wireless',
            'category' => 'Elektronik & Komputer',
            'location' => 'Auditorium',
            'purchase_price' => 2000000,
            'purchase_date' => '2026-02-01',
            'status' => 'borrowed',
            'borrowed_by' => 'Joko',
            'approved_by' => 'sadmin',
            'borrowed_at' => now(),
        ]);

        $response = $this->post(route('borrowings.return', $asset->uuid), [
            'returner_name' => 'Joko',
            'condition' => 'good',
            'notes' => 'Sudah selesai dipakai.',
        ]);

        $asset->refresh();
        $this->assertEquals('available', $asset->status);
        $this->assertNull($asset->borrowed_by);
        $this->assertNull($asset->approved_by);

        $this->assertDatabaseHas('asset_logs', [
            'asset_id' => $asset->id,
            'action' => 'returned',
            'admin_name' => 'sadmin',
        ]);
    }

    /** [AT-101 Flow 3 & 4] Test Soft Deletes and Financial Reporting integrity */
    public function test_soft_deletes_and_financial_reporting()
    {
        $this->actingAs($this->superAdmin);

        $asset = Asset::create([
            'name' => 'PC Server Lama',
            'category' => 'Mesin & Alat Berat',
            'location' => 'Server Room',
            'purchase_price' => 50000000,
            'purchase_date' => '2026-01-01',
            'status' => 'available',
        ]);

        // Soft delete execution
        $response = $this->delete(route('assets.destroy', $asset->id), [
            'reason' => 'Rusak total motherboard.',
        ]);

        $this->assertSoftDeleted('assets', ['id' => $asset->id]);

        // Trashed asset should still appear in financial reports
        $reportResponse = $this->get(route('reports.index'));
        $reportResponse->assertStatus(200);
        $reportResponse->assertSee('PC Server Lama');
    }
}
