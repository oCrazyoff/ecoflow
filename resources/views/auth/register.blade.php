<x-guest-layout>

    {{-- Titulo da página --}}
    <x-slot name='title'>
        {{ config('app.name') }} • Cadastre-se
    </x-slot>

    <form method="POST" action="{{ route('register') }}">
        @csrf

        <h1 class="text-center font-bold text-2xl">Criar conta</h1>
        <p class="text-center mb-5 text-black/70">Preencha seus dados para criar uma nova conta</p>


        <!-- Name -->
        <div>
            <x-input-label for="name" :value="__('Nome')" />
            <x-text-input id="name" class="block mt-1 w-full" type="text" name="name" :value="old('name')" placeholder="Seu nome" required autofocus autocomplete="name" />
            <x-input-error :messages="$errors->get('name')" class="mt-2" />
        </div>

        <!-- Email Address -->
        <div class="mt-4">
            <x-input-label for="email" :value="__('Email')" />
            <x-text-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email')" placeholder="seu@email.com" required autocomplete="username" />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <!-- Password -->
        <div class="mt-4">
            <x-input-label for="password" :value="__('Senha')" />

            <x-text-input id="password" class="block mt-1 w-full"
                            type="password"
                            name="password"
                            placeholder="*****"
                            required autocomplete="new-password" />

            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <!-- Confirm Password -->
        <div class="mt-4">
            <x-input-label for="password_confirmation" :value="__('Confirmar senha')" />

            <x-text-input id="password_confirmation" class="block mt-1 w-full"
                            type="password"
                            name="password_confirmation" 
                            placeholder="*****"
                            required autocomplete="new-password" />

            <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
        </div>

        <x-primary-button class="mt-4">
            {{ __('Criar conta') }}
        </x-primary-button>

        <p class="text-center mt-4">Já tem uma conta? <a class="text-teal-600 underline decoration-1" href="{{ route('login') }}">Entrar</a></p>
    </form>
</x-guest-layout>
