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
}
