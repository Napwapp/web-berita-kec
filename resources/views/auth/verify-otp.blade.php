<x-auth-layout title="Verifikasi OTP">
    <x-slot name="header">
        <x-flash-messages />    
    </x-slot
    
    <!-- Heading -->
    <div class="text-center mb-8">
        <h1 class="text-xl font-bold text-gray-800 mb-1">Verifikasi Kode OTP</h1>
        <p class="text-sm text-gray-500 leading-relaxed">Masukan Kode OTP yang di kirim ke email Anda</p>
    </div>

    <form id="otp-form" method="POST" action="{{ route('verification.otp.check') }}">
        @csrf

        <!-- OTP Inputs -->
        <div class="flex justify-center gap-3 mb-8" id="otp-container">
            <input id="otp-1" name="otp_1" type="text" maxlength="1" inputmode="numeric" pattern="[0-9]*"
                autocomplete="one-time-code"
                class="w-12 h-12 text-center text-lg rounded-lg border-2 border-gray-300 focus:border-green-600 focus:ring-2 focus:ring-green-200 outline-none" />
            <input id="otp-2" name="otp_2" type="text" maxlength="1" inputmode="numeric" pattern="[0-9]*"
                class="w-12 h-12 text-center text-lg rounded-lg border-2 border-gray-300 focus:border-green-600 focus:ring-2 focus:ring-green-200 outline-none" />
            <input id="otp-3" name="otp_3" type="text" maxlength="1" inputmode="numeric" pattern="[0-9]*"
                class="w-12 h-12 text-center text-lg rounded-lg border-2 border-gray-300 focus:border-green-600 focus:ring-2 focus:ring-green-200 outline-none" />
            <input id="otp-4" name="otp_4" type="text" maxlength="1" inputmode="numeric" pattern="[0-9]*"
                class="w-12 h-12 text-center text-lg rounded-lg border-2 border-gray-300 focus:border-green-600 focus:ring-2 focus:ring-green-200 outline-none" />
            <input id="otp-5" name="otp_5" type="text" maxlength="1" inputmode="numeric" pattern="[0-9]*"
                class="w-12 h-12 text-center text-lg rounded-lg border-2 border-gray-300 focus:border-green-600 focus:ring-2 focus:ring-green-200 outline-none" />
            <input id="otp-6" name="otp_6" type="text" maxlength="1" inputmode="numeric" pattern="[0-9]*"
                class="w-12 h-12 text-center text-lg rounded-lg border-2 border-gray-300 focus:border-green-600 focus:ring-2 focus:ring-green-200 outline-none" />
        </div>

        <!-- Input type hidden untuk menangkap nilai ke 6 input kotak -->
        <input type="hidden" name="otp" id="otp-hidden" />

        <!-- Resend -->
        <div class="text-center mb-6">
            <p class="text-sm text-gray-500">
                Tidak Menerima Kode?
                <button type="button" class="text-green-600 font-medium ml-1" id="resend-btn"
                    onclick="startResendTimer()">Kirim Ulang</button>
            </p>
            <p class="text-xs text-gray-400 mt-1 hidden" id="timer-text">Kirim ulang dalam <span id="countdown"
                    class="font-semibold text-green-600">60</span>s</p>
        </div>

        <!-- Verify Button -->
        <x-button id="verify-btn" type="submit" class="w-full font-semibold text-base disabled:opacity-50" disabled>
            Verifikasi
        </x-button>
    </form>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const inputs = Array.from(document.querySelectorAll('#otp-container input[type="text"]'));
            const hidden = document.getElementById('otp-hidden');
            const verifyBtn = document.getElementById('verify-btn');

            function updateHidden() {
                hidden.value = inputs.map(i => i.value || '').join('');
                verifyBtn.disabled = hidden.value.length !== inputs.length;
            }

            inputs.forEach((input, idx) => {
                input.addEventListener('input', (e) => {
                    const v = e.target.value.replace(/[^0-9]/g, '');
                    e.target.value = v.slice(0, 1);
                    if (v.length > 0 && idx < inputs.length - 1) {
                        inputs[idx + 1].focus();
                        inputs[idx + 1].select();
                    }
                    updateHidden();
                });

                input.addEventListener('keydown', (e) => {
                    if (e.key === 'Backspace' && !e.target.value && idx > 0) {
                        inputs[idx - 1].focus();
                        inputs[idx - 1].value = '';
                        updateHidden();
                        e.preventDefault();
                    }
                    if (e.key === 'ArrowLeft' && idx > 0) {
                        inputs[idx - 1].focus();
                    }
                    if (e.key === 'ArrowRight' && idx < inputs.length - 1) {
                        inputs[idx + 1].focus();
                    }
                });

                // handle paste
                input.addEventListener('paste', (e) => {
                    e.preventDefault();
                    const paste = (e.clipboardData || window.clipboardData).getData('text').replace(/\D/g, '');
                    if (!paste) return;
                    for (let i = 0; i < inputs.length; i++) {
                        inputs[i].value = paste[i] || '';
                    }
                    updateHidden();
                    // focus next empty or last
                    const firstEmpty = inputs.find(i => !i.value);
                    (firstEmpty || inputs[inputs.length - 1]).focus();
                });
            });

            // Resend otp handler
            window.startResendTimer = function () {
                const resendBtn = document.getElementById('resend-btn');
                const timerText = document.getElementById('timer-text');
                const countdown = document.getElementById('countdown');
                const token = document.querySelector('#otp-form input[name="_token"]').value;
                const resendUrl = "{{ route('verification.otp.resend') }}";

                // Mulai timer 60 detik untuk tombol resend otp
                let t = 60;
                resendBtn.disabled = true;
                resendBtn.classList.add('opacity-50');
                timerText.classList.remove('hidden');
                countdown.textContent = t;
                const iv = setInterval(() => {
                    t -= 1;
                    countdown.textContent = t;
                    if (t <= 0) {
                        clearInterval(iv);
                        resendBtn.disabled = false;
                        resendBtn.classList.remove('opacity-50');
                        timerText.classList.add('hidden');
                    }
                }, 1000);

                // send request to server to generate & send otp
                fetch(resendUrl, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': token,
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({})
                }).then(resp => resp.json())
                    .then(data => {
                        if (!data || !data.status) {
                            // re-enable button on failure
                            clearInterval(iv);
                            resendBtn.disabled = false;
                            resendBtn.classList.remove('opacity-50');
                            timerText.classList.add('hidden');
                            showFlash(data?.message || 'Gagal mengirim ulang OTP', 'error');
                        } else {
                            showFlash(data.message || 'OTP telah dikirim ulang', 'success');
                        }
                    }).catch(err => {
                        clearInterval(iv);
                        resendBtn.disabled = false;
                        resendBtn.classList.remove('opacity-50');
                        timerText.classList.add('hidden');
                        console.error(err);
                    });
            };
        });

        // Menampilkan pesan yg direturn dari response json
        function showFlash(message, type = 'success') {
            // cari header tempat komponen flash berada
            const header = document.querySelector('header');
            if (!header) return;

            const wrapper = document.createElement('div');
            wrapper.className = 'mt-4 p-3 rounded shadow-sm max-w-md mx-auto';

            if (type === 'success') {
                wrapper.classList.add('bg-green-50', 'text-green-700', 'border', 'border-green-100');
            } else {
                wrapper.classList.add('bg-red-50', 'text-red-700', 'border', 'border-red-100');
            }

            wrapper.innerHTML = `<div class="text-sm">${escapeHtml(message)}</div>`;

            // tambahkan dan otomatis hilangkan setelah 6 detik
            header.prepend(wrapper);
            setTimeout(() => wrapper.remove(), 6000);
        }

        function escapeHtml(str) {
            return String(str)
                .replace(/&/g, '&amp;')
                .replace(/</g, '&lt;')
                .replace(/>/g, '&gt;')
                .replace(/"/g, '&quot;')
                .replace(/'/g, '&#039;');
        }
    </script>
</x-auth-layout>