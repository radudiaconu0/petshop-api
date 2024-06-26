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


}
