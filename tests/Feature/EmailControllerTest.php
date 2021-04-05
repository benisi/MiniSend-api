<?php

namespace Tests\Feature;

use App\Mail\Mailer;
use App\Models\Batch;
use App\Models\Mail;
use App\Models\Token;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Symfony\Component\HttpFoundation\Response;
use Tests\TestCase;
use Illuminate\Support\Facades\Mail as MailFacades;
use App\Models\User;
use Tests\Mock\EmailRequestData;
use Tests\Traits\TestAuth;

class EmailControllerTest extends TestCase
{

    use RefreshDatabase;
    use TestAuth;

    const EMAIL_URL = 'api/v1/email';
    const BATCH_URL = 'api/v1/batch';

    public function test_can_send_email()
    {
        $data = EmailRequestData::getRequestDataWithOutVariables();

        MailFacades::fake();
        MailFacades::assertNothingSent();

        $user = User::factory()->create();
        $jwt = 'yeyyeebdb8348488484848484848484';

        Token::create([
            'name' => 'test',
            'token' => $jwt,
            'user_id' => $user->id
        ]);

        $this->postJson(self::EMAIL_URL, $data, ['Authorization' => "Bearer {$jwt}"])
            ->assertStatus(Response::HTTP_ACCEPTED);

        MailFacades::assertSent(Mailer::class, 2);
        MailFacades::assertSent(Mailer::class, function ($mail) {
            return $mail->hasTo('test@doe.com');
        });

        MailFacades::assertSent(Mailer::class, function ($mail) {
            return $mail->hasTo('mark@doe.com');
        });

        $mail = Batch::first();

        $this->assertDatabaseHas('batches', [
            "sender_email" => 'bisidahomen@gmail.com',
            "sender_name" => 'Benjamin Isidahomen',
            "subject" => 'Hi from Benjamin',
            "status" => Batch::STATUS_COMPLETED,
            "text" => 'Benjamin is saying hi',
            "html" => null,
            "attachments" => null,
            'pending_mail' => 0,
            'user_id' => $user->id
        ]);

        $this->assertDatabaseHas('mails', [
            'batch_id' => $mail->id,
            'sender_email' => 'bisidahomen@gmail.com',
            "email" => "test@doe.com",
            "name" => "John doe",
            "status" => Mail::STATUS_SENT
        ]);

        $this->assertDatabaseHas('mails', [
            'batch_id' => $mail->id,
            'sender_email' => 'bisidahomen@gmail.com',
            "email" => "mark@doe.com",
            "name" => "Mark doe",
            "status" => Mail::STATUS_SENT
        ]);
    }

    public function test_can_send_email_with_variable_substitution()
    {
        $data = EmailRequestData::getRequestDataWithVariables();

        MailFacades::fake();
        MailFacades::assertNothingSent();

        $user = User::factory()->create();
        $jwt = 'yeyyeebdb8348488484848484848484';

        Token::create([
            'name' => 'test',
            'token' => $jwt,
            'user_id' => $user->id
        ]);

        $this->postJson(self::EMAIL_URL, $data,  ['Authorization' => "Bearer {$jwt}"])
            ->assertStatus(Response::HTTP_ACCEPTED);

        MailFacades::assertSent(Mailer::class, 2);
        MailFacades::assertSent(Mailer::class, function ($mail) {
            return $mail->hasTo('test@doe.com');
        });

        MailFacades::assertSent(Mailer::class, function ($mail) {
            return $mail->hasTo('mark@doe.com');
        });

        $mail = Batch::first();

        $this->assertDatabaseHas('batches', [
            "sender_email" => 'bisidahomen@gmail.com',
            "sender_name" => 'Benjamin Isidahomen',
            "subject" => 'Hi from {$name}',
            "status" => Batch::STATUS_COMPLETED,
            "text" => '{$name} is saying hi',
            "html" => null,
            "attachments" => null,
            'pending_mail' => 0,
            'user_id' => $user->id
        ]);

        $this->assertDatabaseHas('mails', [
            'batch_id' => $mail->id,
            'sender_email' => 'bisidahomen@gmail.com',
            "email" => "test@doe.com",
            "name" => "John doe",
            "status" => Mail::STATUS_SENT
        ]);

        $this->assertDatabaseHas('mails', [
            'batch_id' => $mail->id,
            'sender_email' => 'bisidahomen@gmail.com',
            "email" => "mark@doe.com",
            "name" => "Mark doe",
            "status" => Mail::STATUS_SENT
        ]);
    }

    public function test_can_send_email_with_variable_substitution_with_html_content()
    {
        $data = EmailRequestData::getRequestDataWithVariableAndHtml();

        MailFacades::fake();
        MailFacades::assertNothingSent();

        $user = User::factory()->create();
        $jwt = 'yeyyeebdb8348488484848484848484';

        Token::create([
            'name' => 'test',
            'token' => $jwt,
            'user_id' => $user->id
        ]);
        $this->postJson(self::EMAIL_URL, $data,  ['Authorization' => "Bearer {$jwt}"])
            ->assertStatus(Response::HTTP_ACCEPTED);

        MailFacades::assertSent(Mailer::class, 2);
        MailFacades::assertSent(Mailer::class, function ($mail) {
            return $mail->hasTo('test@doe.com');
        });

        MailFacades::assertSent(Mailer::class, function ($mail) {
            return $mail->hasTo('mark@doe.com');
        });

        $mail = Batch::first();

        $this->assertDatabaseHas('batches', [
            "sender_email" => 'bisidahomen@gmail.com',
            "sender_name" => 'Benjamin Isidahomen',
            "subject" => 'Hi from {$name}',
            "status" => Batch::STATUS_COMPLETED,
            "text" => null,
            "attachments" => null,
            'pending_mail' => 0,
            'user_id' => $user->id
        ]);

        $this->assertDatabaseHas('mails', [
            'batch_id' => $mail->id,
            'sender_email' => 'bisidahomen@gmail.com',
            "email" => "test@doe.com",
            "name" => "John doe",
            "subject" => 'Hi from Benjamin',
            "status" => Mail::STATUS_SENT
        ]);

        $this->assertDatabaseHas('mails', [
            'batch_id' => $mail->id,
            'sender_email' => 'bisidahomen@gmail.com',
            "email" => "mark@doe.com",
            "name" => "Mark doe",
            "status" => Mail::STATUS_SENT
        ]);
    }

    public function test_can_send_email_with_attachment()
    {
        $data = EmailRequestData::getRequestDataWithAttachment();

        MailFacades::fake();
        MailFacades::assertNothingSent();

        $user = User::factory()->create();
        $jwt = 'yeyyeebdb8348488484848484848484';

        Token::create([
            'name' => 'test',
            'token' => $jwt,
            'user_id' => $user->id
        ]);
        $response = $this->postJson(self::EMAIL_URL, $data,  ['Authorization' => "Bearer {$jwt}"]);
        $response->assertStatus(Response::HTTP_ACCEPTED);

        MailFacades::assertSent(Mailer::class, 2);
        MailFacades::assertSent(Mailer::class, function ($mail) {
            return $mail->hasTo('test@doe.com');
        });

        MailFacades::assertSent(Mailer::class, function ($mail) {
            return $mail->hasTo('mark@doe.com');
        });

        $mail = Batch::first();

        $this->assertDatabaseHas('batches', [
            "sender_email" => 'bisidahomen@gmail.com',
            "sender_name" => 'Benjamin Isidahomen',
            "subject" => 'Hi from Benjamin',
            "status" => Batch::STATUS_COMPLETED,
            "text" => 'Benjamin is saying hi',
            'pending_mail' => 0,
            'user_id' => $user->id
        ]);

        $this->assertDatabaseHas('mails', [
            'batch_id' => $mail->id,
            'sender_email' => 'bisidahomen@gmail.com',
            "email" => "test@doe.com",
            "name" => "John doe",
            "subject" => 'Hi from Benjamin',
            "status" => Mail::STATUS_SENT
        ]);

        $this->assertDatabaseHas('mails', [
            'batch_id' => $mail->id,
            'sender_email' => 'bisidahomen@gmail.com',
            "email" => "mark@doe.com",
            "name" => "Mark doe",
            "status" => Mail::STATUS_SENT
        ]);
    }

    public function test_will_return_422_if_an_invalid_attachment_is_supplied()
    {
        $data = EmailRequestData::getRequestDataWithInvalidAttachment();

        MailFacades::fake();
        MailFacades::assertNothingSent();

        $user = User::factory()->create();
        $jwt = 'yeyyeebdb8348488484848484848484';

        Token::create([
            'name' => 'test',
            'token' => $jwt,
            'user_id' => $user->id
        ]);
        $response = $this->postJson(self::EMAIL_URL, $data,  ['Authorization' => "Bearer {$jwt}"]);
        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
    }


    public function test_can_send_email_will_fail_when_supplied_invalid_payload()
    {
        $data = [];

        MailFacades::fake();
        MailFacades::assertNothingSent();
        $user = User::factory()->create();
        $token = 'yeyyeebdb8348488484848484848484';

        Token::create([
            'name' => 'test',
            'token' => $token,
            'user_id' => $user->id
        ]);
        $response = $this->postJson(self::EMAIL_URL, $data,  ['Authorization' => "Bearer {$token}"]);
        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
    }

    public function test_can_send_email_will_fail_if_key_is_not_supplied()
    {
        $data = [];

        MailFacades::fake();
        MailFacades::assertNothingSent();
        $response = $this->postJson(self::EMAIL_URL, $data);
        $response->assertStatus(Response::HTTP_UNAUTHORIZED);
    }

    public function test_can_get_emails()
    {
        $data = EmailRequestData::getRequestDataWithVariables();

        MailFacades::fake();
        MailFacades::assertNothingSent();

        $user = User::factory()->create();
        $token = 'yeyyeebdb8348488484848484848484';

        Token::create([
            'name' => 'test',
            'token' => $token,
            'user_id' => $user->id
        ]);

        $jwt = $this->getJwt($user);
        $this->postJson(self::EMAIL_URL, $data,  ['Authorization' => "Bearer {$token}"])
            ->assertStatus(Response::HTTP_ACCEPTED);

        $response = $this->getJson(self::EMAIL_URL, ['Authorization' => "Bearer {$jwt}"]);
        $response->assertStatus(Response::HTTP_OK);
        $responseJson = $response->decodeResponseJson();
        $this->assertEquals($responseJson['data']['total'], 2);
    }


    public function test_can_get_emails_filtered_by_recipient()
    {
        $data = EmailRequestData::getRequestDataWithVariables();

        MailFacades::fake();
        MailFacades::assertNothingSent();

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

        $response = $this->getJson(self::EMAIL_URL . "?filters[recipient_email]=mark@doe.com", ['Authorization' => "Bearer {$jwt}"]);
        $response->assertStatus(Response::HTTP_OK);
        $responseJson = $response->decodeResponseJson();
        $this->assertEquals($responseJson['data']['total'], 1);
        $this->assertEquals($responseJson['data']['page'], 1);

        $response = $this->getJson(self::EMAIL_URL . "?filters[recipient_email]=test", ['Authorization' => "Bearer {$jwt}"]);
        $response->assertStatus(Response::HTTP_OK);
        $responseJson = $response->decodeResponseJson();
        $this->assertEquals($responseJson['data']['total'], 0);
        $this->assertEquals($responseJson['data']['page'], 1);
    }


    public function test_can_get_emails_searched_by_recipient()
    {
        $data = EmailRequestData::getRequestDataWithVariables();

        MailFacades::fake();
        MailFacades::assertNothingSent();

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

        $response = $this->getJson(self::EMAIL_URL . "?search=test@doe.com", ['Authorization' => "Bearer {$jwt}"]);
        $response->assertStatus(Response::HTTP_OK);
        $responseJson = $response->decodeResponseJson();
        $this->assertEquals($responseJson['data']['total'], 1);
        $this->assertEquals($responseJson['data']['page'], 1);

        $response = $this->getJson(self::EMAIL_URL . "?search=bisidahomen", ['Authorization' => "Bearer {$jwt}"]);
        $response->assertStatus(Response::HTTP_OK);
        $responseJson = $response->decodeResponseJson();
        $this->assertEquals($responseJson['data']['total'], 2);
        $this->assertEquals($responseJson['data']['page'], 1);
    }

    public function test_can_get_emails_sorted()
    {
        $data = EmailRequestData::getRequestDataWithVariables();
        MailFacades::fake();
        MailFacades::assertNothingSent();

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

        $mail = Mail::where('name', 'Mark doe')->first();
        $mail->created_at = now()->subDays(2);
        $mail->save();

        $response = $this->getJson(self::EMAIL_URL . "?sort[field]=created_at&sort[direction]=desc", ['Authorization' => "Bearer {$jwt}"]);
        $response->assertStatus(Response::HTTP_OK);
        $responseJson = $response->decodeResponseJson();
        $this->assertEquals($responseJson['data']['mail'][0]['name'], 'John doe');

        $response = $this->getJson(self::EMAIL_URL . "?sort[field]=created_at&sort[direction]=asc", ['Authorization' => "Bearer {$jwt}"]);
        $response->assertStatus(Response::HTTP_OK);
        $responseJson = $response->decodeResponseJson();
        $this->assertEquals($responseJson['data']['mail'][0]['name'], 'Mark doe');
    }

    public function test_can_get_a_single_mail()
    {
        $data = EmailRequestData::getRequestDataWithVariables();
        MailFacades::fake();
        MailFacades::assertNothingSent();

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

        $response = $this->getJson(self::EMAIL_URL . "/{$mail->id}", ['Authorization' => "Bearer {$jwt}"]);
        $response->assertStatus(Response::HTTP_OK);
        $responseJson = $response->decodeResponseJson();
        $this->assertEquals($mail->id, $responseJson['data']['id']);
    }

    public function test_can_get_all_batch()
    {
        $data = EmailRequestData::getRequestDataWithVariables();
        MailFacades::fake();
        MailFacades::assertNothingSent();

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

        $response = $this->getJson(self::BATCH_URL, ['Authorization' => "Bearer {$jwt}"]);
        $response->assertStatus(Response::HTTP_OK);
        $responseJson = $response->decodeResponseJson();
        $this->assertEquals(1, $responseJson['data']['total']);
    }
}
