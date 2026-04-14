@php([
    $content = $news->currentVersion,
])

<!-- Content Berita -->
<div class="prose max-w-none text-gray-700 leading-relaxed space-y-4 mb-8">
    {!! $content->content !!}
</div>