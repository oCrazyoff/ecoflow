<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Category;
use App\Models\Transaction;

class TransactionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Primeiro usuario cadastrado
        $user = User::first();

        // Cria uma categoria de Renda
        $salarioCategory = Category::create([
            'user_id' => $user->id,
            'name' => 'Salário',
            'type' => 'income',
        ]);

        // Cria algumas transações de Renda
        Transaction::create([
            'user_id' => $user->id,
            'category_id' => $salarioCategory->id,
            'description' => 'Salário Mensal',
            'amount' => 5000.00,
            'type' => 'income',
            'date' => now(),
            'status' => 'paid', // Status para renda pode ser sempre 'pago' (recebido)
            'is_recurrent' => true,
        ]);

        Transaction::create([
            'user_id' => $user->id,
            'category_id' => $salarioCategory->id,
            'description' => 'Freelance de Site',
            'amount' => 1200.50,
            'type' => 'income',
            'date' => now()->subDays(10),
            'status' => 'paid',
            'is_recurrent' => false,
        ]);
    }
}
