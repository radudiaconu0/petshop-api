<?php

namespace App\Http\Middleware;

use App\Models\User;
use App\Services\JwtService;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class JwtMiddleware
{
    protected $jwtService;

    public function __construct(JwtService $jwtService)
    {
        $this->jwtService = $jwtService;
    }

    public function handle(Request $request, Closure $next)
    {
        $jwt = $request->cookie('jwt');

        if (! $jwt) {
            return response()->json(['error' => 'Token not provided'], 401);
        }

        try {
            $isValid = $this->jwtService->validateToken($jwt);

            if (! $isValid) {
                return response()->json(['error' => 'Unauthorized'], 401);
            }

            $token = $this->jwtService->config->parser()->parse($jwt);
            $jti = $token->claims()->get('jti');
            $dbToken = DB::table('jwt_tokens')->where('unique_id', $jti)->first();

            if (! $dbToken || $dbToken->expires_at < now()) {
                return response()->json(['error' => 'Token expired or invalid'], 401);
            }

            // Set the authenticated user in the request
            $user = User::find($token->claims()->get('uid'));
            if (! $user) {
                return response()->json(['error' => 'User not found'], 401);
            }
            $request->setUserResolver(function () use ($user) {
                return $user;
            });

        } catch (\Exception $e) {
            return response()->json(['error' => 'Unauthorized: '.$e->getMessage()], 401);
        }

        return $next($request);
    }
}
