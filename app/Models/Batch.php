<?php

namespace App\Models;

use App\Traits\AppendQueryParameters;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class Batch extends Model
{
    use HasFactory;
    use AppendQueryParameters;

    const STATUS_UNCOMPLETE = 'uncomplete';
    const STATUS_COMPLETED = 'completed';

    const DEFAULT_PAGE = 1;
    const DEFAULT_PER_PAGE = 20;

    protected $casts = [
        'attachments' => 'array',
    ];

    public static $searchable = ['sender_email', 'sender_name'];

    public static $filterable = [];

    public static $sortable = [
        'created_at' => 'created_at',
        'status' => 'status',
        'sender_email' => 'sender_email',
        'sender_name' => 'sender_name',
        'recipient_count' => 'recipient_count',
        'pending_mail' => 'pending_mail'
    ];

    protected $guarded = [];

    public function mails()
    {
        return $this->hasMany(Mail::class);
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

    public static function fetch()
    {
        $query = self::where('user_id', Auth::id());
        $result = self::appendQueryOptionsToQuery($query);

        $counter = clone $result;

        return [
            'batch' => $result->get(),
            'page' => request()->get('page') ?? self::DEFAULT_PAGE,
            'total' => $counter->count(),
        ];
    }
}
