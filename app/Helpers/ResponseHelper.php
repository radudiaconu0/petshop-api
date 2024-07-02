<?php

namespace App\Helpers;

class ResponseHelper
{
    public static function success($data = [], $extra = [], $status = 200)
    {
        return response()->json([
            'success' => 1,
            'data' => $data,
            'error' => null,
            'errors' => [],
            'extra' => $extra,
        ], $status);
    }

    public static function error($message = 'An error occurred', $errors = [], $extra = [], $status = 400)
    {
        return response()->json([
            'success' => 0,
            'data' => [],
            'error' => $message,
            'errors' => $errors,
            'extra' => $extra,
        ], $status);
    }
}
