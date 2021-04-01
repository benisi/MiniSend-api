<?php

namespace Tests\Feature;

use App\Mail\Mailer;
use App\Models\Mail as ModelsMail;
use App\Models\MailRecipient;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Symfony\Component\HttpFoundation\Response;
use Tests\TestCase;
use Illuminate\Support\Facades\Mail;
use App\Models\Mail as ModelMail;
use App\Models\User;
use Tests\Traits\TestAuth;

class EmailControllerTest extends TestCase
{

    use RefreshDatabase;
    use TestAuth;

    const EMAIL_URL = 'api/v1/email';

    public function test_can_send_email()
    {
        $data = [
            "from" => [
                "email" => "bisidahomen@gmail.com",
                "name" => "Benjamin Isidahomen"
            ],
            "to" => [
                [
                    "email" => "test@doe.com",
                    "name" => "John doe"
                ],
                [
                    "email" => "mark@doe.com",
                    "name" => "Mark doe"
                ],
            ],
            "subject" => 'Hi from Benjamin',
            "text" => 'Benjamin is saying hi'
        ];

        Mail::fake();
        Mail::assertNothingSent();

        $user = User::factory()->create();
        $jwt = $this->getJwt($user);

        $this->postJson(self::EMAIL_URL, $data, ['Authorization' => "Bearer {$jwt}"])
            ->assertStatus(Response::HTTP_ACCEPTED);

        Mail::assertSent(Mailer::class, 2);
        Mail::assertSent(Mailer::class, function ($mail) {
            return $mail->hasTo('test@doe.com');
        });

        Mail::assertSent(Mailer::class, function ($mail) {
            return $mail->hasTo('mark@doe.com');
        });

        $mail = ModelsMail::first();

        $this->assertDatabaseHas('mails', [
            "sender_email" => 'bisidahomen@gmail.com',
            "sender_name" => 'Benjamin Isidahomen',
            "subject" => 'Hi from Benjamin',
            "status" => ModelMail::STATUS_COMPLETED,
            "text" => 'Benjamin is saying hi',
            "html" => null,
            "attachments" => null,
            'pending_mail' => 0,
            'user_id' => $user->id
        ]);

        $this->assertDatabaseHas('mail_recipients', [
            'mail_id' => $mail->id,
            'sender_email' => 'bisidahomen@gmail.com',
            "email" => "test@doe.com",
            "name" => "John doe",
            "status" => MailRecipient::STATUS_SENT
        ]);

        $this->assertDatabaseHas('mail_recipients', [
            'mail_id' => $mail->id,
            'sender_email' => 'bisidahomen@gmail.com',
            "email" => "mark@doe.com",
            "name" => "Mark doe",
            "status" => MailRecipient::STATUS_SENT
        ]);
    }

    public function test_can_send_email_with_variable_substitution()
    {
        $data = [
            "from" => [
                "email" => "bisidahomen@gmail.com",
                "name" => "Benjamin Isidahomen"
            ],
            "to" => [
                [
                    "email" => "test@doe.com",
                    "name" => "John doe"
                ],
                [
                    "email" => "mark@doe.com",
                    "name" => "Mark doe"
                ],
            ],
            "subject" => 'Hi from {$name}',
            "text" => '{$name} is saying hi',
            "variables" => [
                [
                    "email" => "test@doe.com",
                    "substitutions" => [
                        [
                            "var" => "name",
                            "value" => "Benjamin"
                        ]
                    ]
                ]
            ]
        ];

        Mail::fake();
        Mail::assertNothingSent();

        $user = User::factory()->create();
        $jwt = $this->getJwt($user);
        $this->postJson(self::EMAIL_URL, $data,  ['Authorization' => "Bearer {$jwt}"])
            ->assertStatus(Response::HTTP_ACCEPTED);

        Mail::assertSent(Mailer::class, 2);
        Mail::assertSent(Mailer::class, function ($mail) {
            return $mail->hasTo('test@doe.com');
        });

        Mail::assertSent(Mailer::class, function ($mail) {
            return $mail->hasTo('mark@doe.com');
        });

        $mail = ModelsMail::first();

        $this->assertDatabaseHas('mails', [
            "sender_email" => 'bisidahomen@gmail.com',
            "sender_name" => 'Benjamin Isidahomen',
            "subject" => 'Hi from {$name}',
            "status" => ModelMail::STATUS_COMPLETED,
            "text" => '{$name} is saying hi',
            "html" => null,
            "attachments" => null,
            'pending_mail' => 0,
            'user_id' => $user->id
        ]);

        $this->assertDatabaseHas('mail_recipients', [
            'mail_id' => $mail->id,
            'sender_email' => 'bisidahomen@gmail.com',
            "email" => "test@doe.com",
            "name" => "John doe",
            "status" => MailRecipient::STATUS_SENT
        ]);

        $this->assertDatabaseHas('mail_recipients', [
            'mail_id' => $mail->id,
            'sender_email' => 'bisidahomen@gmail.com',
            "email" => "mark@doe.com",
            "name" => "Mark doe",
            "status" => MailRecipient::STATUS_SENT
        ]);
    }

    public function test_can_send_email_with_variable_substitution_with_html_content()
    {
        $data = [
            "from" => [
                "email" => "bisidahomen@gmail.com",
                "name" => "Benjamin Isidahomen"
            ],
            "to" => [
                [
                    "email" => "test@doe.com",
                    "name" => "John doe"
                ],
                [
                    "email" => "mark@doe.com",
                    "name" => "Mark doe"
                ],
            ],
            "subject" => 'Hi from {$name}',
            "html" => '<h1>{$name} is saying hi</h1>
                <p>testing html with {$name}</p>
            ',
            "variables" => [
                [
                    "email" => "test@doe.com",
                    "substitutions" => [
                        [
                            "var" => "name",
                            "value" => "Benjamin"
                        ]
                    ]
                ]
            ]
        ];

        Mail::fake();
        Mail::assertNothingSent();

        $user = User::factory()->create();
        $jwt = $this->getJwt($user);
        $this->postJson(self::EMAIL_URL, $data,  ['Authorization' => "Bearer {$jwt}"])
            ->assertStatus(Response::HTTP_ACCEPTED);

        Mail::assertSent(Mailer::class, 2);
        Mail::assertSent(Mailer::class, function ($mail) {
            return $mail->hasTo('test@doe.com');
        });

        Mail::assertSent(Mailer::class, function ($mail) {
            return $mail->hasTo('mark@doe.com');
        });

        $mail = ModelsMail::first();

        $this->assertDatabaseHas('mails', [
            "sender_email" => 'bisidahomen@gmail.com',
            "sender_name" => 'Benjamin Isidahomen',
            "subject" => 'Hi from {$name}',
            "status" => ModelMail::STATUS_COMPLETED,
            "text" => null,
            "attachments" => null,
            'pending_mail' => 0,
            'user_id' => $user->id
        ]);

        $this->assertDatabaseHas('mail_recipients', [
            'mail_id' => $mail->id,
            'sender_email' => 'bisidahomen@gmail.com',
            "email" => "test@doe.com",
            "name" => "John doe",
            "subject" => 'Hi from Benjamin',
            "status" => MailRecipient::STATUS_SENT
        ]);

        $this->assertDatabaseHas('mail_recipients', [
            'mail_id' => $mail->id,
            'sender_email' => 'bisidahomen@gmail.com',
            "email" => "mark@doe.com",
            "name" => "Mark doe",
            "status" => MailRecipient::STATUS_SENT
        ]);
    }

    public function test_can_send_email_will_fail_when_supplied_invalid_payload()
    {
        $data = [];

        Mail::fake();
        Mail::assertNothingSent();
        $user = User::factory()->create();
        $jwt = $this->getJwt($user);
        $response = $this->postJson(self::EMAIL_URL, $data,  ['Authorization' => "Bearer {$jwt}"]);
        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
    }

    public function test_can_send_email_will_fail_if_key_is_not_supplied()
    {
        $data = [];

        Mail::fake();
        Mail::assertNothingSent();
        $response = $this->postJson(self::EMAIL_URL, $data);
        $response->assertStatus(Response::HTTP_UNAUTHORIZED);
    }

    public function test_can_get_user_email()
    {
        $data = [
            "from" => [
                "email" => "bisidahomen@gmail.com",
                "name" => "Benjamin Isidahomen"
            ],
            "to" => [
                [
                    "email" => "test@doe.com",
                    "name" => "John doe"
                ],
                [
                    "email" => "mark@doe.com",
                    "name" => "Mark doe"
                ],
            ],
            "subject" => 'Hi from {$name}',
            "html" => '<h1>{$name} is saying hi</h1>
                <p>testing html with {$name}</p>
            ',
            "variables" => [
                [
                    "email" => "test@doe.com",
                    "substitutions" => [
                        [
                            "var" => "name",
                            "value" => "Benjamin"
                        ]
                    ]
                ]
            ]
        ];

        Mail::fake();
        Mail::assertNothingSent();

        $user = User::factory()->create();
        $jwt = $this->getJwt($user);
        $this->postJson(self::EMAIL_URL, $data,  ['Authorization' => "Bearer {$jwt}"])
            ->assertStatus(Response::HTTP_ACCEPTED);

        $response = $this->getJson(self::EMAIL_URL, ['Authorization' => "Bearer {$jwt}"]);
        $response->assertStatus(Response::HTTP_OK);
        $responseJson = $response->decodeResponseJson();
        $this->assertEquals($responseJson['data']['total'], 2);
    }


    public function test_can_get_user_email_filtered_by_recipient()
    {
        $data = [
            "from" => [
                "email" => "bisidahomen@gmail.com",
                "name" => "Benjamin Isidahomen"
            ],
            "to" => [
                [
                    "email" => "test@doe.com",
                    "name" => "John doe"
                ],
                [
                    "email" => "mark@doe.com",
                    "name" => "Mark doe"
                ],
            ],
            "subject" => 'Hi from {$name}',
            "html" => '<h1>{$name} is saying hi</h1>
                <p>testing html with {$name}</p>
            ',
            "variables" => [
                [
                    "email" => "test@doe.com",
                    "substitutions" => [
                        [
                            "var" => "name",
                            "value" => "Benjamin"
                        ]
                    ]
                ]
            ]
        ];

        Mail::fake();
        Mail::assertNothingSent();

        $user = User::factory()->create();
        $jwt = $this->getJwt($user);
        $this->postJson(self::EMAIL_URL, $data,  ['Authorization' => "Bearer {$jwt}"])
            ->assertStatus(Response::HTTP_ACCEPTED);

        $response = $this->getJson(self::EMAIL_URL."?filters[recipient_email]=mark@doe.com", ['Authorization' => "Bearer {$jwt}"]);
        $response->assertStatus(Response::HTTP_OK);
        $responseJson = $response->decodeResponseJson();
        $this->assertEquals($responseJson['data']['total'], 1);
        $this->assertEquals($responseJson['data']['page'], 1);

        $response = $this->getJson(self::EMAIL_URL."?filters[recipient_email]=test", ['Authorization' => "Bearer {$jwt}"]);
        $response->assertStatus(Response::HTTP_OK);
        $responseJson = $response->decodeResponseJson();
        $this->assertEquals($responseJson['data']['total'], 0);
        $this->assertEquals($responseJson['data']['page'], 1);
    }


    public function test_can_get_user_email_search_by_recipient()
    {
        $data = [
            "from" => [
                "email" => "bisidahomen@gmail.com",
                "name" => "Benjamin Isidahomen"
            ],
            "to" => [
                [
                    "email" => "test@doe.com",
                    "name" => "John doe"
                ],
                [
                    "email" => "mark@doe.com",
                    "name" => "Mark doe"
                ],
            ],
            "subject" => 'Hi from {$name}',
            "html" => '<h1>{$name} is saying hi</h1>
                <p>testing html with {$name}</p>
            ',
            "variables" => [
                [
                    "email" => "test@doe.com",
                    "substitutions" => [
                        [
                            "var" => "name",
                            "value" => "Benjamin"
                        ]
                    ]
                ]
            ]
        ];

        Mail::fake();
        Mail::assertNothingSent();

        $user = User::factory()->create();
        $jwt = $this->getJwt($user);
        $this->postJson(self::EMAIL_URL, $data,  ['Authorization' => "Bearer {$jwt}"])
            ->assertStatus(Response::HTTP_ACCEPTED);

        $response = $this->getJson(self::EMAIL_URL."?search=test@doe.com", ['Authorization' => "Bearer {$jwt}"]);
        $response->assertStatus(Response::HTTP_OK);
        $responseJson = $response->decodeResponseJson();
        $this->assertEquals($responseJson['data']['total'], 1);
        $this->assertEquals($responseJson['data']['page'], 1);

        $response = $this->getJson(self::EMAIL_URL."?search=bisidahomen", ['Authorization' => "Bearer {$jwt}"]);
        $response->assertStatus(Response::HTTP_OK);
        $responseJson = $response->decodeResponseJson();
        $this->assertEquals($responseJson['data']['total'], 2);
        $this->assertEquals($responseJson['data']['page'], 1);
    }
}
