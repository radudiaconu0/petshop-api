<?php

namespace App\Swagger\Schemas;

/**
 * @OA\Schema(
 *     schema="User",
 *     @OA\Property(property="uuid", type="string"),
 *     @OA\Property(property="first_name", type="string"),
 *     @OA\Property(property="last_name", type="string"),
 *     @OA\Property(property="email", type="string", format="email"),
 *     @OA\Property(property="address", type="string"),
 *     @OA\Property(property="phone_number", type="string"),
 *     @OA\Property(property="avatar", type="string"),
 *     @OA\Property(property="is_admin", type="boolean"),
 *     @OA\Property(property="marketing", type="boolean"),
 *     @OA\Property(property="created_at", type="string", format="date-time"),
 *     @OA\Property(property="updated_at", type="string", format="date-time")
 * )
 */
class User
{
}
