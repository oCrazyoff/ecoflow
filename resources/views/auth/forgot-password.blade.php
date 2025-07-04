<x-guest-layout>

    <!-- Session Status -->
    <x-auth-session-status class="mb-4" :status="session('status')" />

    <form method="POST" action="{{ route('password.email') }}">
        @csrf

        <h1 class="text-center font-bold text-2xl">Redefinir senha</h1>
        <p class="text-center mb-5 text-black/70">
            Problemas para acessar sua conta? Insira seu e-mail e enviaremos um link para redefinir sua senha com segurança.
        </p>

        <!-- Email Address -->
        <div>
            <x-input-label for="email" :value="__('Email')" />
            <x-text-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email')" placeholder="seu@email.com" required autofocus />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <div class="flex items-center justify-end mt-4">
            <x-primary-button>
                {{ __('Enviar') }}
            </x-primary-button>
        </div>

        <p class="text-center mt-4">Lembrou da sua senha? <a class="text-teal-600 underline decoration-1" href="{{ route('login') }}">Entrar</a></p>
    </form>
</x-guest-layout>
