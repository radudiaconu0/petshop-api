<?php

namespace App\Swagger\Schemas;

/**
 * @OA\Schema(
 *     schema="Promotion",
 *     @OA\Property(property="uuid", type="integer"),
 *     @OA\Property(property="title", type="string"),
 *     @OA\Property(property="content", type="string"),
 *     @OA\Property(property="metadata", type="json"),
 *     @OA\Property(property="created_at", type="string", format="date-time"),
 *     @OA\Property(property="updated_at", type="string", format="date-time")
 * )
 */
class Promotion
{

}
