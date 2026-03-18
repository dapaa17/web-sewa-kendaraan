<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class ProfileTest extends TestCase
{
    use RefreshDatabase;

    public function test_profile_page_is_displayed(): void
    {
        /** @var User $user */
        $user = User::factory()->create();

        $response = $this
            ->actingAs($user)
            ->get('/profile');

        $response->assertOk();
    }

    public function test_profile_information_can_be_updated(): void
    {
        /** @var User $user */
        $user = User::factory()->create();

        $response = $this
            ->actingAs($user)
            ->patch('/profile', [
                'name' => 'Test User',
                'email' => 'test@example.com',
            ]);

        $response
            ->assertSessionHasNoErrors()
            ->assertRedirect('/profile');

        $user->refresh();

        $this->assertSame('Test User', $user->name);
        $this->assertSame('test@example.com', $user->email);
        $this->assertNull($user->email_verified_at);
    }

    public function test_email_verification_status_is_unchanged_when_the_email_address_is_unchanged(): void
    {
        /** @var User $user */
        $user = User::factory()->create();

        $response = $this
            ->actingAs($user)
            ->patch('/profile', [
                'name' => 'Test User',
                'email' => $user->email,
            ]);

        $response
            ->assertSessionHasNoErrors()
            ->assertRedirect('/profile');

        $this->assertNotNull($user->refresh()->email_verified_at);
    }

    public function test_user_can_delete_their_account(): void
    {
        /** @var User $user */
        $user = User::factory()->create();

        $response = $this
            ->actingAs($user)
            ->delete('/profile', [
                'password' => 'password',
            ]);

        $response
            ->assertSessionHasNoErrors()
            ->assertRedirect('/');

        $this->assertGuest();
        $this->assertSoftDeleted($user);
    }

    public function test_correct_password_must_be_provided_to_delete_account(): void
    {
        /** @var User $user */
        $user = User::factory()->create();

        $response = $this
            ->actingAs($user)
            ->from('/profile')
            ->delete('/profile', [
                'password' => 'wrong-password',
            ]);

        $response
            ->assertSessionHasErrorsIn('userDeletion', 'password')
            ->assertRedirect('/profile');

        $this->assertNotNull($user->fresh());
    }

    public function test_admin_is_treated_as_ktp_verified(): void
    {
        /** @var User $admin */
        $admin = User::factory()->create([
            'role' => 'admin',
        ]);

        $this->assertTrue($admin->isKtpVerified());
        $this->assertFalse($admin->isKtpPending());
        $this->assertSame('Tidak Diperlukan', $admin->getKtpStatusLabel());
    }

    public function test_admin_ktp_page_shows_verification_is_not_required(): void
    {
        /** @var User $admin */
        $admin = User::factory()->create([
            'role' => 'admin',
        ]);

        $response = $this
            ->actingAs($admin)
            ->get('/profile/ktp');

        $response
            ->assertOk()
            ->assertSee('Akun Admin Tidak Perlu Verifikasi KTP')
            ->assertDontSee('id="ktp_image"', false);
    }

    public function test_admin_cannot_upload_ktp(): void
    {
        Storage::fake('public');

        /** @var User $admin */
        $admin = User::factory()->create([
            'role' => 'admin',
        ]);
        $originalStatus = $admin->fresh()->ktp_status;

        $response = $this
            ->actingAs($admin)
            ->post('/profile/ktp', [
                'ktp_number' => '1234567890123456',
                'ktp_image' => UploadedFile::fake()->create('ktp.jpg', 100, 'image/jpeg'),
            ]);

        $response
            ->assertRedirect('/profile/ktp')
            ->assertSessionHas('warning', 'Akun admin tidak memerlukan verifikasi KTP.');

        $admin->refresh();

        $this->assertNull($admin->ktp_image);
        $this->assertNull($admin->ktp_number);
        $this->assertSame($originalStatus, $admin->ktp_status);
        $this->assertSame([], Storage::disk('public')->allFiles('ktp'));
    }
}
