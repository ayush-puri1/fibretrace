<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;

class UserVerificationTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_approve_pending_user()
    {
        $admin = User::factory()->create(['role' => 'admin', 'status' => 'verified']);
        $pendingUser = User::factory()->create(['role' => 'seller', 'status' => 'pending']);

        $response = $this->actingAs($admin)->post(route('admin.verifications.approve', $pendingUser->id));

        $response->assertRedirect();
        $this->assertDatabaseHas('users', [
            'id' => $pendingUser->id,
            'status' => 'verified',
            'verified_by' => $admin->id
        ]);
    }

    public function test_admin_can_reject_pending_user()
    {
        $admin = User::factory()->create(['role' => 'admin', 'status' => 'verified']);
        $pendingUser = User::factory()->create(['role' => 'seller', 'status' => 'pending']);

        $response = $this->actingAs($admin)->post(route('admin.verifications.reject', $pendingUser->id), [
            'reason' => 'Invalid GSTIN'
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('users', [
            'id' => $pendingUser->id,
            'status' => 'rejected',
            'rejection_reason' => 'Invalid GSTIN'
        ]);
    }

    public function test_non_admin_cannot_approve_user()
    {
        $seller = User::factory()->create(['role' => 'seller', 'status' => 'verified']);
        $pendingUser = User::factory()->create(['role' => 'buyer', 'status' => 'pending']);

        $response = $this->actingAs($seller)->post(route('admin.verifications.approve', $pendingUser->id));

        $response->assertForbidden(); // 403
    }
}
