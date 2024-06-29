<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class File extends Model
{
    use HasFactory, HasUuids;

    protected function casts()
    {
        return [
            'uuid' => 'string',
        ];
    }

    public function uniqueIds()
    {
        return ['uuid'];
    }

    public function findByUuid($uuid)
    {
        return $this->where('uuid', $uuid)->first();
    }
}
