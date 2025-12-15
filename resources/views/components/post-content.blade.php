@props(['post'])

@php
    $hasBuilderContent = !empty($post->content_builder) && is_array($post->content_builder);
    // Debug: uncomment để debug
    // if(request()->has('debug')) {
    //     dd(['hasBuilderContent' => $hasBuilderContent, 'content_builder' => $post->content_builder, 'content' => $post->content]);
    // }
@endphp

@if($hasBuilderContent)
    <div class="builder-content space-y-8">
        @foreach($post->content_builder as $block)
            @if(isset($block['type']) && isset($block['data']))
                @switch($block['type'])
                    @case('paragraph')
                        <div class="prose prose-lg max-w-none">{!! $block['data']['content'] ?? '' !!}</div>
                        @break

                    @case('heading')
                        @php
                            $level = $block['data']['level'] ?? 'h2';
                            $text = $block['data']['text'] ?? '';
                        @endphp
                        @if($level === 'h2')
                            <h2 class="text-3xl font-bold text-gray-900 mt-12 mb-6">{{ $text }}</h2>
                        @elseif($level === 'h3')
                            <h3 class="text-2xl font-semibold text-gray-800 mt-10 mb-4">{{ $text }}</h3>
                        @elseif($level === 'h4')
                            <h4 class="text-xl font-medium text-gray-700 mt-8 mb-3">{{ $text }}</h4>
                        @endif
                        @break

                    @case('image')
                        <div class="my-8">
                            @if(!empty($block['data']['image']))
                                @php
                                    $alignment = $block['data']['alignment'] ?? 'center';
                                    $alignClass = match($alignment) {
                                        'left' => 'text-left',
                                        'right' => 'text-right',
                                        default => 'text-center'
                                    };
                                @endphp
                                <div class="{{ $alignClass }}">
                                    <img src="{{ asset('storage/' . $block['data']['image']) }}"
                                         alt="{{ $block['data']['alt'] ?? '' }}"
                                         class="max-w-full h-auto rounded-lg shadow-lg {{ $alignment === 'center' ? 'mx-auto' : '' }}"
                                         loading="lazy">
                                    @if(!empty($block['data']['caption']))
                                        <p class="text-sm text-gray-600 mt-3 italic">{{ $block['data']['caption'] }}</p>
                                    @endif
                                </div>
                            @endif
                        </div>
                        @break

                    @case('gallery')
                        <div class="my-8">
                            @if(!empty($block['data']['images']) && is_array($block['data']['images']))
                                @php
                                    $columns = $block['data']['columns'] ?? '3';
                                    $gridClass = match($columns) {
                                        '2' => 'grid-cols-1 md:grid-cols-2',
                                        '4' => 'grid-cols-1 md:grid-cols-2 lg:grid-cols-4',
                                        default => 'grid-cols-1 md:grid-cols-2 lg:grid-cols-3'
                                    };
                                @endphp
                                <div class="grid {{ $gridClass }} gap-4">
                                    @foreach($block['data']['images'] as $image)
                                        <div class="aspect-square overflow-hidden rounded-lg">
                                            <img src="{{ asset('storage/' . $image) }}"
                                                 alt="Gallery image"
                                                 class="w-full h-full object-cover hover:scale-105 transition-transform duration-300"
                                                 loading="lazy">
                                        </div>
                                    @endforeach
                                </div>
                                @if(!empty($block['data']['caption']))
                                    <p class="text-sm text-gray-600 mt-4 text-center italic">{{ $block['data']['caption'] }}</p>
                                @endif
                            @endif
                        </div>
                        @break

                    @case('quote')
                        <div class="my-8">
                            <blockquote class="border-l-4 border-red-500 bg-red-50 p-6 rounded-r-lg">
                                <p class="text-lg italic text-gray-800 mb-4">"{{ $block['data']['content'] ?? '' }}"</p>
                                @if(!empty($block['data']['author']) || !empty($block['data']['source']))
                                    <footer class="text-sm text-gray-600">
                                        @if(!empty($block['data']['author']))<cite class="font-medium">— {{ $block['data']['author'] }}</cite>@endif
                                        @if(!empty($block['data']['source']))<span class="ml-2">({{ $block['data']['source'] }})</span>@endif
                                    </footer>
                                @endif
                            </blockquote>
                        </div>
                        @break

                    @case('video')
                        <div class="my-8">
                            @if($block['data']['type'] === 'youtube' && !empty($block['data']['url']))
                                @php preg_match('/(?:youtube\.com\/watch\?v=|youtu\.be\/)([^&\n?#]+)/', $block['data']['url'], $matches); $videoId = $matches[1] ?? ''; @endphp
                                @if($videoId)<div class="aspect-video"><iframe src="https://www.youtube.com/embed/{{ $videoId }}" class="w-full h-full rounded-lg" frameborder="0" allowfullscreen></iframe></div>@endif
                            @elseif($block['data']['type'] === 'vimeo' && !empty($block['data']['url']))
                                @php preg_match('/vimeo\.com\/(\d+)/', $block['data']['url'], $matches); $videoId = $matches[1] ?? ''; @endphp
                                @if($videoId)<div class="aspect-video"><iframe src="https://player.vimeo.com/video/{{ $videoId }}" class="w-full h-full rounded-lg" frameborder="0" allowfullscreen></iframe></div>@endif
                            @elseif($block['data']['type'] === 'upload' && !empty($block['data']['file']))
                                <div class="aspect-video"><video controls class="w-full h-full rounded-lg"><source src="{{ asset('storage/' . $block['data']['file']) }}" type="video/mp4">Trình duyệt của bạn không hỗ trợ video.</video></div>
                            @endif
                            @if(!empty($block['data']['caption']))<p class="text-sm text-gray-600 mt-3 text-center italic">{{ $block['data']['caption'] }}</p>@endif
                        </div>
                        @break

                    @case('audio')
                        <div class="my-8">
                            @if(!empty($block['data']['file']))
                                <div class="bg-gray-50 p-6 rounded-lg">
                                    @if(!empty($block['data']['title']))
                                        <h4 class="font-semibold text-gray-900 mb-2">{{ $block['data']['title'] }}</h4>
                                    @endif
                                    @if(!empty($block['data']['artist']))
                                        <p class="text-sm text-gray-600 mb-4">{{ $block['data']['artist'] }}</p>
                                    @endif
                                    <audio controls class="w-full">
                                        <source src="{{ asset('storage/' . $block['data']['file']) }}" type="audio/mpeg">
                                        Trình duyệt của bạn không hỗ trợ audio.
                                    </audio>
                                    @if(!empty($block['data']['caption']))
                                        <p class="text-sm text-gray-600 mt-3 italic">{{ $block['data']['caption'] }}</p>
                                    @endif
                                </div>
                            @endif
                        </div>
                        @break

                    @case('code')
                        <div class="my-8">
                            @if(!empty($block['data']['content']))
                                <div class="bg-gray-900 rounded-lg overflow-hidden">
                                    @if(!empty($block['data']['title']))
                                        <div class="bg-gray-800 px-4 py-2 text-sm text-gray-300">{{ $block['data']['title'] }}</div>
                                    @endif
                                    <pre class="p-4 text-sm text-gray-100 overflow-x-auto"><code>{{ $block['data']['content'] }}</code></pre>
                                </div>
                            @endif
                        </div>
                        @break

                    @case('list')
                        <div class="my-8">
                            @if(!empty($block['data']['items']))
                                @if(!empty($block['data']['title']))
                                    <h4 class="font-semibold text-gray-900 mb-4">{{ $block['data']['title'] }}</h4>
                                @endif
                                @php
                                    $items = explode("\n", $block['data']['items']);
                                    $type = $block['data']['type'] ?? 'bullet';
                                @endphp
                                @if($type === 'numbered')
                                    <ol class="list-decimal list-inside space-y-2">
                                        @foreach($items as $item)
                                            @if(trim($item))<li class="text-gray-700">{{ trim($item) }}</li>@endif
                                        @endforeach
                                    </ol>
                                @elseif($type === 'checklist')
                                    <ul class="space-y-2">
                                        @foreach($items as $item)
                                            @if(trim($item))<li class="flex items-center"><span class="text-green-500 mr-2">✓</span><span class="text-gray-700">{{ trim($item) }}</span></li>@endif
                                        @endforeach
                                    </ul>
                                @else
                                    <ul class="list-disc list-inside space-y-2">
                                        @foreach($items as $item)
                                            @if(trim($item))<li class="text-gray-700">{{ trim($item) }}</li>@endif
                                        @endforeach
                                    </ul>
                                @endif
                            @endif
                        </div>
                        @break

                    @case('cta')
                        <div class="my-8">
                            @if(!empty($block['data']['title']) && !empty($block['data']['button_text']) && !empty($block['data']['button_url']))
                                @php
                                    $style = $block['data']['style'] ?? 'primary';
                                    $size = $block['data']['size'] ?? 'medium';
                                    $buttonClass = match($style) {
                                        'secondary' => 'bg-gray-600 hover:bg-gray-700',
                                        'success' => 'bg-green-600 hover:bg-green-700',
                                        'warning' => 'bg-yellow-600 hover:bg-yellow-700',
                                        'danger' => 'bg-red-600 hover:bg-red-700',
                                        default => 'bg-red-600 hover:bg-red-700'
                                    };
                                    $sizeClass = match($size) {
                                        'small' => 'px-4 py-2 text-sm',
                                        'large' => 'px-8 py-4 text-lg',
                                        default => 'px-6 py-3'
                                    };
                                @endphp
                                <div class="bg-gray-50 p-8 rounded-lg text-center">
                                    <h4 class="text-2xl font-bold text-gray-900 mb-4">{{ $block['data']['title'] }}</h4>
                                    @if(!empty($block['data']['description']))
                                        <p class="text-gray-600 mb-6">{{ $block['data']['description'] }}</p>
                                    @endif
                                    <a href="{{ $block['data']['button_url'] }}"
                                       class="inline-block {{ $buttonClass }} {{ $sizeClass }} text-white font-medium rounded-lg transition-colors duration-300">
                                        {{ $block['data']['button_text'] }}
                                    </a>
                                </div>
                            @endif
                        </div>
                        @break

                    @case('divider')
                        <div class="my-8">
                            @php $style = $block['data']['style'] ?? 'solid'; $borderClass = match($style) { 'dashed' => 'border-dashed', 'dotted' => 'border-dotted', 'double' => 'border-double border-t-4', default => 'border-solid' }; @endphp
                            <hr class="border-gray-300 {{ $borderClass }}">
                        </div>
                        @break

                    @case('google_map')
                        <div class="my-8">
                            @if(!empty($block['data']['title']))
                                <h4 class="text-xl font-semibold text-gray-900 mb-4 text-center">{{ $block['data']['title'] }}</h4>
                            @endif
                            @if(!empty($block['data']['embed_code']))
                                @php
                                    $height = $block['data']['height'] ?? '400';
                                    // Làm sạch và xử lý mã embed
                                    $embedCode = $block['data']['embed_code'];
                                    // Thêm responsive classes và custom height
                                    $embedCode = str_replace(
                                        ['width="600"', 'height="450"', 'style="border:0;"'],
                                        ['width="100%"', 'height="' . $height . '"', 'style="border:0; border-radius: 12px;"'],
                                        $embedCode
                                    );
                                @endphp
                                <div class="relative overflow-hidden rounded-xl shadow-lg bg-gray-100">
                                    <div style="height: {{ $height }}px;">
                                        {!! $embedCode !!}
                                    </div>
                                </div>
                            @else
                                <div class="bg-gray-100 rounded-xl p-8 text-center">
                                    <div class="text-gray-400 mb-2">
                                        <svg class="w-12 h-12 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                        </svg>
                                    </div>
                                    <p class="text-gray-500">Chưa có mã nhúng Google Maps</p>
                                </div>
                            @endif
                        </div>
                        @break

                    @default
                        {{-- Debug: hiển thị block type chưa được hỗ trợ --}}
                        <div class="my-4 p-4 bg-yellow-50 border border-yellow-200 rounded-lg">
                            <p class="text-sm text-yellow-800">Block type chưa được hỗ trợ: <strong>{{ $block['type'] ?? 'unknown' }}</strong></p>
                            @if(config('app.debug'))
                                <pre class="text-xs text-yellow-700 mt-2">{{ json_encode($block, JSON_PRETTY_PRINT) }}</pre>
                            @endif
                        </div>
                @endswitch
            @endif
        @endforeach
    </div>
@else
    {{-- Fallback: sử dụng content truyền thống --}}
    @if(!empty($post->content))
        <div class="prose prose-lg max-w-none">{!! $post->content !!}</div>
    @else
        <div class="text-center py-12 text-gray-500">
            <i class="fas fa-file-alt text-4xl mb-4"></i>
            <p>Nội dung đang được cập nhật...</p>
            @if(config('app.debug'))
                <div class="mt-4 p-4 bg-yellow-50 border border-yellow-200 rounded text-left text-sm">
                    <strong>Debug Info:</strong><br>
                    Content Builder: {{ $post->content_builder ? 'Có dữ liệu' : 'Trống' }}<br>
                    Content: {{ $post->content ? 'Có dữ liệu (' . strlen($post->content) . ' ký tự)' : 'Trống' }}<br>
                    Builder Type: {{ gettype($post->content_builder) }}<br>
                    Builder Count: {{ is_array($post->content_builder) ? count($post->content_builder) : 'N/A' }}
                </div>
            @endif
        </div>
    @endif
@endif
