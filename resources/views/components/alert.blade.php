@props([
    'type' => 'info',
    'message' => null,
])

@php
$baseclass = 'alert-component relative flex items-start gap-3 rounded-lg p-4 text-sm border mt-4 max-w-md mx-auto transition-opacity duration-500';

$types = [
    'success' => [
        'class' => 'bg-green-100 border-green-500 text-green-700',
        'icon' => 'fa-circle-check'
    ],
    'error' => [
        'class' => 'bg-red-100 border-red-500 text-red-700',
        'icon' => 'fa-circle-xmark'
    ],
    'warning' => [
        'class' => 'bg-yellow-100 border-yellow-500 text-yellow-700',
        'icon' => 'fa-triangle-exclamation'
    ],
    'info' => [
        'class' => 'bg-blue-100 border-blue-500 text-blue-700',
        'icon' => 'fa-circle-info'
    ],
];

$config = $types[$type] ?? $types['info'];

$class = $baseclass . ' ' . $config['class'];
$icon = $config['icon'];
@endphp

@if ($message)
    <div {{ $attributes->merge(['class' => $class]) }} role="alert">
        <!-- Icon -->
        <div class="flex-shrink-0 mt-0.5">
            <i class="fa-solid {{ $icon }} text-base"></i>
        </div>

        <div class="flex-1">
            {!! $message !!}
        </div>

        <!-- Tombol close -->
        <button
            type="button"
            onclick="closeAlert(this)"
            class="flex-shrink-0 opacity-70 hover:opacity-100 transition"
        >
            <i class="fa-solid fa-xmark"></i>
        </button>
    </div>
@endif

<script>
    function closeAlert(button){
        const alert = button.closest('.alert-component');
        alert.style.opacity = '0';

        alert.remove();
    }
</script>
