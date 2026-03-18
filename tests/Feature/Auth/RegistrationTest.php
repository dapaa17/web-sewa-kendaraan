<?php

namespace Tests\Feature\Auth;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RegistrationTest extends TestCase
{
    use RefreshDatabase;

    private const REGISTRATION_SUCCESS_MESSAGE = 'Akun berhasil dibuat. Silakan login.';

    public function test_registration_screen_can_be_rendered(): void
    {
        $response = $this->get('/register');

        $response->assertStatus(200);
    }

    public function test_new_users_can_register(): void
    {
        $response = $this->post('/register', [
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => 'password',
            'password_confirmation' => 'password',
        ]);

        $this->assertGuest();
        $response->assertRedirect(route('login', absolute: false));
        $response->assertSessionHas('status', self::REGISTRATION_SUCCESS_MESSAGE);
    }

    public function test_login_screen_displays_registration_success_message(): void
    {
        $response = $this->withSession([
            'status' => self::REGISTRATION_SUCCESS_MESSAGE,
        ])->get('/login');

        $response->assertOk();
        $response->assertSeeText(self::REGISTRATION_SUCCESS_MESSAGE);
    }
}
