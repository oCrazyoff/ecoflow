<x-app-layout>
    <h2>Minhas rendas</h2>
    <x-seletor-mes />

    <table>
        <thead>
            <tr>
                <th>Data</th>
                <th>Descrição</th>
                <th>Valor</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($incomes as $income)
                <tr>
                    <td>{{ $income->date->format('d/m/Y') }}</td>
                    <td>{{ $income->description }}</td>
                    <td>R$ {{ number_format($income->amount, 2, ',', '.') }}</td>
                </tr>
            @empty
                <tr>
                    <td>Nenhuma renda cadastrada</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</x-app-layout>
