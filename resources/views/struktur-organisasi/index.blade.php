<x-app title="Struktur Organisasi Kecamatan Binong">
    <div class="min-h-screen bg-gray-50 py-10 px-4 sm:px-6 lg:px-8">

        {{-- Heading Section --}}
        <div class="max-w-5xl mx-auto mb-8 text-center">
            <h1 class="text-3xl sm:text-4xl font-bold text-gray-800 mb-3">
                Struktur Organisasi Kecamatan Binong
            </h1>
            <p class="text-gray-500 text-sm sm:text-base max-w-xl mx-auto">
                Bagan susunan pengurus dan jabatan organisasi secara resmi
            </p>
            {{-- Divider accent --}}
            <div class="flex items-center justify-center gap-2 mt-4">
                <div class="h-px w-12 bg-gray-300"></div>
                <div class="h-1.5 w-8 rounded-full bg-green-500"></div>
                <div class="h-px w-12 bg-gray-300"></div>
            </div>
        </div>

        {{-- Main Card --}}
        <div class="max-w-5xl mx-auto">
            <div class="bg-white rounded-2xl shadow-sm border border-gray-200 overflow-hidden">

                {{-- Card Header --}}
                <div
                    class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 px-5 py-4 bg-gray-50 border-b border-gray-200">
                    <div class="flex items-center gap-2">
                        <span class="inline-block w-2.5 h-2.5 rounded-full bg-green-500"></span>
                        <span class="text-sm font-medium text-gray-600">Bagan Struktur Organisasi</span>
                    </div>

                    <div class="flex items-center gap-2">
                        {{-- Form Upload/Update Gambar struktur organisasi --}}
                        @auth
                            @if(auth()->user()->role === 'admin')
                                <form action="{{ route('admin.struktur-organisasi.store') }}" method="POST"
                                    enctype="multipart/form-data" class="flex items-center gap-2" id="form-upload">
                                    @csrf

                                    <label for="gambar"
                                        class="inline-flex items-center gap-2 px-4 py-2 text-sm text-white bg-green-600 rounded-lg hover:bg-green-700 transition-all duration-200 cursor-pointer">
                                        <i class="fas fa-upload"></i>
                                        {{ $gambar ? 'Update Gambar' : 'Upload Gambar' }}
                                    </label>

                                    <input type="file" name="gambar" id="gambar" accept=".jpg,.jpeg,.png,.webp,.svg"
                                        class="hidden" onchange="document.getElementById('form-upload').submit()" />

                                    <button type="submit" id="btn-submit"
                                        class="hidden items-center gap-2 px-4 py-2 text-sm text-white bg-blue-600 rounded-lg hover:bg-blue-700 transition-all duration-200">
                                        <i class="fas fa-check"></i>
                                        Simpan
                                    </button>
                                </form>
                            @endif
                        @endauth

                        {{-- Tombol Unduh --}}
                        @if(isset($gambar) && $gambar)
                            <a href="{{ asset('storage/' . $gambar) }}" download
                                class="inline-flex items-center gap-2 px-4 py-2 text-sm text-gray-600 border border-gray-300 rounded-lg hover:bg-gray-100 hover:text-gray-800 transition-all duration-200">
                                <i class="fas fa-download"></i>
                                Unduh Gambar
                            </a>

                            {{-- Tombol Hapus --}}
                            @auth
                                @if(auth()->user()->role === 'admin')
                                    <form action="{{ route('admin.struktur-organisasi.destroy') }}" method="POST"
                                        onsubmit="return confirm('Yakin ingin menghapus gambar ini? Tindakan tidak dapat dibatalkan.')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="inline-flex items-center gap-2 px-4 py-2 text-sm text-red-600
                                                        border border-red-300 rounded-lg hover:bg-red-50 hover:text-red-700 transition-all
                                                        duration-200">
                                            <i class="fas fa-trash"></i>
                                            Hapus Gambar
                                        </button>
                                    </form>
                                @endif
                            @endauth
                        @endif
                    </div>
                </div>

                {{-- Validasi error --}}
                {{-- Validasi error --}}
                @error('gambar')
                    <div x-data="{ show: true }" x-show="show"
                        class="px-5 py-3 bg-red-50 border-b border-red-200 text-red-600 text-sm flex items-center justify-between gap-2">
                        <div class="flex items-center gap-2">
                            <i class="fas fa-circle-exclamation shrink-0"></i>
                            {{ $message }}
                        </div>
                        <button type="button" @click="show = false"
                            class="text-red-400 hover:text-red-600 transition-colors duration-200">
                            <i class="fas fa-xmark"></i>
                        </button>
                    </div>
                @enderror

                {{-- Success message --}}
                @if(session('success'))
                    <div x-data="{ show: true }" x-show="show"
                        class="px-5 py-3 bg-green-50 border-b border-green-200 text-green-700 text-sm flex items-center justify-between gap-2">
                        <div class="flex items-center gap-2">
                            <i class="fas fa-check shrink-0"></i>
                            {{ session('success') }}
                        </div>
                        <button type="button" @click="show = false"
                            class="text-green-400 hover:text-green-600 transition-colors duration-200">
                            <i class="fas fa-xmark"></i>
                        </button>
                    </div>
                @endif

                {{-- Image Area --}}
                <div class="p-4 sm:p-6 bg-gray-50">
                    @if(isset($gambar) && $gambar)
                        {{-- Trigger: klik gambar untuk zoom --}}
                        <a href="#lightbox" class="relative group cursor-zoom-in block">
                            <img src="{{ asset('storage/' . $gambar) }}" alt="Struktur Organisasi"
                                class="w-full h-auto rounded-xl border border-gray-200 object-contain transition-transform duration-300 group-hover:scale-[1.005]" />
                            <div
                                class="absolute inset-0 flex items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity duration-200 pointer-events-none">
                                <div
                                    class="bg-black/50 text-white text-xs font-medium px-3 py-1.5 rounded-full flex items-center gap-1.5">
                                    <i class="fas fa-magnifying-glass-plus"></i>
                                    Klik untuk perbesar
                                </div>
                            </div>
                        </a>

                        {{-- CSS-only Lightbox --}}
                        <div id="lightbox" class="fixed inset-0 z-50 items-center justify-center bg-black/85 p-4
                                        invisible opacity-0 transition-all duration-200
                                        target:visible target:opacity-100 target:flex">

                            {{-- Backdrop tutup --}}
                            <a href="#" class="absolute inset-0" aria-label="Tutup"></a>

                            <img src="{{ asset('storage/' . $gambar) }}" alt="Struktur Organisasi - Tampilan Penuh"
                                class="relative max-w-full max-h-[90vh] object-contain rounded-md z-10" />

                            <a href="#"
                                class="absolute top-4 right-5 text-white text-2xl leading-none hover:text-gray-300 transition-colors duration-200 z-10"
                                aria-label="Tutup">
                                <i class="fas fa-xmark"></i>
                            </a>

                            <p class="absolute bottom-5 text-white/50 text-xs z-10 pointer-events-none">
                                Klik di luar gambar atau tombol <i class="fas fa-xmark"></i> untuk menutup
                            </p>
                        </div>

                    @else
                        {{-- Placeholder --}}
                        <div class="flex flex-col items-center justify-center py-20 text-center">
                            <div
                                class="w-16 h-16 rounded-2xl bg-gray-100 border border-gray-200 flex items-center justify-center mb-4">
                                <i class="fas fa-image text-2xl text-gray-400"></i>
                            </div>
                            <p class="text-gray-700 font-medium mb-1">Gambar belum tersedia</p>
                            <p class="text-gray-400 text-sm">Belum ada gambar yang diupload.</p>
                        </div>
                    @endif
                </div>

                {{-- Card Footer --}}
                <div class="px-5 py-3 bg-white border-t border-gray-100 flex items-center justify-between">
                    <p class="text-xs text-gray-400">
                        @if(isset($updated_at) && $updated_at)
                            Diperbarui: {{ \Carbon\Carbon::parse($updated_at)->translatedFormat('d F Y') }}
                        @else
                            &nbsp;
                        @endif
                    </p>
                    @if(isset($gambar) && $gambar)
                        <a href="#lightbox"
                            class="inline-flex items-center gap-1.5 text-xs text-gray-400 hover:text-green-600 transition-colors duration-200">
                            <i class="fas fa-expand"></i>
                            Tampilkan penuh
                        </a>
                    @endif
                </div>
            </div>
        </div>

    </div>

    @push('scripts')
        <script>
            const inputFile = document.getElementById('gambar');
            const btnSubmit = document.getElementById('btn-submit');

            inputFile?.addEventListener('change', function () {
                if (this.files.length > 0) {
                    btnSubmit.classList.remove('hidden');
                    btnSubmit.classList.add('inline-flex');
                } else {
                    btnSubmit.classList.add('hidden');
                    btnSubmit.classList.remove('inline-flex');
                }
            });
        </script>
    @endpush
</x-app>