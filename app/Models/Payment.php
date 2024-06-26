<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    use HasFactory, HasUuids;

    protected function casts()
    {
        return [
            'uuid' => 'string',
            'details' => 'array',
        ];
    }

    public function uniqueIds()
    {
        return ['uuid'];
    }
}
