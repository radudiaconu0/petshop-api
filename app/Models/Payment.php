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

    public function findByUuid($uuid)
    {
        return $this->where('uuid', $uuid)->first();
    }

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function apiObject()
    {
        return [
            'uuid' => $this->uuid,
            'type' => $this->type,
            'details' => $this->details,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
