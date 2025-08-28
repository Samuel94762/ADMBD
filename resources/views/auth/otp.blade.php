<x-guest-layout>
    <div class="mb-4 text-sm text-gray-600">
        Ingresa el código de verificación que enviamos a tu correo electrónico. 
        Este código expira en <strong>60 segundos</strong>.
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
            <x-primary-button>
                {{ __('Validar OTP') }}
            </x-primary-button>
        </div>
    </form>
</x-guest-layout>
