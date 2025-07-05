<x-app-layout>

    {{-- Titulo da pagina --}}
    <x-slot name="title">Dashboard</x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    Bem vindo! {{ Auth::user()->name }}
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
