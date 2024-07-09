<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Post extends Model
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

    public function apiObject()
    {
        return [
            'uuid' => $this->uuid,
            'title' => $this->title,
            'content' => $this->content,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
