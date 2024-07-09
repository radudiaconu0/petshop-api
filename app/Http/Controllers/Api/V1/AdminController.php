<?php

namespace App\Http\Controllers\Api\V1;

use App\Helpers\ResponseHelper;
use App\Http\Controllers\Controller;
use App\Models\JWTToken;
use App\Models\User;
use App\Services\JwtService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

/**
 *
 * @OA\Tag(
 *     name="Admin",
 *     description="Admin"
 * )
 */
class AdminController extends Controller
{
    protected $jwtService;

    public function __construct(JwtService $jwtService)
    {
        $this->jwtService = $jwtService;
    }

    /**
     * @OA\Post(
     *     path="/api/v1/admin/login",
     *     summary="Admin login",
     *     tags={"Admin"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(property="email", type="string", format="email"),
     *             @OA\Property(property="password", type="string")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful login",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean"),
     *             @OA\Property(property="data", type="object",
     *                 @OA\Property(property="token", type="string")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Invalid credentials"
     *     )
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
     *     path="/api/v1/admin/logout",
     *     summary="Admin logout",
     *     tags={"Admin"},
     *     security={{"jwt": {}}},
     *     @OA\Response(
     *         response=200,
     *         description="Successful logout"
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized"
     *     )
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
     * @OA\Post(
     *     path="/api/v1/admin/create",
     *     summary="Create admin user",
     *     tags={"Admin"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(property="first_name", type="string"),
     *             @OA\Property(property="last_name", type="string"),
     *             @OA\Property(property="address", type="string"),
     *             @OA\Property(property="phone_number", type="string"),
     *             @OA\Property(property="avatar", type="string"),
     *             @OA\Property(property="email", type="string", format="email"),
     *             @OA\Property(property="password", type="string"),
     *             @OA\Property(property="password_confirmation", type="string")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Admin user created successfully"
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Validation error"
     *     )
     * )
     */
    public function createAdminUser(Request $request)
    {
        $request->validate([
            'first_name' => 'required|string',
            'last_name' => 'required|string',
            'address' => 'required|string',
            'phone_number' => 'required|string',
            'avatar' => 'required|string',
            'email' => 'required|string|email',
            'password' => 'required|string|confirmed|min:8',
        ]);

        $user = User::create([
            'email' => $request->email,
            'password' => bcrypt($request->password),
            'is_admin' => true,
        ]);

        return ResponseHelper::success($user);
    }

    /**
     * @OA\Get(
     *     path="/api/v1/admin/user-listing",
     *     summary="Get user listing",
     *     tags={"Admin"},
     *     security={{"jwt": {}}},
     *     @OA\Parameter(name="limit", in="query", @OA\Schema(type="integer")),
     *     @OA\Parameter(name="sort", in="query", @OA\Schema(type="string", enum={"asc", "desc"})),
     *     @OA\Parameter(name="sort_by", in="query", @OA\Schema(type="string")),
     *     @OA\Parameter(name="page", in="query", @OA\Schema(type="integer")),
     *     @OA\Parameter(name="email", in="query", @OA\Schema(type="string")),
     *     @OA\Parameter(name="first_name", in="query", @OA\Schema(type="string")),
     *     @OA\Parameter(name="last_name", in="query", @OA\Schema(type="string")),
     *     @OA\Parameter(name="phone_number", in="query", @OA\Schema(type="string")),
     *     @OA\Parameter(name="address", in="query", @OA\Schema(type="string")),
     *     @OA\Parameter(name="created_at", in="query", @OA\Schema(type="string", format="date")),
     *     @OA\Parameter(name="marketing", in="query", @OA\Schema(type="boolean")),
     *     @OA\Response(
     *         response=200,
     *         description="Successful response",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean"),
     *             @OA\Property(property="data", type="object",
     *                 @OA\Property(property="data", type="array", @OA\Items(ref="#/components/schemas/User")),
     *                 @OA\Property(property="current_page", type="integer"),
     *                 @OA\Property(property="last_page", type="integer"),
     *                 @OA\Property(property="per_page", type="integer"),
     *                 @OA\Property(property="total", type="integer")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized"
     *     )
     * )
     */
    public function getUsers(Request $request)
    {
        $request->validate([
            'limit' => 'integer',
            'sort' => 'string|in:asc,desc',
            'sort_by' => 'string',
            'page' => 'integer',
            'email' => 'string',
            'first_name' => 'string',
            'last_name' => 'string',
            'phone_number' => 'string',
            'address' => 'string',
            'created_at' => 'date',
            'marketing' => 'boolean',
        ]);

        $limit = $request->input('limit', 10);
        $sort = $request->input('sort', 'desc');
        $sortBy = $request->input('sort_by', 'created_at');
        $page = $request->input('page', 1);

        $query = User::query();

        if ($request->has('email')) {
            $query->where('email', 'like', '%' . $request->email . '%');
        }

        if ($request->has('first_name')) {
            $query->where('first_name', 'like', '%' . $request->first_name . '%');
        }

        if ($request->has('last_name')) {
            $query->where('last_name', 'like', '%' . $request->last_name . '%');
        }

        if ($request->has('phone_number')) {
            $query->where('phone_number', 'like', '%' . $request->phone_number . '%');
        }

        if ($request->has('address')) {
            $query->where('address', 'like', '%' . $request->address . '%');
        }

        if ($request->has('created_at')) {
            $query->whereDate('created_at', $request->created_at);
        }

        if ($request->has('marketing')) {
            $query->where('marketing', $request->marketing);
        }

        return ResponseHelper::success($query->orderBy($sortBy, $sort)->paginate($limit, ['*'], 'page', $page));
    }

    /**
     * @OA\Delete(
     *     path="/api/v1/admin/user-delete/{uuid}",
     *     summary="Delete user",
     *     tags={"Admin"},
     *     security={{"jwt": {}}},
     *     @OA\Parameter(
     *         name="uuid",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="User deleted successfully"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="User not found"
     *     )
     * )
     */
    public function deleteUser(Request $request, $uuid)
    {
        $user = User::findByUuid($uuid);

        if (!$user) {
            return ResponseHelper::error('User not found', status: 404);
        }

        $user->delete();

        return ResponseHelper::success(['message' => 'User deleted']);
    }

    /**
     * @OA\Put(
     *     path="/api/v1/admin/user-edit/{uuid}",
     *     summary="Edit user",
     *     tags={"Admin"},
     *     security={{"jwt": {}}},
     *     @OA\Parameter(
     *         name="uuid",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(property="first_name", type="string"),
     *             @OA\Property(property="last_name", type="string"),
     *             @OA\Property(property="address", type="string"),
     *             @OA\Property(property="phone_number", type="string"),
     *             @OA\Property(property="avatar", type="string"),
     *             @OA\Property(property="email", type="string", format="email"),
     *             @OA\Property(property="password", type="string"),
     *             @OA\Property(property="password_confirmation", type="string")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="User updated successfully"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="User not found"
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Validation error"
     *     )
     * )
     */
    public function editUser(Request $request, $uuid)
    {
        $user = User::findByUuid($uuid);

        if (!$user) {
            return ResponseHelper::error('User not found', status: 404);
        }

        $request->validate([
            'first_name' => 'string',
            'last_name' => 'string',
            'address' => 'string',
            'phone_number' => 'string',
            'avatar' => 'string',
            'email' => 'string|email',
            'password' => 'string|confirmed|min:8',
        ]);

        $user->update($request->all());

        return ResponseHelper::success($user);
    }
}
