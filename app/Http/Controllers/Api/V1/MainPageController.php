<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Post;
use App\Models\Promotion;

class MainPageController extends Controller
{
    public function getBlogPosts()
    {
        return Post::orderBy('created_at', 'desc')->paginate(10);
    }

    public function getPromotions()
    {
        return Promotion::paginate(20);
    }
}
