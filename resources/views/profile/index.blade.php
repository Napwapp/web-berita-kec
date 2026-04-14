<x-app title="Profil Saya" :showNavbar="false">
    <div x-data="{ modal: null, successMessage: null,
        init() {
            let msg = sessionStorage.getItem('success_message');
            if (msg) {
                this.successMessage = msg;
                sessionStorage.removeItem('success_message');
            }
        }}" @keydown.escape.window="modal = null">

        
        <div x-show="successMessage" x-transition
            class="flex items-center justify-between gap-2 px-4 py-3 border text-sm rounded-xl bg-green-100 border-green-500 text-green-700 max-w-2xl mx-auto">
            <div class="flex items-center gap-2">
                <i class="fas fa-circle-check"></i>
                <span x-text="successMessage"></span>
            </div>

            <button @click="successMessage = null" class="text-green-600 hover:text-green-800">
                <i class="fas fa-xmark"></i>
            </button>
        </div>

        <div class="min-h-screen bg-gray-50">
            <div class="max-w-4xl mx-auto space-y-6">
                {{-- ── Header / Hero Card ─────────────────────────────────────────── --}}
                <div class="bg-white rounded-2xl border border-gray-200 shadow-sm overflow-hidden">
                    {{-- Banner strip --}}
                    <div class="h-24 bg-green-600"></div>
                    {{-- Avatar + info + tombol edit --}}
                    <div class="px-6 pb-6">
                        <div class="flex flex-col sm:flex-row sm:items-end sm:justify-between gap-4 -mt-12">

                            {{-- Avatar --}}
                            <div class="relative w-fit">
                                <img src="{{ auth()->user()->profile_photo }}" alt="Foto Profil"
                                    class="w-24 h-24 rounded-2xl border-4 border-white object-cover shadow-sm" />

                                {{-- Badge role --}}
                                <span
                                    class="absolute -bottom-2 -right-2 inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-xs font-medium
                                {{ auth()->user()->role === 'admin' ? 'bg-green-600 text-white' : 'bg-gray-100 text-gray-600 border border-gray-200' }}">
                                    <i
                                        class="fas {{ auth()->user()->role === 'admin' ? 'fa-shield-halved' : 'fa-user' }} text-[10px]"></i>
                                    {{ ucfirst(auth()->user()->role ?? 'user') }}
                                </span>
                            </div>

                            {{-- Nama & email --}}
                            <div class="flex-1 sm:pb-1">
                                <h1 class="text-xl font-bold text-gray-800">{{ auth()->user()->name }}</h1>
                                <p class="text-sm text-gray-500 mt-0.5">{{ auth()->user()->email }}</p>
                            </div>

                            {{-- Tombol edit profil --}}
                            <div class="flex flex-row gap-4 sm:pb-1">
                                <button @click="modal = 'edit'"
                                    class="inline-flex items-center gap-2 px-4 py-2 text-sm font-medium text-white bg-green-600 rounded-lg hover:bg-green-700">
                                    <i class="fas fa-pen-to-square"></i>
                                    Edit Profil
                                </button>

                                @auth
                                    <form method="POST" action="{{ route('logout') }}">
                                        @csrf
                                
                                        <button type="submit" class="bg-red-600 hover:bg-red-700 text-white text-sm px-4 py-2 rounded-lg">
                                            <i class="fa-solid fa-right-from-bracket"></i>
                                            Logout
                                        </button>
                                    </form>
                                @endauth
                            </div>
                        </div>
                    </div>
                </div>

                {{-- ── Grid dua kolom ──────────────────────────────────────────────── --}}
                <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

                    {{-- Kolom kiri: info akun --}}
                    <div class="lg:col-span-2 space-y-6">

                        {{-- Flash messages --}}
                        @if(session('success'))
                            <div x-data="{ show: true }" x-show="show"
                                class="flex items-center justify-between gap-2 px-4 py-3 bg-green-50 border border-green-200 text-green-700 text-sm rounded-xl">
                                <div class="flex items-center gap-2">
                                    <i class="fas fa-check-circle shrink-0"></i>
                                    {{ session('success') }}
                                </div>
                                <button @click="show = false" class="text-green-400 hover:text-green-600">
                                    <i class="fas fa-xmark"></i>
                                </button>
                            </div>
                        @endif

                        @if(session('error'))
                            <div x-data="{ show: true }" x-show="show"
                                class="flex items-center justify-between gap-2 px-4 py-3 bg-red-50 border border-red-200 text-red-600 text-sm rounded-xl">
                                <div class="flex items-center gap-2">
                                    <i class="fas fa-circle-exclamation shrink-0"></i>
                                    {{ session('error') }}
                                </div>
                                <button @click="show = false" class="text-red-400 hover:text-red-600">
                                    <i class="fas fa-xmark"></i>
                                </button>
                            </div>
                        @endif

                        {{-- Card: Informasi Akun --}}
                        <div class="bg-white rounded-2xl border border-gray-200 shadow-sm overflow-hidden">
                            <div class="flex items-center justify-between px-5 py-4 border-b border-gray-100">
                                <div class="flex items-center gap-2">
                                    <span class="w-2 h-2 rounded-full bg-green-500 inline-block"></span>
                                    <h2 class="text-sm font-semibold text-gray-700">Informasi Akun</h2>
                                </div>
                                <button @click="modal = 'edit'"
                                    class="text-xs text-green-600 hover:text-green-700 flex items-center gap-1 transition-colors duration-200">
                                    <i class="fas fa-pen text-[10px]"></i> Edit
                                </button>
                            </div>
                            <div class="divide-y divide-gray-50">
                                <div class="flex items-center justify-between px-5 py-3.5">
                                    <div class="flex items-center gap-3 text-sm text-gray-500">
                                        <i class="fas fa-user w-4 text-center text-gray-400"></i>
                                        Nama Lengkap
                                    </div>
                                    <span class="text-sm font-medium text-gray-800">{{ auth()->user()->name }}</span>
                                </div>
                                <div class="flex items-center justify-between px-5 py-3.5">
                                    <div class="flex items-center gap-3 text-sm text-gray-500">
                                        <i class="fas fa-envelope w-4 text-center text-gray-400"></i>
                                        Email
                                    </div>
                                    <span class="text-sm font-medium text-gray-800">{{ auth()->user()->email }}</span>
                                </div>

                                <div class="flex items-center justify-between px-5 py-3.5">
                                    <div class="flex items-center gap-3 text-sm text-gray-500">
                                        <i class="fas fa-shield-halved w-4 text-center text-gray-400"></i>
                                        Role
                                    </div>
                                    <span
                                        class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-medium
                                    {{ auth()->user()->role === 'admin' ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-600' }}">
                                        {{ ucfirst(auth()->user()->role ?? 'user') }}
                                    </span>
                                </div>
                                <div class="flex items-center justify-between px-5 py-3.5">
                                    <div class="flex items-center gap-3 text-sm text-gray-500">
                                        <i class="fas fa-calendar w-4 text-center text-gray-400"></i>
                                        Bergabung
                                    </div>
                                    <span class="text-sm font-medium text-gray-800">
                                        {{ auth()->user()->created_at->translatedFormat('d F Y') }}
                                    </span>
                                </div>
                            </div>
                        </div>

                        {{-- Card: password --}}
                        <div class="bg-white rounded-2xl border border-gray-200 shadow-sm overflow-hidden">
                            <div class="flex items-center justify-between px-5 py-4 border-b border-gray-100">
                                <div class="flex items-center gap-2">
                                    <span class="w-2 h-2 rounded-full bg-green-500 inline-block"></span>
                                    <h2 class="text-sm font-semibold text-gray-700">Keamanan</h2>
                                </div>
                                <button @click="modal = 'password'"
                                    class="text-xs text-green-600 hover:text-green-700 flex items-center gap-1 transition-colors duration-200">
                                    <i class="fas fa-pen text-[10px]"></i> Ubah
                                </button>
                            </div>
                            <div class="divide-y divide-gray-50">
                                <div class="flex items-center justify-between px-5 py-3.5">
                                    <div class="flex items-center gap-3 text-sm text-gray-500">
                                        <i class="fas fa-lock w-4 text-center text-gray-400"></i>
                                        Password
                                    </div>
                                    <div class="flex items-center gap-2">
                                        <span class="text-sm text-gray-400 tracking-widest">••••••••</span>
                                        <button @click="modal = 'password'"
                                            class="text-xs px-2.5 py-1 text-green-600 border border-green-200 rounded-lg hover:bg-green-50 transition-colors duration-200">
                                            Ganti
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Kolom kanan: foto + aktivitas --}}
                    <div class="space-y-6">

                        {{-- Card: Foto Profil --}}
                        <div class="bg-white rounded-2xl border border-gray-200 shadow-sm overflow-hidden">
                            <div class="flex items-center gap-2 px-5 py-4 border-b border-gray-100">
                                <span class="w-2 h-2 rounded-full bg-green-500 inline-block"></span>
                                <h2 class="text-sm font-semibold text-gray-700">Foto Profil</h2>
                            </div>
                            <div class="p-5 flex flex-col items-center gap-4">
                                <img src="{{ auth()->user()->profile_photo }}" alt="Foto Profil"
                                    class="w-24 h-24 rounded-2xl border-4 border-white object-cover shadow-sm" />

                                <form action="{{ route('profile.foto') }}" method="POST" enctype="multipart/form-data"
                                    id="form-foto" class="w-full">
                                    @csrf
                                    @method('PATCH')

                                    <label for="foto"
                                        class="w-full inline-flex items-center justify-center gap-2 px-4 py-2 text-sm text-green-700 border border-green-300 bg-green-50 rounded-lg hover:bg-green-100 transition-colors duration-200 cursor-pointer">
                                        <i class="fas fa-camera"></i>
                                        Ganti Foto
                                    </label>
                                    <input type="file" id="foto" name="foto" accept="image/*" class="hidden"
                                        onchange="document.getElementById('form-foto').submit()" />

                                    @error('foto')
                                        <p class="mt-2 text-xs text-red-500 text-center">{{ $message }}</p>
                                    @enderror
                                </form>

                                @if(auth()->user()->profile_photo)
                                    <form action="{{ route('profile.foto.hapus') }}" method="POST" class="w-full"
                                        onsubmit="return confirm('Hapus foto profil?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit"
                                            class="w-full inline-flex items-center justify-center gap-2 px-4 py-2 text-sm text-red-500 border border-red-200 rounded-lg hover:bg-red-50 transition-colors duration-200">
                                            <i class="fas fa-trash"></i>
                                            Hapus Foto
                                        </button>
                                    </form>
                                @endif
                            </div>
                        </div>

                        {{-- Card: Info Singkat --}}
                        <div class="bg-white rounded-2xl border border-gray-200 shadow-sm overflow-hidden">
                            <div class="flex items-center gap-2 px-5 py-4 border-b border-gray-100">
                                <span class="w-2 h-2 rounded-full bg-green-500 inline-block"></span>
                                <h2 class="text-sm font-semibold text-gray-700">Ringkasan</h2>
                            </div>
                            <div class="p-5 space-y-3">
                                <div class="flex items-center gap-3 p-3 bg-gray-50 rounded-xl">
                                    <div
                                        class="w-8 h-8 rounded-lg bg-green-100 flex items-center justify-center shrink-0">
                                        <i class="fas fa-id-badge text-green-600 text-xs"></i>
                                    </div>
                                    <div>
                                        <p class="text-xs text-gray-400">ID Pengguna</p>
                                        <p class="text-sm font-medium text-gray-700">#{{ auth()->user()->id }}</p>
                                    </div>
                                </div>
                                <div class="flex items-center gap-3 p-3 bg-gray-50 rounded-xl">
                                    <div
                                        class="w-8 h-8 rounded-lg bg-green-100 flex items-center justify-center shrink-0">
                                        <i class="fas fa-circle-check text-green-600 text-xs"></i>
                                    </div>
                                    <div>
                                        <p class="text-xs text-gray-400">Status Akun</p>
                                        <p class="text-sm font-medium text-green-600">Aktif</p>
                                    </div>
                                </div>
                                <div class="flex items-center gap-3 p-3 bg-gray-50 rounded-xl">
                                    <div
                                        class="w-8 h-8 rounded-lg bg-green-100 flex items-center justify-center shrink-0">
                                        <i class="fas fa-calendar-plus text-green-600 text-xs"></i>
                                    </div>
                                    <div>
                                        <p class="text-xs text-gray-400">Bergabung</p>
                                        <p class="text-sm font-medium text-gray-700">
                                            {{ auth()->user()->created_at->diffForHumans() }}
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
        </div>

        <!-- Modal edit profil -->
        <div x-show="modal === 'edit'" x-transition
            class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 px-4" @click.self="modal = null"
            style="display: none;">
            <div class="bg-white w-full max-w-md rounded-2xl shadow-lg overflow-hidden">

                <!-- Header -->
                <div class="flex items-center justify-between px-6 py-4 border-b border-gray-100">
                    <div class="flex items-center gap-2">
                        <i class="fas fa-pen-to-square text-green-600"></i>
                        <h3 class="text-sm font-semibold text-gray-800">Edit Profil</h3>
                    </div>
                    <button @click="modal = null" class="text-gray-400 hover:text-gray-600">
                        <i class="fas fa-xmark text-lg"></i>
                    </button>
                </div>

                {{-- Form --}}
                <form action="{{ route('profile.update') }}" method="POST" class="p-6 space-y-4">
                    @csrf
                    @method('PATCH')

                    <div>
                        <label class="block text-xs font-medium text-gray-600 mb-1.5">Nama Lengkap</label>
                        <input type="text" name="name" value="{{ old('name', auth()->user()->name) }}"
                            class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent transition"
                            placeholder="Nama lengkap" />
                        @error('name')
                            <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-xs font-medium text-gray-600 mb-1.5">
                            Email
                        </label>
                        <input
                            type="email"
                            name="email"
                            value="{{ auth()->user()->email }}"
                            disabled
                            class="w-full px-3 py-2 text-sm border border-gray-200 rounded-lg bg-gray-50 text-gray-400 cursor-not-allowed"
                        />
                        <p class="mt-1.5 text-xs text-gray-400 flex items-center gap-1">
                            <i class="fas fa-circle-info"></i>
                            Email tidak dapat diubah untuk saat ini. Dikarenakan fitur belum tersedia
                        </p>
                    </div>                   

                    <div class="flex items-center justify-end gap-3 pt-2">
                        <button type="button" onclick="closeModal('modal-edit-profil')"
                            class="px-4 py-2 text-sm text-gray-600 border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors">
                            Batal
                        </button>
                        <button type="submit"
                            class="px-4 py-2 text-sm text-white bg-green-600 rounded-lg hover:bg-green-700 transition-colors">
                            <i class="fas fa-check mr-1.5"></i> Simpan
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Modal Ganti Password -->
        <div x-show="modal === 'password'" x-transition
            class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 px-4" @click.self="modal = null"
            style="display: none;">

            <div x-data="passwordForm()" class="bg-white w-full max-w-md rounded-2xl shadow-lg overflow-hidden">

                <div class="flex items-center justify-between px-6 py-4 border-b border-gray-100">
                    <div class="flex items-center gap-2">
                        <i class="fas fa-lock text-green-600"></i>
                        <h3 class="text-sm font-semibold text-gray-800">Ganti Password</h3>
                    </div>
                    <button @click="modal = null" class="text-gray-400 hover:text-gray-600">
                        <i class="fas fa-xmark text-lg"></i>
                    </button>
                </div>

                <form @submit.prevent="submitPassword" class="p-6 space-y-4">

                    <div>
                        <label class="block text-xs font-medium text-gray-600 mb-1.5">Password Baru</label>
                        <div class="relative">
                            <input x-model="form.password" :type="showPassword ? 'text' : 'password'" name="password"
                                class="w-full px-3 py-2 pr-10 text-sm border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent transition"
                                placeholder="Password baru (min. 8 karakter)" />
                            <button type="button" @click="showPassword = !showPassword"
                                class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600">
                                <i class="fas" :class="showPassword ? 'fa-eye-slash' : 'fa-eye'"></i>
                            </button>
                        </div>
                        <template x-if="errors.password">
                            <p class="mt-1 text-xs text-red-500" x-text="errors.password[0]"></p>
                        </template>
                    </div>

                    <div>
                        <label class="block text-xs font-medium text-gray-600 mb-1.5">Konfirmasi Password Baru</label>
                        <div class="relative">
                            <input x-model="form.password_confirmation" :type="showConfirm ? 'text' : 'password'"
                                name="password_confirmation"
                                class="w-full px-3 py-2 pr-10 text-sm border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent transition"
                                placeholder="Ulangi password baru" />
                            <button type="button" @click="showConfirm = !showConfirm"
                                class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600">
                                <i class="fas" :class="showConfirm ? 'fa-eye-slash' : 'fa-eye'"></i>
                            </button>
                        </div>
                        <template x-if="errors.password_confirmation">
                            <p class="mt-1 text-xs text-red-500" x-text="errors.password_confirmation[0]"></p>
                        </template>
                    </div>

                    <div class="flex items-center justify-end gap-3 pt-2">
                        <button type="button" @click="modal = null"
                            class="px-4 py-2 text-sm text-gray-600 border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors">
                            Batal
                        </button>
                        <button type="submit" :disabled="loading"
                            class="inline-flex items-center gap-1.5 px-4 py-2 text-sm text-white bg-green-600 rounded-lg hover:bg-green-700 transition-colors disabled:opacity-60 disabled:cursor-not-allowed">
                            <i class="fas" :class="loading ? 'fa-spinner fa-spin' : 'fa-check'"></i>
                            <span x-text="loading ? 'Menyimpan...' : 'Simpan'"></span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
        <!-- Ajax with Apine JS -->
        <script>
            function passwordForm() {
                return {
                    form: {
                        password: '',
                        password_confirmation: ''
                    },
                    errors: {},
                    loading: false,
                    showPassword: false,
                    showConfirm: false,

                    async submitPassword() {
                        this.errors = {}
                        this.loading = true

                        try {
                            const response = await fetch("{{ route('profile.password') }}", {
                                method: 'PATCH',
                                headers: {
                                    'Content-Type': 'application/json',
                                    'Accept': 'application/json',
                                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                                },
                                body: JSON.stringify({
                                    password: this.form.password,
                                    password_confirmation: this.form.password_confirmation
                                })
                            })

                            const data = await response.json()

                            if (!response.ok) {
                                this.errors = data.errors || {}
                                return
                            }

                            sessionStorage.setItem('success_message', data.message);
                            window.location.href = "{{ route('profile.index') }}"


                        } catch (e) {
                            console.error(e)
                        } finally {
                            this.loading = false
                        }
                    }
                }
            }

        </script>
    </div>
</x-app>