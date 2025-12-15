@php
    $hasChildren = isset($node['children']) && is_array($node['children']) && count($node['children']) > 0;
    $isActive = ($current ?? '') === ($node['id'] ?? '');
    $indent = max(0, (int)($level ?? 0)) * 12;
@endphp
<div class="w-full" x-data="{ open: {{ $level < 1 ? 'true' : 'false' }} }">
    <div class="flex items-center">
        @if($hasChildren)
            <button type="button" @click="open = !open" class="mr-1 p-1 text-gray-500 hover:text-red-600" aria-label="Toggle">
                <i :class="open ? 'fas fa-chevron-down' : 'fas fa-chevron-right'"></i>
            </button>
        @else
            <span class="mr-1 w-4"></span>
        @endif
        <button wire:click="$set('category', '{{ $node['id'] }}')"
                class="filter-btn block text-left px-3 py-2 rounded-lg font-open-sans text-sm {{ $isActive ? 'active' : '' }}"
                style="padding-left: {{ $indent }}px">
            {{ $node['label'] }}
            <span class="text-gray-400 text-xs">({{ $node['total'] ?? $node['count'] ?? 0 }})</span>
        </button>
    </div>
    @if($hasChildren)
        <div class="ml-4 mt-1 space-y-1" x-show="open" x-collapse>
            @foreach($node['children'] as $child)
                @include('livewire.partials.mshopkeeper-category-node', ['node' => $child, 'level' => ($level ?? 0) + 1, 'current' => ($current ?? '')])
            @endforeach
        </div>
    @endif
</div>
