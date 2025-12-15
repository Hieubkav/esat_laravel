<?php

namespace App\Helpers;

use Illuminate\Support\Str;

class ContentHelper
{
    /**
     * Xử lý nội dung bài viết để tránh hiển thị HTML entities
     * 
     * @param object $post - Post object
     * @param int $limit - Giới hạn ký tự
     * @return string
     */
    public static function getCleanExcerpt($post, $limit = 150)
    {
        $content = '';
        
        // Ưu tiên lấy từ content_builder
        if (!empty($post->content_builder) && is_array($post->content_builder)) {
            foreach ($post->content_builder as $block) {
                if (isset($block['type']) && $block['type'] === 'paragraph' && isset($block['data']['content'])) {
                    $content .= strip_tags(html_entity_decode($block['data']['content'], ENT_QUOTES, 'UTF-8')) . ' ';
                }
            }
        } else {
            // Fallback về content thường
            $content = strip_tags(html_entity_decode($post->content ?? '', ENT_QUOTES, 'UTF-8'));
        }
        
        // Làm sạch nội dung
        $content = trim($content);
        $content = preg_replace('/\s+/', ' ', $content); // Loại bỏ khoảng trắng thừa
        
        return $content ? Str::limit($content, $limit) : '';
    }
}
