<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class JWTToken extends Model
{
    protected $table = 'jwt_tokens';

    protected function casts()
    {
        return [
            'restrictions' => 'array',
            'permissions' => 'array',
            'expires_at' => 'timestamp',
            'last_used_at' => 'timestamp',
            'refreshed_at' => 'timestamp',
        ];
    }

    protected $fillable = [
        'user_id',
        'unique_id',
        'token_title', // 'Login Token
        'restrictions',
        'permissions',
        'expires_at',
        'last_used_at',
        'refreshed_at',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
