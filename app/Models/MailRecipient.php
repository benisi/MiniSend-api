<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MailRecipient extends Model
{
    use HasFactory;

    const STATUS_POSTED = 'posted';
    const STATUS_SENT = 'sent';
    const STATUS_FAILED = 'failed';

    protected $fillable = ['sender_email', "email", "name",'variables'];

    protected $casts = [
        'variables' => 'array',
    ];

    public function mail()
    {
        return $this->belongsTo(Mail::class);
    }
}
