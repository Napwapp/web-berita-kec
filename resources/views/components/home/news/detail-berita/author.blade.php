@props(['news'])

@php
    $author = $news->author;
    $publishedAt = optional($news->currentVersion)->published_at ?? $news->created_at;
@endphp

<div class="flex items-center text-sm text-gray-600 mb-6">
    <img alt="{{ $author?->name ?? 'Author' }}" class="w-8 h-8 rounded-full bg-gray-300 mr-3"
        src="{{ $author?->profile_photo ?? asset('images/profile-pictures/default-profile.webp') }}" />
    <span>Diupload oleh <strong class="text-gray-900 font-medium">{{ Str::limit($author?->name ?? 'Unknown Author', 20) }}</strong> <span
            class="mx-1">|</span>
        Diuplod pada {{ $publishedAt->format('F j, Y') }}</span>
    <i class="fa-solid fa-clock w-4 h-4 ml-2 text-gray-400" aria-hidden="true"></i>
</div>