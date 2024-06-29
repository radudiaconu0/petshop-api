<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    use HasFactory, HasUuids;

    public function uniqueIds()
    {
        return ['uuid'];
    }

    public function getRouteKey()
    {
        return 'uuid';
    }

    public function findByUuid($uuid)
    {
        return $this->where('uuid', $uuid)->first();
    }
}
