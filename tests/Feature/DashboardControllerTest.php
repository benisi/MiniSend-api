<?php

namespace Tests\Feature;

use App\Models\Mail;
use App\Models\Token;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail as FacadesMail;
use Symfony\Component\HttpFoundation\Response;
use Tests\Mock\EmailRequestData;
use Tests\TestCase;
use Tests\Traits\TestAuth;

class DashboardControllerTest extends TestCase
{
    use RefreshDatabase;
    use TestAuth;

    const DASHBOARD_URL = 'api/v1/dashboard';
    const EMAIL_URL = 'api/v1/email';

    public function test_can_get_dashboard_details()
    {
        $data = EmailRequestData::getRequestDataWithVariables();
        FacadesMail::fake();
        FacadesMail::assertNothingSent();

        $user = User::factory()->create();
        $jwt = $this->getJwt($user);
        $token = 'yeyyeebdb8348488484848484848484';

        Token::create([
            'name' => 'test',
            'token' => $token,
            'user_id' => $user->id
        ]);
        $this->postJson(self::EMAIL_URL, $data,  ['Authorization' => "Bearer {$token}"])
            ->assertStatus(Response::HTTP_ACCEPTED);

        $mail = Mail::first();
        $mail->status = Mail::STATUS_FAILED;
        $mail->save();

        $response = $this->getJson(self::DASHBOARD_URL, ['Authorization' => "Bearer {$jwt}"]);
        $response->assertStatus(Response::HTTP_OK);
        $responseJson = $response->decodeResponseJson();
        $this->assertEquals($responseJson['data']['mail'][Mail::STATUS_SENT], 1);
        $this->assertEquals($responseJson['data']['mail'][Mail::STATUS_FAILED], 1);
        $this->assertEquals($responseJson['data']['mail'][Mail::STATUS_POSTED], 0);
    }
}
