@props([
    'title' => 'Document',
    'heading' => 'Selamat Datang'
])

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title }}</title>
    <x-link />
</head>

</body>

<body class="min-h-dvh flex flex-col bg-gray-100 text-gray-800">
    <header>
        {{ $header ?? '' }}
    </header>

    <div class="flex-1 flex items-center justify-center">
        <div class="w-full max-w-md bg-white shadow-md rounded-md p-8 mx-4">
            {{-- Logo & Heading, tampil by default kecuali di-hide --}}
            @if(!isset($hideHeader))
                <div class="mb-4 text-center">
                    <img src="{{ asset('assets/images/logo/logo.webp') }}" alt="Logo" class="h-22 w-20 mx-auto mb-2">
                    <h1 class="text-xl font-semibold text-gray-800">{{ $heading ?? 'Selamat Datang' }}</h1>
                </div>
            @endif

            {{ $slot }}
        </div>
    </div>
</body>
</body>

</html>