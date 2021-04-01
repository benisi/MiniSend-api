<?php

namespace App\Models;

use App\Traits\AppendQueryParameters;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MailRecipient extends Model
{
    use HasFactory;
    use AppendQueryParameters;

    const STATUS_POSTED = 'posted';
    const STATUS_SENT = 'sent';
    const STATUS_FAILED = 'failed';

    const DEFAULT_PAGE = 1;
    const DEFAULT_PER_PAGE = 20;

    protected $fillable = ['sender_email', "email", "name", 'variables'];

    public static $searchable = ['mail_recipients.sender_email', 'mail_recipients.email', 'mail_recipients.subject'];
    public static $filterable = ['recipient_email' => 'email'];

    protected $casts = [
        'variables' => 'array',
    ];

    public function mail()
    {
        return $this->belongsTo(Mail::class);
    }
}
