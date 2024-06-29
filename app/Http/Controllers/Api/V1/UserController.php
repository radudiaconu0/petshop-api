<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\JWTToken;
use App\Models\User;
use App\Services\JwtService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
    protected JwtService $jwtService;

    public function __construct(JwtService $jwtService)
    {
        $this->jwtService = $jwtService;
    }

    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|string|email',
            'password' => 'required|string',
        ]);

        $user = User::where('email', $request->email)->first();

        if (! $user || ! Auth::attempt($request->only('email', 'password'))) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        $token = $this->jwtService->issueToken($user);

        $cookie = cookie('jwt', $token, 60, '/', null, true, true, false, 'None');

        return response()->json(['message' => 'Logged in successfully'])->cookie($cookie);
    }

    public function logout(Request $request)
    {
        $jwt = $request->cookie('jwt');

        if (! $jwt) {
            return response()->json(['error' => 'Token not provided'], 401);
        }

        try {
            $token = $this->jwtService->config->parser()->parse($jwt);
            $jti = $token->claims()->get('jti');

            JWTToken::where('unique_id', $jti)->delete();

            $cookie = cookie()->forget('jwt');

            return response()->json(['message' => 'Logged out successfully'])->withCookie($cookie);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Unauthorized: '.$e->getMessage()], 401);
        }
    }

    public function user(Request $request)
    {
        return response()->json($request->user());
    }
}
