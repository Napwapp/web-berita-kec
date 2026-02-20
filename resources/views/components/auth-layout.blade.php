@props(['title' => 'Document'])

<!DOCTYPE html>
<html lang="en">

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

    <main class="flex-1 flex items-center justify-center">
        <div class="w-full max-w-md bg-white shadow-md rounded-md p-8">
            {{ $slot }}
        </div>
    </main>
</body>
</body>

</html>