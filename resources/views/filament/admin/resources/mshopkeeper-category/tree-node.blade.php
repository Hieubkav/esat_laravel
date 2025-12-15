@php
    $hasChildren = !empty($node['children']);
    $isActive = !($node['inactive'] ?? false);
    $isLeaf = $node['is_leaf'] ?? false;
    $nodeId = $node['id'] ?? uniqid();
@endphp

<div class="tree-node group" style="margin-left: {{ $level * 24 }}px;">
    <!-- Node Container -->
    <div class="flex items-center py-3 px-4 rounded-xl transition-all duration-300 ease-in-out {{ $level === 0 ? 'bg-gradient-to-r from-amber-50/80 to-orange-50/50 dark:from-amber-900/30 dark:to-orange-900/20 border-l-4 border-amber-400 dark:border-amber-500' : '' }} hover:bg-gradient-to-r hover:from-blue-50/80 hover:to-indigo-50/50 dark:hover:from-blue-900/20 dark:hover:to-indigo-900/10 hover:shadow-sm hover:border-blue-200/50 dark:hover:border-blue-700/30 border border-transparent group-hover:scale-[1.01]">

        <!-- Tree Lines and Toggle -->
        <div class="flex items-center mr-3">
            @if($level > 0)
                <!-- Connection Lines -->
                <div class="relative mr-2">
                    <div class="absolute -left-6 top-0 w-6 h-4 border-l-2 border-b-2 border-gray-300 dark:border-gray-600 rounded-bl-md"></div>
                </div>
            @endif

            @if($hasChildren)
                <!-- Expandable Toggle -->
                <button
                    onclick="toggleNode('{{ $nodeId }}')"
                    class="flex items-center justify-center w-6 h-6 rounded-lg bg-white dark:bg-gray-700 border border-gray-200 dark:border-gray-600 hover:bg-blue-50 dark:hover:bg-blue-900/30 hover:border-blue-300 dark:hover:border-blue-600 transition-all duration-200 shadow-sm hover:shadow-md"
                >
                    <svg
                        id="icon-{{ $nodeId }}"
                        class="w-3 h-3 text-gray-600 dark:text-gray-300 transition-transform duration-200 tree-toggle-icon"
                        fill="none"
                        stroke="currentColor"
                        viewBox="0 0 24 24"
                        style="transform: rotate(90deg);"
                    >
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                    </svg>
                </button>
            @else
                <!-- Leaf Indicator -->
                <div class="w-5 h-5 flex items-center justify-center">
                    <div class="w-2 h-2 rounded-full {{ $isActive ? 'bg-green-400' : 'bg-gray-400' }}"></div>
                </div>
            @endif
        </div>

        <!-- Node Icon -->
        <div class="flex-shrink-0 mr-3">
            @if($hasChildren)
                <!-- Folder Icon -->
                <div class="w-10 h-10 rounded-xl {{ $level === 0 ? ($isActive ? 'bg-gradient-to-br from-amber-100 to-orange-200 dark:from-amber-900/50 dark:to-orange-800/30' : 'bg-gradient-to-br from-gray-100 to-gray-200 dark:from-gray-700 dark:to-gray-600') : ($isActive ? 'bg-gradient-to-br from-blue-100 to-blue-200 dark:from-blue-900/50 dark:to-blue-800/30' : 'bg-gradient-to-br from-gray-100 to-gray-200 dark:from-gray-700 dark:to-gray-600') }} flex items-center justify-center transition-all duration-300 hover:shadow-lg hover:scale-105 border border-white/50 dark:border-gray-600/50">
                    @if($level === 0)
                        <!-- Root Category Icon -->
                        <svg class="w-5 h-5 {{ $isActive ? 'text-amber-600 dark:text-amber-400' : 'text-gray-500 dark:text-gray-400' }} transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                        </svg>
                    @else
                        <!-- Sub Category Icon -->
                        <svg class="w-5 h-5 {{ $isActive ? 'text-blue-600 dark:text-blue-400' : 'text-gray-500 dark:text-gray-400' }} transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-6l-2-2H5a2 2 0 00-2 2z"/>
                        </svg>
                    @endif
                </div>
            @else
                <!-- File Icon -->
                <div class="w-10 h-10 rounded-xl {{ $isActive ? 'bg-gradient-to-br from-green-100 to-green-200 dark:from-green-900/50 dark:to-green-800/30' : 'bg-gradient-to-br from-gray-100 to-gray-200 dark:from-gray-700 dark:to-gray-600' }} flex items-center justify-center transition-all duration-300 hover:shadow-lg hover:scale-105 border border-white/50 dark:border-gray-600/50">
                    <svg class="w-5 h-5 {{ $isActive ? 'text-green-600 dark:text-green-400' : 'text-gray-500 dark:text-gray-400' }} transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                </div>
            @endif
        </div>

        <!-- Node Content -->
        <div class="flex-1 min-w-0">
            <div class="flex items-center justify-between">
                <div class="flex items-center space-x-3 min-w-0 flex-1">
                    <!-- Category Name -->
                    <h4 class="text-sm font-semibold {{ $isActive ? 'text-gray-900 dark:text-white' : 'text-gray-500 dark:text-gray-400' }} truncate">
                        @if($level === 0)
                            🏢 {{ $node['name'] ?? 'Không có tên' }}
                        @else
                            {{ $node['name'] ?? 'Không có tên' }}
                        @endif
                    </h4>

                    <!-- Category Code -->
                    @if(!empty($node['code']))
                        <span class="inline-flex items-center px-3 py-1.5 rounded-lg text-xs font-medium bg-gradient-to-r from-indigo-100 to-blue-100 dark:from-indigo-900/50 dark:to-blue-900/30 text-indigo-700 dark:text-indigo-300 border border-indigo-200 dark:border-indigo-700 transition-all duration-200 hover:shadow-md hover:scale-105 hover:from-indigo-200 hover:to-blue-200 dark:hover:from-indigo-800/60 dark:hover:to-blue-800/40">
                            <svg class="w-3 h-3 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/>
                            </svg>
                            {{ $node['code'] }}
                        </span>
                    @endif
                </div>

                <!-- Badges Container -->
                <div class="flex items-center space-x-2 flex-shrink-0">
                    <!-- Status Badge -->
                    <span class="inline-flex items-center px-3 py-1.5 rounded-full text-xs font-medium transition-all duration-200 {{
                        $isActive
                            ? 'bg-gradient-to-r from-green-100 to-emerald-100 dark:from-green-900/50 dark:to-emerald-900/30 text-green-700 dark:text-green-300 border border-green-200 dark:border-green-700 hover:shadow-md hover:scale-105'
                            : 'bg-gradient-to-r from-red-100 to-rose-100 dark:from-red-900/50 dark:to-rose-900/30 text-red-700 dark:text-red-300 border border-red-200 dark:border-red-700 hover:shadow-md hover:scale-105'
                    }}">
                        <div class="w-2 h-2 rounded-full {{ $isActive ? 'bg-green-500 animate-pulse' : 'bg-red-500' }} mr-2"></div>
                        {{ $isActive ? 'Hoạt động' : 'Tạm dừng' }}
                    </span>

                    <!-- Type Badge -->
                    <span class="inline-flex items-center px-3 py-1.5 rounded-full text-xs font-medium transition-all duration-200 {{
                        $isLeaf
                            ? 'bg-gradient-to-r from-blue-100 to-cyan-100 dark:from-blue-900/50 dark:to-cyan-900/30 text-blue-700 dark:text-blue-300 border border-blue-200 dark:border-blue-700 hover:shadow-md hover:scale-105'
                            : 'bg-gradient-to-r from-purple-100 to-violet-100 dark:from-purple-900/50 dark:to-violet-900/30 text-purple-700 dark:text-purple-300 border border-purple-200 dark:border-purple-700 hover:shadow-md hover:scale-105'
                    }}">
                        @if($isLeaf)
                            <svg class="w-3 h-3 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                            </svg>
                            Sản phẩm
                        @else
                            <svg class="w-3 h-3 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-6l-2-2H5a2 2 0 00-2 2z"/>
                            </svg>
                            @if($level === 0)
                                Danh mục gốc
                            @else
                                Danh mục con
                            @endif
                        @endif
                    </span>

                    <!-- Grade Badge -->
                    <span class="inline-flex items-center px-3 py-1.5 rounded-lg text-xs font-medium bg-gradient-to-r from-gray-100 to-slate-100 dark:from-gray-700 dark:to-slate-700 text-gray-600 dark:text-gray-300 border border-gray-200 dark:border-gray-600 transition-all duration-200 hover:shadow-md hover:scale-105">
                        <svg class="w-3 h-3 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16V4m0 0L3 8m4-4l4 4m6 0v12m0 0l4-4m-4 4l-4-4"/>
                        </svg>
                        Cấp {{ $node['grade'] ?? 0 }}
                    </span>
                </div>
            </div>

            <!-- Description -->
            @if(!empty($node['description']))
                <div class="mt-2 flex items-start space-x-2">
                    <svg class="w-3 h-3 text-gray-400 dark:text-gray-500 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    <p class="text-xs text-gray-500 dark:text-gray-400 leading-relaxed">
                        {{ $node['description'] }}
                    </p>
                </div>
            @endif

            <!-- Children Count (if has children) -->
            @if($hasChildren)
                <div class="mt-2 flex items-center space-x-1 text-xs text-gray-500 dark:text-gray-400">
                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14-7l-7 7-7-7m14 18l-7-7-7 7"/>
                    </svg>
                    <span>{{ count($node['children']) }} danh mục con</span>
                </div>
            @endif
        </div>
    </div>
</div>

<!-- Children -->
@if($hasChildren)
    <div id="children-{{ $nodeId }}" class="tree-node-children" style="display: block;">
        @foreach($node['children'] as $child)
            @include('filament.admin.resources.mshopkeeper-category.tree-node', [
                'node' => $child,
                'level' => $level + 1
            ])
        @endforeach
    </div>
@endif
