<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Token extends Model
{
    use HasFactory;

    protected $fillable = ['user_id', 'name', 'token'];

    public static function getFromRequest()
    {
        $token = request()->header('authorization');

        $token = \str_replace('Bearer ', '', $token);

        return Token::where('token', $token)->first();
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
