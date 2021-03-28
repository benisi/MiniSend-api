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

class EmailControllerTest extends TestCase
{

    use RefreshDatabase;

    const EMAIL_URL = 'api/v1/email';

    public function testCanSendEmail()
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
            "text" => 'Benjamin is saying hi',
            "attachments" => [
                'filename' => 'test',
                'url' => 'https://docs.google.com/document/d/1jBut-Fy7U4CaLPQql59NLi9-pmzDsn1toZDPIdYBb_4/edit'
            ]
        ];

        Mail::fake();
        Mail::assertNothingSent();

        $this->postJson(self::EMAIL_URL, $data)
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
            'pending_mail' => 0
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

    public function testCanSendEmailWithVariableSubstitution()
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

        $this->postJson(self::EMAIL_URL, $data)
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
            'pending_mail' => 0
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
}
