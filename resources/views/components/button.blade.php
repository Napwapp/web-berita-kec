@props([
    'type' => 'button',
])

<button 
    type="{{ $type }}"
    {{ $attributes->merge([
        'class' => 'px-5 py-2 rounded-lg font-medium transition 
                    bg-primary text-white 
                    hover:opacity-90 
                    disabled:opacity-50 disabled:cursor-not-allowed'
    ]) }}>
    {{ $slot }}
</button>
