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

        // 7-day Revenue Trend Data
        $chartData = [];
        $chartLabels = [];
        $revenueData = Transaction::selectRaw('DATE(created_at) as date, SUM(total_price) as revenue')
            ->where('created_at', '>=', now()->subDays(6))
            ->groupBy('date')
            ->orderBy('date', 'asc')
            ->get()
            ->keyBy('date');

        for ($i = 6; $i >= 0; $i--) {
            $date = now()->subDays($i)->format('Y-m-d');
            $label = now()->subDays($i)->format('D'); // Mon, Tue, etc.
            $revenue = (int) ($revenueData[$date]->revenue ?? 0);
            $chartData[] = $revenue;
            $chartLabels[] = $label;
        }

        // Category Distribution Data
        $categorySales = TransactionItem::join('products', 'transaction_items.product_id', '=', 'products.id')
            ->join('categories', 'products.category_id', '=', 'categories.id')
            ->select('categories.name', DB::raw('SUM(transaction_items.quantity) as total_qty'))
            ->groupBy('categories.name')
            ->orderBy('total_qty', 'desc')
            ->take(5)
            ->get();

        return view('livewire.dashboard.overview', [
            'dailySalesCount' => $dailySalesCount,
            'dailyRevenue' => $dailyRevenue,
            'lowStockProducts' => $lowStockProducts,
            'totalProducts' => $totalProducts,
            'topProducts' => $topProducts,
            'recentTransactions' => $recentTransactions,
            'revenueChartData' => $chartData,
            'revenueChartLabels' => $chartLabels,
            'categoryChartData' => $categorySales->pluck('total_qty')->toArray(),
            'categoryChartLabels' => $categorySales->pluck('name')->toArray(),
        ]);
    }
}
