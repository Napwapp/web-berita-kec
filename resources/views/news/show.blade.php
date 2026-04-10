<!-- Halaman Detail berita -->
<x-app>
    <x-breadcumb :items="[
        ['label' => 'Beranda', 'url' => '/'],
        ['label' => 'Berita', 'url' => route('news.index')],
        ['label' => $news->currentVersion ? $news->currentVersion->title : 'Detail Berita']
    ]">
    </x-breadcumb>

    @if($news->currentVersion)
        <h1>{{ $news->currentVersion->title }}</h1>
        <p>{{ $news->currentVersion->content }}</p>
    @else
        <p>Berita tidak ditemukan.</p>
    @endif
</x-app>