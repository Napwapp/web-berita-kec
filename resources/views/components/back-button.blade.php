@props(['href' => '', 'class' => ''])

<a href="{{ $href }}">
    <button class="bg-green-600 hover:bg-green-700 text-white py-2 px-4 m-4 rounded {{ $class }}">
        <i class="fas fa-arrow-left"></i>
        {{$slot}}
    </button>
</a>