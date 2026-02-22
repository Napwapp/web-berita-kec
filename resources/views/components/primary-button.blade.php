<button {{ $attributes->merge([
    'type' => 'submit',
    'class' => 'px-5 py-2 rounded-md font-medium 
        bg-primary text-white 
        hover:opacity-90 
        disabled:opacity-50 disabled:cursor-not-allowed'
]) }}>
    {{ $slot }}
</button>