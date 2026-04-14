<div {{ $attributes->merge(['class' => 'flex items-center w-full gap-3']) }}>
    @if(trim($slot))
        <div class="flex-1 h-px bg-gray-300"></div>
        <span class="text-xs text-gray-400 font-medium">{{ $slot }}</span>
    @endif
    <div class="flex-1 h-px bg-gray-300"></div>
</div>