<?php

namespace App\Swagger\Schemas;

/**
 * @OA\Schema(
 *     schema="Post",
 *     @OA\Property(property="uuid", type="string"),
 *     @OA\Property(property="title", type="string"),
 *     @OA\Property(property="content", type="string"),
 *     @OA\Property(property="created_at", type="string", format="date-time"),
 *     @OA\Property(property="updated_at", type="string", format="date-time")
 * )
 */
class Post
{
}
