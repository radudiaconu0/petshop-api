<?php

namespace App\Http\Controllers\Api\V1;

use App\Helpers\ResponseHelper;
use App\Http\Controllers\Controller;
use App\Models\JWTToken;
use App\Models\User;
use App\Services\JwtService;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;

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

        if (!$user || !Auth::attempt($request->only('email', 'password'))) {
            return ResponseHelper::error('Invalid credentials', status: 401);
        }

        $token = $this->jwtService->issueToken($user);

        $cookie = cookie('jwt', $token, 60, '/', null, false, false, false, 'lax');

        return ResponseHelper::success(['token' => $token])->withCookie($cookie);
    }

    public function logout(Request $request)
    {
        $jwt = $request->cookie('jwt');

        if (!$jwt) {
            ResponseHelper::error(['error' => 'Token not provided'], status: 401);
        }

        try {
            $token = $this->jwtService->config->parser()->parse($jwt);
            $jti = $token->claims()->get('jti');

            JWTToken::where('unique_id', $jti)->delete();

            $cookie = cookie()->forget('jwt');

            return ResponseHelper::success(['message' => 'Logged out'])->withCookie($cookie);
        } catch (\Exception $e) {
            return ResponseHelper::error('An error occurred', ['error' => $e->getMessage()]);
        }
    }

    public function user(Request $request)
    {
        $user = $request->user();

        return ResponseHelper::success($user);
    }

    public function getOrders(Request $request)
    {
        $user = $request->user();

        $orders = $user->orders()->with('order_status')->paginate(10);

        return ResponseHelper::success($orders);
    }

    public function forgotPassword(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
        ]);

        $user = User::where('email', $request->email)->first();

        if (!$user) {
            return ResponseHelper::error('Invalid e-mail', errors: ['email' => 'User not found'], status: 404);
        }

        $token = Password::createToken($user);

        return ResponseHelper::success(['reset_token' => $token]);
    }

    public function resetPassword(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'token' => 'required|string',
            'password' => 'required|string|min:8|confirmed',
        ]);

        $status = Password::reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function (User $user, string $password) {
                $user->forceFill([
                    'password' => Hash::make($password)
                ]);

                $user->save();

                event(new PasswordReset($user));
            }
        );

        if ($status !== Password::PASSWORD_RESET) {
            return ResponseHelper::error('Invalid token', errors: ['token' => 'Token is invalid'], status: 400);
        }

        return ResponseHelper::success(['message' => 'Password reset successfully']);

    }
}
