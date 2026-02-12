@props(['title' => 'Document'])

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{$title}}</title>

    <x-link />
    
</head>

<body class="bg-gray-50 text-gray-800">
    <div class="container mx-auto px-4 max-w-4xl">
        <!-- Navbar -->
        <x-navbar />

        <main class="mb-10">
            <!-- Header -->
            <x-header />

            <!-- Konten utama -->
            <div class="bg-white rounded-lg shadow p-6">
                {{$slot}}
            </div>
        </main>
    </div>
</body>
</html>