<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class Mail extends Model
{
    use HasFactory;

    const STATUS_UNCOMPLETE = 'uncomplete';
    const STATUS_COMPLETED = 'completed';

    protected $guarded = [];

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
            'pending_mail' => $recipientCount,
            'user_id' => Auth::id()
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

    public static function fetch()
    {
        $query = MailRecipient::join('mails', 'mails.id', '=', 'mail_recipients.mail_id')
            ->select('mail_recipients.*')
            ->where('user_id', Auth::id());
        $result = MailRecipient::appendQueryOptionsToRequest($query);

        $counter = clone $result;

        return [
            'mail' => $result->get(),
            'page' => request()->get('page') ?? MailRecipient::DEFAULT_PAGE,
            'total' => $counter->count(),
        ];
    }
}
