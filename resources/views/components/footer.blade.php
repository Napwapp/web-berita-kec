<!-- Footer -->
<footer class="bg-green-900 text-gray-300">
    <div class="mx-auto w-full max-w-screen-xl p-4 py-6 lg:py-8">
        <div class="md:flex md:justify-between">
            <!-- Logo di footer -->
            <div class="mb-6 md:mb-0 mr-6">
                <a href="https://flowbite.com/" class="flex">
                    <img src="{{asset('assets/images/logo/logo.webp')}}" class="h-10 me-3"
                        alt="Logo Kecamatan Binong" />
                    <span class="text-heading text-2xl font-bold whitespace-nowrap text-gray-100">Kecamatan Binong</span>
                </a>

                <!-- Deskripsi tentang perusahaan -->
                <p class="mt-2 text-sm text-body">
                    Kecamatan Binong, Subang, Jawa Barat, adalah daerah pertanian subur dan lumbung padi utama. Mayoritas penduduknya petani, dengan pusat pemerintahan di Desa Binong. Binong juga punya kekayaan budaya seperti Sisingaan dan situs sejarah Subanglarang
                </p>
            </div>
            

            <!-- Links di footer -->
            <div class="grid grid-cols-2 gap-8 sm:gap-6 sm:grid-cols-3">
                <div>
                    <h2 class="mb-6 text-sm font-bold text-heading uppercase text-gray-100">Informasi</h2>
                    <ul class="text-body font-medium text-gray-300">
                        <li class="mb-4">
                            <a href="#latest-news" class="hover:underline">Berita Terbaru</a>
                        </li>

                        <li class="mb-4">
                            <a href="#featured-categories" class="hover:underline">Kategori Berita Pilihan </a>
                        </li>

                        <li>
                            <a href="#popular-news" class="hover:underline">Berita Populer</a>
                        </li>
                    </ul>
                </div>

                <!-- Social Media Links -->
                <div>
                    <h2 class="mb-6 text-sm font-bold text-heading uppercase text-gray-100">Sosial Media</h2>
                    <ul class="text-body font-medium text-gray-300">
                        <li class="mb-4">
                            <a href="#"
                                class="hover:underline flex items-center gap-2">
                                <i class="fab fa-instagram"></i>
                                Instagram
                            </a>
                        </li>
                        <li>
                            <a href="#" class="hover:underline flex items-center gap-2">
                                <i class="fab fa-facebook"></i>
                                Facebook
                            </a>
                        </li>

                    </ul>
                </div>

                <!-- Kontak -->
                <div>
                    <h2 class="mb-6 text-sm font-bold text-heading uppercase text-gray-100">Kontak</h2>
                    <ul class="text-body font-medium text-gray-300 space-y-4">
                        <li class="flex flex-col gap-1">
                            <div class="flex items-center gap-2">
                                <i class="fas fa-map-marker-alt"></i>
                                <span class="font-semibold">Alamat</span>
                            </div>
                            <span class="text-sm leading-relaxed">Jl. Raya Binong No. 123, Kecamatan Binong, Belakang Polsek</span>
                        </li>
                        <li class="flex flex-col gap-1">
                            <div class="flex items-center gap-2">
                                <i class="fas fa-phone"></i>
                                <span class="font-semibold">Nomor Telepon</span>
                            </div>
                            <span class="text-sm leading-relaxed">(021) xxxx xxxx</span>
                        </li>
                        <li class="flex flex-col gap-1">
                            <div class="flex items-center gap-2">
                                <i class="fas fa-envelope"></i>
                                <span class="font-semibold">Email</span>
                            </div>
                            <span class="text-sm leading-relaxed">kecamatanbinong@email.com</span>
                        </li>
                    </ul>
                </div>
            </div>
        </div>

        <hr class="my-6 border-default sm:mx-auto lg:my-8" />

        <!-- Copyright -->
        <div class="sm:flex sm:items-center sm:justify-between">
            <span class="text-sm text-body sm:text-center">© {{ date('Y') }} <a href="#"
                    class="hover:underline">Kecamatan Binong</a>. Semua hak dilindungi undang-undang.
            </span>
            <div class="flex mt-4 sm:justify-center sm:mt-0">
                <a href="#" class="text-body hover:text-heading">
                    <i class="fab fa-facebook w-5 h-5"></i>
                    <span class="sr-only">Facebook page</span>
                </a>
                <a href="#" class="text-body hover:text-heading ms-5">
                    <i class="fab fa-instagram w-5 h-5"></i>
                    <span class="sr-only">Instagram page</span>
                </a>
            </div>
        </div>
    </div>
</footer>