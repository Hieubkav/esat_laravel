<?php

namespace App\Livewire;

use App\Models\Post;
use App\Models\CatPost;
use Livewire\Component;
use Illuminate\Support\Facades\Cache;

class PostsFilter extends Component
{
    public $search = '';
    public $category = '';
    public $type = '';
    public $sort = 'newest';
    public $perPage = 12;
    public $loadedPosts = [];
    public $hasMorePosts = true;

    public $typeNames = [
        'normal' => 'Bài viết thường',
        'news' => 'Tin tức',
        'service' => 'Dịch vụ',
        'course' => 'Khóa học'
    ];

    protected $queryString = [
        'search' => ['except' => ''],
        'category' => ['except' => ''],
        'type' => ['except' => ''],
        'sort' => ['except' => 'newest'],
    ];

    public function mount()
    {
        $this->search = request('search', '');
        $this->category = request('category', '');
        $this->type = request('type', '');
        $this->sort = request('sort', 'newest');
        $this->loadPosts();
    }

    public function updatedSearch()
    {
        $this->resetPosts();
    }

    public function updatedCategory()
    {
        $this->resetPosts();
    }

    public function updatedType()
    {
        // Reset category nếu category hiện tại không thuộc type mới
        if ($this->category && $this->type) {
            $selectedCategory = CatPost::find($this->category);
            if ($selectedCategory && $selectedCategory->type !== $this->type) {
                $this->category = '';
            }
        }

        // Clear cache để load lại categories theo type mới
        $this->clearCategoriesCache();

        $this->resetPosts();
    }

    public function updatedSort()
    {
        $this->resetPosts();
    }

    public function clearFilters()
    {
        $this->search = '';
        $this->category = '';
        $this->type = '';
        $this->sort = 'newest';

        // Clear cache để load lại tất cả categories
        $this->clearCategoriesCache();

        $this->resetPosts();
    }

    private function clearCategoriesCache()
    {
        // Clear cache cho tất cả các type
        $types = ['all', 'normal', 'news', 'service', 'course'];
        foreach ($types as $type) {
            Cache::forget('posts_categories_filter_' . $type);
        }
    }

    public function loadMore()
    {
        $this->perPage += 12;
        $this->loadPosts();
    }

    private function resetPosts()
    {
        $this->perPage = 12;
        $this->loadedPosts = [];
        $this->hasMorePosts = true;
        $this->loadPosts();
    }

    private function loadPosts()
    {
        $query = $this->getQuery();

        $posts = $query->take($this->perPage)->get();
        $this->loadedPosts = $posts;

        // Check if there are more posts
        $totalPosts = $this->getQuery()->count();
        $this->hasMorePosts = $posts->count() < $totalPosts;
    }

    private function getQuery()
    {
        $query = Post::where('status', 'active')
            ->with(['categories', 'images' => function($query) {
                $query->where('status', 'active')->orderBy('order');
            }]);

        // Lọc theo chuyên mục
        if ($this->category && $this->category !== 'all') {
            $query->whereHas('categories', function($q) {
                $q->where('cat_post_id', $this->category);
            });
        }

        // Lọc theo type
        if ($this->type && in_array($this->type, ['normal', 'news', 'service', 'course'])) {
            $query->where('type', $this->type);
        }

        // Tìm kiếm theo từ khóa
        if ($this->search) {
            $query->where(function($q) {
                $q->where('title', 'like', "%{$this->search}%")
                  ->orWhere('content', 'like', "%{$this->search}%");
            });
        }

        // Sắp xếp
        switch ($this->sort) {
            case 'oldest':
                $query->orderBy('created_at', 'asc');
                break;
            case 'featured':
                $query->orderBy('is_featured', 'desc')->orderBy('created_at', 'desc');
                break;
            case 'newest':
            default:
                $query->orderBy('order')->orderBy('created_at', 'desc');
                break;
        }

        return $query;
    }

    public function getCategoriesProperty()
    {
        // Tạo cache key dựa trên type để cache riêng biệt cho từng type
        $cacheKey = 'posts_categories_filter_' . ($this->type ?: 'all');

        return Cache::remember($cacheKey, 1800, function () {
            $query = CatPost::where('status', 'active')
                ->whereNull('parent_id');

            // Lọc theo type nếu có chọn nhóm nội dung cụ thể
            if ($this->type && in_array($this->type, ['normal', 'news', 'service', 'course'])) {
                $query->where('type', $this->type);
            }

            return $query->withCount(['posts' => function($query) {
                    $query->where('status', 'active');
                    // Nếu có type được chọn, chỉ đếm posts có cùng type
                    if ($this->type && in_array($this->type, ['normal', 'news', 'service', 'course'])) {
                        $query->where('type', $this->type);
                    }
                }])
                ->orderBy('order')
                ->get();
        });
    }

    public function render()
    {
        $totalPosts = $this->getQuery()->count();

        return view('livewire.posts-filter', [
            'posts' => collect($this->loadedPosts),
            'totalPosts' => $totalPosts
        ]);
    }
}
