<?php

namespace Tests\Feature;

use App\Models\Token;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Symfony\Component\HttpFoundation\Response;
use Tests\TestCase;
use Tests\Traits\TestAuth;

class TokenControllerTest extends TestCase
{
    use RefreshDatabase;
    use TestAuth;

    const TOKEN_URL = 'api/v1/token';

    public function test_can_create_token()
    {
        $data = ['name' => 'token'];

        $user = User::factory()->create();
        $jwt = $this->getJwt($user);

        $this->postJson(self::TOKEN_URL, $data,  ['Authorization' => "Bearer {$jwt}"])
            ->assertStatus(Response::HTTP_CREATED);
    }

    public function test_can_get_token()
    {

        $data = ['name' => 'token'];

        $user = User::factory()->create();
        $jwt = $this->getJwt($user);

        $this->postJson(self::TOKEN_URL, $data,  ['Authorization' => "Bearer {$jwt}"])
            ->assertStatus(Response::HTTP_CREATED);

        $response = $this->getJson(self::TOKEN_URL,  ['Authorization' => "Bearer {$jwt}"]);
        $response->assertStatus(Response::HTTP_OK);
        $responseJson = $response->decodeResponseJson();
        $this->assertEquals(1, count($responseJson['data']));
    }

    public function test_can_delete_token()
    {

        $data = ['name' => 'token'];

        $user = User::factory()->create();
        $jwt = $this->getJwt($user);

        $this->postJson(self::TOKEN_URL, $data,  ['Authorization' => "Bearer {$jwt}"])
            ->assertStatus(Response::HTTP_CREATED);

        $token = Token::first();

        $response = $this->deleteJson(self::TOKEN_URL . "/$token->id", [], ['Authorization' => "Bearer {$jwt}"]);
        $response->assertStatus(Response::HTTP_OK);
        $this->assertDatabaseMissing('tokens', [
            'name' => $token->name,
            'token' => $token->token
        ]);
    }
}
