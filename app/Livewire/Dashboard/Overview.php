<?php

namespace App\Livewire\Dashboard;

use Livewire\Component;
use App\Models\Transaction;
use App\Models\Product;
use App\Models\TransactionItem;
use Illuminate\Support\Facades\DB;

class Overview extends Component
{
    public function render()
    {
        $dailySalesCount = Transaction::whereDate('created_at', today())->count();
        $dailyRevenue = Transaction::whereDate('created_at', today())->sum('total_price');
        $lowStockProducts = Product::whereColumn('stock', '<=', 'min_stock')->count();
        $totalProducts = Product::count();

        // Top 5 Products
        $topProducts = TransactionItem::select('product_id', DB::raw('SUM(quantity) as total_sold'))
            ->groupBy('product_id')
            ->orderBy('total_sold', 'desc')
            ->with('product')
            ->take(5)
            ->get();

        // Recent Transactions
        $recentTransactions = Transaction::with('user')->latest()->take(5)->get();

        return view('livewire.dashboard.overview', [
            'dailySalesCount' => $dailySalesCount,
            'dailyRevenue' => $dailyRevenue,
            'lowStockProducts' => $lowStockProducts,
            'totalProducts' => $totalProducts,
            'topProducts' => $topProducts,
            'recentTransactions' => $recentTransactions,
        ]);
    }
}
