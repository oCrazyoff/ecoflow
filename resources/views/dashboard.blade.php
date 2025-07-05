<x-app-layout>

    {{-- Titulo da pagina --}}
    <x-slot name="title">Dashboard</x-slot>
    <div>
        <div>
            <h2>Dashboard</h2>
            <p>Olá, {{ Auth::user()->name }}! Aqui está o resumo das suas finanças</p>
        </div>
        <x-seletor-mes />
    </div>
</x-app-layout>
