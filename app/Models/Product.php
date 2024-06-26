<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Product extends Model
{
    use SoftDeletes, HasFactory, HasUuids;

    protected function casts()
    {
        return [
            'category_uuid' => 'string',
            'uuid' => 'string',
            'metadata' => 'array',
        ];
    }

    public function uniqueIds()
    {
        return ['uuid'];
    }
}
