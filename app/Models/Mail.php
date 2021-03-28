<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;

class Mail extends Model
{
    use HasFactory;

    const STATUS_UNCOMPLETE = 'uncomplete';
    const STATUS_COMPLETED = 'completed';

    protected $fillable = [
        'sender_email',
        'sender_name',
        'subject',
        'recipient_count',
        'pending_mail',
        'text'
    ];

    public function recipients()
    {
        return $this->hasMany(MailRecipient::class);
    }

    public static function processMailRequestData(Request $request): array
    {
        $recipientCount = count($request->to);
        $data = [
            'sender_email' => $request->from['email'],
            'sender_name' => $request->from['name'],
            'subject' => $request->subject,
            'recipient_count' => $recipientCount,
            'pending_mail' => $recipientCount
        ];

        if ($request->text) {
            $data['text'] = $request->text;
        }

        if ($request->html) {
            $data['html'] = $request->html;
        }

        if ($request->attachments) {
            $data['attachments'] = $request->attachments;
        }
        return $data;
    }

    public static function processRecipientsData(Request $request)
    {
        $recipients = $request->to;
        $recipients = collect($recipients)->map(function ($recipient) use ($request) {
            $recipient['sender_email'] = $request->from['email'];
            if ($request->variables) {
                $found = collect($request->variables)->first(function ($var) use ($recipient) {
                    return $recipient['email'] === $var['email'];
                });
                if ($found) {
                    $recipient['variables'] = $found['substitutions'];
                }
            }

            return $recipient;
        });


        return $recipients;
    }
}
