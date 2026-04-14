@props(['title' => 'Document', 'showNavbar' => true])

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    @yield('meta')

    <x-link />
</head>

<body class="bg-gray-50 text-gray-800 ">
    <div class="container mx-auto px-4 max-w-5xl">
        @if ($showNavbar)
            <!-- Navbar -->
            <x-navbar />
        @endif

        <main class="mb-10">
            <x-flash-messages />

            <!-- Konten utama -->
            <main class="bg-white rounded-lg shadow p-6">
                {{$slot}}
            </main>
        </main>

    </div>
    <x-footer />
</body>

</html>