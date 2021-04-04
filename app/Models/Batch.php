<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class Batch extends Model
{
    use HasFactory;

    const STATUS_UNCOMPLETE = 'uncomplete';
    const STATUS_COMPLETED = 'completed';

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
}
