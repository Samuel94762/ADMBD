<x-guest-layout>
    <div class="mb-4 text-sm text-gray-600">
        Ingresa el código de verificación que enviamos a tu correo electrónico.
        Este código expira en <strong><span id="countdown">{{ $remainingSeconds ?? 60 }}</span> segundos</strong>.
        <p id="expired-note" class="text-red-600 mt-2 hidden">
            El código ha expirado. Vuelve a iniciar sesión para generar uno nuevo.
        </p>
    </div>

    <!-- Formulario OTP -->
    <form method="POST" action="{{ route('otp.validate') }}">
        @csrf

        <!-- Campo para OTP -->
        <div>
            <x-input-label for="codigo" :value="__('Código OTP')" />
            <x-text-input id="codigo" class="block mt-1 w-full"
                          type="text" name="codigo" required autofocus
                          maxlength="6" placeholder="123456" />
            <x-input-error :messages="$errors->get('codigo')" class="mt-2" />
        </div>

        <!-- Botón enviar -->
        <div class="flex items-center justify-end mt-4">
            <x-primary-button id="btn-validar">
                {{ __('Validar OTP') }}
            </x-primary-button>
        </div>
    </form>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            let timeLeft = parseInt(@json($remainingSeconds ?? 60), 10);
            const countdownEl = document.getElementById('countdown');
            const codeInput   = document.getElementById('codigo');
            const submitBtn   = document.getElementById('btn-validar');
            const expiredNote = document.getElementById('expired-note');

            function expireUI() {
                if (countdownEl) countdownEl.textContent = '0';
                if (codeInput)   codeInput.setAttribute('disabled', 'disabled');
                if (submitBtn) {
                    submitBtn.setAttribute('disabled', 'disabled');
                    submitBtn.classList.add('opacity-50', 'cursor-not-allowed');
                }
                if (expiredNote) expiredNote.classList.remove('hidden');
            }

            // Si ya está vencido al cargar
            if (timeLeft <= 0) {
                expireUI();
                return;
            }

            const timer = setInterval(() => {
                timeLeft -= 1;
                if (timeLeft <= 0) {
                    clearInterval(timer);
                    expireUI();
                } else if (countdownEl) {
                    countdownEl.textContent = String(timeLeft);
                }
            }, 1000);
        });
    </script>
</x-guest-layout>

