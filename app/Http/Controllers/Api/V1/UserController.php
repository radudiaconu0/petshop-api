<?php

namespace App\Http\Controllers\Api\V1;

use App\Helpers\ResponseHelper;
use App\Http\Controllers\Controller;
use App\Models\JWTToken;
use App\Models\Order;
use App\Models\User;
use App\Services\JwtService;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;

/**
 * @OA\SecurityScheme(
 *       securityScheme="bearerAuth",
 *       in="header",
 *       name="Authorization",
 *       type="http",
 *       scheme="Bearer",
 *       bearerFormat="JWT",
 *  ),
 */
class UserController extends Controller
{
    protected JwtService $jwtService;

    public function __construct(JwtService $jwtService)
    {
        $this->jwtService = $jwtService;
    }

    /**
     * @OA\Post(
     *     path="/api/v1/login",
     *     summary="User login",
     *     tags={"User"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"email", "password"},
     *             @OA\Property(property="email", type="string", format="email"),
     *             @OA\Property(property="password", type="string", format="password")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Login successful",
     *         @OA\JsonContent(
     *             @OA\Property(property="token", type="string")
     *         )
     *     ),
     *     @OA\Response(response=401, description="Invalid credentials")
     * )
     */
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

    /**
     * @OA\Get(
     *     path="/api/v1/logout",
     *     summary="User logout",
     *     tags={"User"},
     *     security={{"bearerAuth": {}}},
     *     @OA\Response(response=200, description="Logged out"),
     *     @OA\Response(response=401, description="Token not provided")
     * )
     */
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

    /**
     * @OA\Get(
     *     path="/api/v1/user",
     *     summary="Get authenticated user",
     *     security={{"bearerAuth": {}}},
     *     tags={"User"},
     *     @OA\Response(response=200, description="User retrieved"),
     *     @OA\Response(response=401, description="Unauthorized")
     * )
     */
    public function user(Request $request)
    {
        $user = $request->user();

        return ResponseHelper::success($user);
    }

    /**
     * @OA\Get(
     *     path="/api/v1/user/orders",
     *     summary="Get user orders",
     *     security={{"bearerAuth": {}}},
     *     tags={"User"},
     *     @OA\Response(response=200, description="Orders retrieved"),
     *     @OA\Response(response=401, description="Unauthorized")
     * )
     */
    public function getOrders(Request $request)
    {
        $request->validate([
            'page' => 'integer',
            'per_page' => 'integer',
        ]);
        $user = $request->user();

        $orders = Order::where('user_id', $user->id)->paginate($request->per_page ?? 10, ['*'], 'page', $request->page ?? 1);

        $orders->getCollection()->transform(function ($order) {
            return $order->apiObject();
        });
        return ResponseHelper::success($orders);
    }

    /**
     * @OA\Post(
     *     path="/api/v1/forgot-password",
     *     summary="Forgot password",
     *     tags={"User"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"email"},
     *             @OA\Property(property="email", type="string", format="email")
     *         )
     *     ),
     *     @OA\Response(response=200, description="Reset token generated"),
     *     @OA\Response(response=404, description="User not found")
     * )
     */
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

    /**
     * @OA\Post(
     *     path="/api/v1/reset-password",
     *     summary="Reset password",
     *     tags={"User"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"email", "token", "password", "password_confirmation"},
     *             @OA\Property(property="email", type="string", format="email"),
     *             @OA\Property(property="token", type="string"),
     *             @OA\Property(property="password", type="string", format="password"),
     *             @OA\Property(property="password_confirmation", type="string", format="password")
     *         )
     *     ),
     *     @OA\Response(response=200, description="Password reset successfully"),
     *     @OA\Response(response=400, description="Invalid token")
     * )
     */
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

    /**
     * @OA\Put(
     *     path="/api/v1/user/edit",
     *     summary="Edit user details",
     *     tags={"User"},
     *     security={{"bearerAuth": {}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"first_name", "last_name", "email", "password", "password_confirmation"},
     *             @OA\Property(property="first_name", type="string"),
     *             @OA\Property(property="last_name", type="string"),
     *             @OA\Property(property="email", type="string", format="email"),
     *             @OA\Property(property="password", type="string", format="password"),
     *             @OA\Property(property="password_confirmation", type="string", format="password"),
     *             @OA\Property(property="avatar", type="string"),
     *             @OA\Property(property="address", type="string"),
     *             @OA\Property(property="phone_number", type="string"),
     *             @OA\Property(property="is_marketing", type="boolean")
     *         )
     *     ),
     *     @OA\Response(response=200, description="User details updated"),
     *     @OA\Response(response=400, description="Invalid input")
     * )
     */
    public function editUser(Request $request)
    {
        $request->validate([
            'first_name' => 'required|string',
            'last_name' => 'required|string',
            'email' => 'required|email',
            'password' => 'required|string|min:8|confirmed',
            'avatar' => 'nullable|string',
            'address' => 'required|string',
            'phone_number' => 'required|string',
            'is_marketing' => 'boolean',
        ]);

        $user = $request->user();

        $user->update($request->only('first_name', 'last_name', 'email', 'avatar', 'address', 'phone_number', 'is_marketing'));

        return ResponseHelper::success($user);
    }


    /**
     * @OA\Post(
     *     path="/api/v1/user/create",
     *     summary="Create user",
     *     tags={"User"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"first_name", "last_name", "email", "password", "password_confirmation"},
     *             @OA\Property(property="first_name", type="string"),
     *             @OA\Property(property="last_name", type="string"),
     *             @OA\Property(property="email", type="string", format="email"),
     *             @OA\Property(property="password", type="string", format="password"),
     *             @OA\Property(property="password_confirmation", type="string", format="password"),
     *             @OA\Property(property="avatar", type="string"),
     *             @OA\Property(property="address", type="string"),
     *             @OA\Property(property="phone_number", type="string")
     *         )
     *     ),
     *     @OA\Response(response=200, description="User created"),
     *     @OA\Response(response=400, description="Invalid input")
     * )
     */

    public function createUser(Request $request)
    {
        $request->validate([
            'first_name' => 'required|string',
            'last_name' => 'required|string',
            'address' => 'required|string',
            'phone_number' => 'required|string',
            'avatar' => 'nullable|string',
            'email' => 'required|string|email',
            'password' => 'required|string|confirmed|min:8',
        ]);

        $user = User::create([
            'email' => $request->email,
            'password' => bcrypt($request->password),
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'address' => $request->address,
            'phone_number' => $request->phone_number,
            'avatar' => $request->avatar,
        ]);

        $token = $this->jwtService->issueToken($user);

        $cookie = cookie('jwt', $token, 60, '/', null, false, false, false, 'lax');

        return ResponseHelper::success(['token' => $token])->withCookie($cookie);
    }
}
