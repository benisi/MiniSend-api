<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Symfony\Component\HttpFoundation\Response;
use Tests\TestCase;

class UserControllerTest extends TestCase
{
    use RefreshDatabase;

    const LOGIN_URL = 'api/login';
    const REGISTER_URL = 'api/register';

    public function test_can_login_user()
    {
        $user = User::factory()->create();

        $request = [
            'email' => $user->email,
            'password' => 'password'
        ];

        $response = $this->postJson(self::LOGIN_URL, $request);

        $response->assertStatus(Response::HTTP_OK);
    }

    public function test_login_failed_on_incorrect_credentials()
    {
        $request = [
            'email' => 'test@doe.com',
            'password' => 'password'
        ];

        $response = $this->postJson(self::LOGIN_URL, $request);

        $response->assertStatus(Response::HTTP_UNAUTHORIZED);
    }

    public function test_login_failed_on_missing_credentials()
    {
        $request = [];

        $response = $this->postJson(self::LOGIN_URL, $request);

        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY)
            ->assertJson([
                'errors' => [
                    'email' => ["The email field is required."],
                    'password' => ["The password field is required."]
                ]
            ]);

        $request = [
            'email' => 'lets test',
            'password' => 'password'
        ];

        $response = $this->postJson(self::LOGIN_URL, $request);

        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY)
            ->assertJson([
                'errors' => [
                    'email' => ["The email must be a valid email address."],
                ]
            ]);
    }

    public function test_add_new_user()
    {
        $request = [
            'name' => 'Benjamin Isidahomen',
            'email' => 'bisidahomen@gmail.com',
            'password' => 'password',
            'password_confirmation' => 'password'
        ];

        $this->postJson(self::REGISTER_URL, $request)
            ->assertStatus(Response::HTTP_CREATED)
            ->assertJson([
                'data' => [
                    'name' => 'Benjamin Isidahomen',
                    'email' => 'bisidahomen@gmail.com'
                ]
            ]);
    }

    public function test_failed_to_add_user_when_input_is_invalid()
    {
        $request = [
            'email' => 'bisidahomen@gmail.com',
            'password' => 'password',
            'password_confirmation' => 'password'
        ];

        $response = $this->postJson(self::REGISTER_URL, $request);
        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY)
            ->assertJson([
                'errors' => [
                    'name' => ['The name field is required.'],
                ]
            ]);

        $request = [
            'name' => 'benjamin',
            'password' => 'password',
            'password_confirmation' => 'password'
        ];

        $response = $this->postJson(self::REGISTER_URL, $request);
        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY)
            ->assertJson([
                'errors' => [
                    'email' => ['The email field is required.'],
                ]
            ]);

        $request = [
            'name' => 'benjamin',
            'email' => 'bisidahomen@gmail.com',
            'password_confirmation' => 'password'
        ];

        $response = $this->postJson(self::REGISTER_URL, $request);
        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY)
            ->assertJson([
                'errors' => [
                    'password' => ['The password field is required.'],
                ]
            ]);

        $request = [
            'name' => 'benjamin',
            'email' => 'bisidahomen@gmail.com',
            'password' => 'password',
            'password_confirmation' => 'passwor'
        ];

        $response = $this->postJson(self::REGISTER_URL, $request);
        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY)
            ->assertJson([
                'errors' => [
                    'password' => ['The password confirmation does not match.'],
                ]
            ]);

            $request = [
                'name' => 'benjamin',
                'email' => 123,
                'password' => 'password',
                'password_confirmation' => 'password'
            ];
    
            $response = $this->postJson(self::REGISTER_URL, $request);
            $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY)
                ->assertJson([
                    'errors' => [
                        'email' => ['The email must be a valid email address.'],
                    ]
                ]);
    }
}
