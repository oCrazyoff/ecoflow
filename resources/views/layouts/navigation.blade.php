<aside x-data="{ menuAberto: true }" :class="menuAberto ? 'w-80' : 'w-fit'"
    class="flex flex-col justify-between h-dvh border-r bg-white">
    <div>
        <div class="flex p-5 gap-3 border-b" x-bind:class="menuAberto ? 'justify-between' : 'justify-center'">
            <div x-show="menuAberto">
                <x-logo />
            </div>
            <button @click="menuAberto = !menuAberto">
                <x-heroicon-o-chevron-left class="text-black w-5 h-5" x-bind:class="{ 'rotate-180': !menuAberto }" />
            </button>
        </div>
        <nav class="flex flex-col gap-2 p-5">
            <a href="{{ route('dashboard') }}" @class([
                'link-menu',
                'bg-teal-600/20 text-teal-600 font-bold' => request()->routeIs('dashboard'),
            ])>
                <x-heroicon-o-squares-2x2 class="w-6 h-6" />
                <div x-show="menuAberto">
                    Dashboard
                </div>
            </a>
            <a class="link-menu" href="{{ route('rendas.index') }}"><x-heroicon-o-wallet class="w-6 h-6" />
                <div x-show="menuAberto">
                    Rendas
                </div>
            </a>
            <a class="link-menu" href="#"><x-heroicon-o-arrow-trending-down class="w-6 h-6" />
                <div x-show="menuAberto">
                    Despesas
                </div>
            </a>
            <a class="link-menu" href="#"><x-heroicon-o-arrow-trending-up class="w-6 h-6" />
                <div x-show="menuAberto">
                    Investimentos
                </div>
            </a>
        </nav>
    </div>
    <div class="flex items-center justify-center p-5 border-t">
        <a href="{{ route('profile.edit') }}" @class([
            'link-menu justify-center w-full',
            'bg-teal-600/20 text-teal-600 font-bold' => request()->routeIs(
                'profile.edit'),
        ])>
            <x-heroicon-o-user-circle class="w-6 h-6" />
            <div x-show="menuAberto">
                {{ Auth::user()->name }}
            </div>
        </a>
    </div>
</aside>
