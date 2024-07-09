<?php

namespace App\Http\Controllers\Api\V1;

use App\Helpers\ResponseHelper;
use App\Http\Controllers\Controller;
use App\Models\Post;
use App\Models\Promotion;
use Illuminate\Http\Request;


/**
 *
 * @OA\Tag(
 *     name="Main Page",
 *     description="Main page"
 * )

 */
class MainPageController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/v1/main/blog",
     *     summary="Get blog posts",
     *     description="Retrieve a paginated list of blog posts",
     *     tags={"Main Page"},
     *     @OA\Parameter(
     *         name="limit",
     *         in="query",
     *         description="Number of items per page",
     *         @OA\Schema(type="integer", default=10)
     *     ),
     *     @OA\Parameter(
     *         name="sort",
     *         in="query",
     *         description="Sort order",
     *         @OA\Schema(type="string", enum={"asc", "desc"}, default="desc")
     *     ),
     *     @OA\Parameter(
     *         name="sort_by",
     *         in="query",
     *         description="Field to sort by",
     *         @OA\Schema(type="string", default="created_at")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful response",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean"),
     *             @OA\Property(property="data", type="object",
     *                 @OA\Property(property="data", type="array", @OA\Items(ref="#/components/schemas/Post")),
     *                 @OA\Property(property="current_page", type="integer"),
     *                 @OA\Property(property="last_page", type="integer"),
     *                 @OA\Property(property="per_page", type="integer"),
     *                 @OA\Property(property="total", type="integer")
     *             )
     *         )
     *     )
     * )
     */
    public function getBlogPosts(Request $request)
    {
        $limit = $request->input('limit', 10);
        $sort = $request->input('sort', 'desc');
        $sortBy = $request->input('sort_by', 'created_at');

        return ResponseHelper::success(Post::orderBy($sortBy, $sort)->paginate($limit));
    }

    /**
     * @OA\Get(
     *     path="/api/v1/main/promotions",
     *     summary="Get promotions",
     *     description="Retrieve a paginated list of promotions",
     *     tags={"Main Page"},
     *     @OA\Parameter(
     *         name="limit",
     *         in="query",
     *         description="Number of items per page",
     *         @OA\Schema(type="integer", default=10)
     *     ),
     *     @OA\Parameter(
     *         name="sort",
     *         in="query",
     *         description="Sort order",
     *         @OA\Schema(type="string", enum={"asc", "desc"}, default="desc")
     *     ),
     *     @OA\Parameter(
     *         name="sort_by",
     *         in="query",
     *         description="Field to sort by",
     *         @OA\Schema(type="string", default="created_at")
     *     ),
     *     @OA\Parameter(
     *         name="page",
     *         in="query",
     *         description="Page number",
     *         @OA\Schema(type="integer", default=1)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful response",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean"),
     *             @OA\Property(property="data", type="object",
     *                 @OA\Property(property="data", type="array", @OA\Items(ref="#/components/schemas/Promotion")),
     *                 @OA\Property(property="current_page", type="integer"),
     *                 @OA\Property(property="last_page", type="integer"),
     *                 @OA\Property(property="per_page", type="integer"),
     *                 @OA\Property(property="total", type="integer")
     *             )
     *         )
     *     )
     * )
     */
    public function getPromotions(Request $request)
    {
        $limit = $request->input('limit', 10);
        $sort = $request->input('sort', 'desc');
        $sortBy = $request->input('sort_by', 'created_at');
        $page = $request->input('page', 1);

        return ResponseHelper::success(Promotion::orderBy($sortBy, $sort)->paginate($limit, ['*'], 'page', $page));
    }

    /**
     * @OA\Get(
     *     path="/api/v1/main/blog/{uuid}",
     *     summary="Get post",
     *     description="Retrieve a single post",
     *     tags={"Main Page"},
     *     @OA\Parameter(
     *         name="uuid",
     *         in="path",
     *         description="Post UUID",
     *         required=true,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful response",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean"),
     *             @OA\Property(property="data", ref="#/components/schemas/Post")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Post not found",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean"),
     *             @OA\Property(property="message", type="string")
     *         )
     *     )
     * )
     */
    public function getPost(string $uuid)
    {
        $post = Post::findByUuid($uuid);

        if (!$post) {
            return ResponseHelper::error('Post not found', status: 404);
        }

        return ResponseHelper::success($post->apiObject());
    }
}
