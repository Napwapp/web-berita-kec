<button {{ $attributes->merge([
    'type' => 'submit',
    'class' => 'px-5 py-2 rounded-md font-medium 
        bg-gray-50 text-gray-800 border border-gray-300
        hover:bg-gray-100 hover:text-gray-900
        disabled:opacity-50 disabled:cursor-not-allowed'
]) }}>
    {{ $slot }}
</button>