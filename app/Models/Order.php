<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory, HasUuids;

    protected function casts()
    {
        return [
            'uuid' => 'string',
            'products' => 'array',
            'address' => 'array',
            'shipped_at' => 'timestamp',
        ];
    }

    public function uniqueIds()
    {
        return ['uuid'];
    }
}
