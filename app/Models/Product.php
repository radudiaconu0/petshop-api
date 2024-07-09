<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Product extends Model
{
    use HasFactory, HasUuids, SoftDeletes;

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

    public function findByUuid($uuid)
    {
        return $this->where('uuid', $uuid)->first();
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function brand()
    {
        $metadata = $this->metadata;
        $brand_uuid = $metadata['brand'];

        return Brand::findByUuid($brand_uuid);
    }

    public function getBrandAttribute()
    {
        return $this->brand();
    }

    public function apiObject()
    {
        return [
            'uuid' => $this->uuid,
            'category' => $this->category->apiObject(),
            'brand' => $this->brand->apiObject(),
            'metadata' => $this->metadata,
        ];
    }
}
