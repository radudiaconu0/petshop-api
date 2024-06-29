<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Services\JwtService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AdminController extends Controller
{
    protected $jwtService;

    public function __construct(JwtService $jwtService)
    {
        $this->jwtService = $jwtService;
    }

    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        $credentials = $request->only('email', 'password');

        if (Auth::attempt($credentials)) {
            $user = Auth::user();
            $token = $this->jwtService->generateToken(['uid' => $user->id]);

            return response()->json([
                'access_token' => $token,
                'token_type' => 'bearer',
                'expires_in' => 3600,
            ]);
        }

        return response()->json(['error' => 'Unauthorized'], 401);
    }

    public function user()
    {
        $user = auth()->user();

        return response()->json($user);
    }
}
