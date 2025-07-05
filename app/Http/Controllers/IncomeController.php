<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

class IncomeController extends Controller
{
    // Exibe as rendas
    public function index()
    {
        $incomes = Transaction::where('user_id', Auth::id())
            ->where('type', 'income')
            ->latest('date')
            ->get();

        return view('incomes.index', ['incomes' => $incomes]);
    }
}
