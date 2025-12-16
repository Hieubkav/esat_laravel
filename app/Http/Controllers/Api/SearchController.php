<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Post;
use Illuminate\Http\Request;

class SearchController extends Controller
{
    public function suggestions(Request $request)
    {
        $query = $request->get('q', '');
        
        if (strlen($query) < 2) {
            return response()->json(['products' => [], 'posts' => []]);
        }

        $products = Product::where('status', 'active')
            ->where('name', 'like', "%{$query}%")
            ->select('id', 'name', 'slug', 'price')
            ->with(['productImages' => fn($q) => $q->orderBy('order')->limit(1)])
            ->limit(5)
            ->get()
            ->map(fn($p) => [
                'id' => $p->id,
                'name' => $p->name,
                'slug' => $p->slug,
                'price' => $p->price,
                'thumbnail' => $p->thumbnail,
                'url' => route('products.show', $p->slug),
            ]);

        $posts = Post::where('status', 'active')
            ->where('title', 'like', "%{$query}%")
            ->select('id', 'title', 'slug', 'thumbnail')
            ->limit(5)
            ->get()
            ->map(fn($p) => [
                'id' => $p->id,
                'title' => $p->title,
                'slug' => $p->slug,
                'thumbnail' => $p->thumbnail,
                'url' => route('posts.show', $p->slug),
            ]);

        return response()->json([
            'products' => $products,
            'posts' => $posts,
        ]);
    }
}
