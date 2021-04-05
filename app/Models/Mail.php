<?php

namespace App\Models;

use App\Helpers\MessageParser;
use App\Traits\AppendQueryParameters;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class Mail extends Model
{
    use HasFactory;
    use AppendQueryParameters;

    const STATUS_POSTED = 'posted';
    const STATUS_SENT = 'sent';
    const STATUS_FAILED = 'failed';

    const DEFAULT_PAGE = 1;
    const DEFAULT_PER_PAGE = 20;

    protected $guarded = [];

    public static $searchable = ['mails.sender_email', 'mails.email', 'mails.subject', 'mails.name', 'batches.sender_name'];

    public static $filterable = ['recipient_email' => 'email', 'batch_id' => 'mails.batch_id'];

    public static $sortable = ['created_at' => 'created_at', 'status' => 'mails.status', 'sender_email' => 'mails.sender_email', 'sender_name' => 'batches.sender_name', 'email' => 'mails.email'];

    protected $casts = [
        'variables' => 'array',
    ];

    public function batch()
    {
        return $this->belongsTo(Batch::class);
    }

    public static function fetch()
    {
        $query = self::join('batches', 'batches.id', '=', 'mails.batch_id')
            ->select('mails.*', 'batches.sender_name')
            ->where('user_id', Auth::id());
        $result = self::appendQueryOptionsToQuery($query);

        $counter = clone $result;

        return [
            'mail' => $result->get(),
            'page' => request()->get('page') ?? self::DEFAULT_PAGE,
            'total' => $counter->count(),
        ];
    }

    public static function processRecipientsData(Request $request)
    {
        $recipients = $request->to;
        $recipients = collect($recipients)->map(function ($recipient) use ($request) {

            $recipient['sender_email'] = $request->from['email'];
            $recipient['variables'] = null;
            if ($request->variables) {
                $found = collect($request->variables)->first(function ($var) use ($recipient) {
                    return $recipient['email'] === $var['email'];
                });
                if ($found) {
                    $recipient['variables'] = $found['substitutions'];
                }
            }

            $recipient['subject'] = self::getSubject($request->subject,  $recipient['variables']);
            $recipient['text'] = self::getText($request->text,  $recipient['variables']);
            $recipient['html'] = self::getHtml($request->html,  $recipient['variables']);

            return $recipient;
        });


        return $recipients;
    }

    private static function getText($text, $variables)
    {
        if ($text) {
            $variables = self::getVariables($variables);
            return MessageParser::substituteValues($text, $variables);
        }

        return null;
    }

    private static function getHtml($html, $variables)
    {
        if ($html) {
            $variables = self::getVariables($variables);
            return MessageParser::substituteValues($html, $variables);
        }
        return null;
    }

    private static function getSubject($subject, $variables)
    {
        $variables = Mail::getVariables($variables);
        return MessageParser::substituteValues($subject, $variables);
    }

    private static function getVariables($variables): array
    {
        if (!$variables) {
            return [];
        }
        return $variables;
    }

    public static function showSingleMail(int $id)
    {

        return Mail::join('batches', 'batches.id', '=', 'mails.batch_id')
            ->select('mails.*', 'batches.sender_name', 'batches.attachments')
            ->where('mails.id', $id)
            ->where('batches.user_id', Auth::id())->first();
    }

    public static function getDashboardMailCount()
    {
        $mail = Mail::join('batches', 'batches.id', '=', 'mails.batch_id')
            ->select(DB::raw('count(mails.id) as count, mails.status'))
            ->groupBy('mails.status')
            ->where('batches.user_id', Auth::id())->get();

        $data = [];
        $status = [Mail::STATUS_FAILED, Mail::STATUS_POSTED, Mail::STATUS_SENT];

        collect($status)->each(function ($status) use ($mail, &$data) {
            $found = $mail->firstWhere('status', $status);
            $data[$status] = $found ? $found->count : 0;
        });

        return $data;
    }
}
