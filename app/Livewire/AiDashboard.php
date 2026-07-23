<?php

namespace App\Livewire;

use Livewire\Component;
use App\Services\GeminiAiService;
use App\Models\Transaction;
use App\Models\Product;
use App\Models\AiChatMessage;
use App\Jobs\ProcessAiResponse;
use Illuminate\Support\Facades\DB;

class AiDashboard extends Component
{

    public $chatMessages = [];
    public $userMessage = '';
    public $isWaiting = false;

    public function mount()
    {
        $userId = auth()->id();
        $history = AiChatMessage::where('user_id', $userId)->orderBy('id', 'asc')->get();

        if ($history->isEmpty()) {
            $welcome = 'Hello! I am your Swiftbill AI Assistant. You can ask me to analyze sales or check stock levels in natural language.';

            $msg = AiChatMessage::create([
                'user_id' => $userId,
                'role' => 'ai',
                'content' => $welcome
            ]);

            $this->chatMessages[] = [
                'role' => 'ai',
                'content' => $welcome,
                'time' => $msg->created_at->format('d M Y, H:i')
            ];
        } else {
            $this->chatMessages = [];
            foreach ($history as $msg) {
                $this->chatMessages[] = [
                    'role' => $msg->role,
                    'content' => $msg->content,
                    'time' => $msg->created_at->format('d M Y, H:i')
                ];
            }
        }
    }

    public function loadMessages()
    {
        $userId = auth()->id();
        $history = AiChatMessage::where('user_id', $userId)->orderBy('id', 'asc')->get();
        
        $this->chatMessages = [];
        foreach ($history as $msg) {
            $this->chatMessages[] = [
                'role' => $msg->role,
                'content' => $msg->content,
                'time' => $msg->created_at->format('d M Y, H:i')
            ];
        }

        // If the last message is from AI (and it was saved by the Job), 
        // we can stop waiting.
        $lastMsg = $history->last();
        if ($lastMsg && $lastMsg->role === 'ai') {
            $this->isWaiting = false;
        }
    }


    public function sendChatMessage(GeminiAiService $aiService)
    {
        $this->validate(['userMessage' => 'required|string|min:2']);

        $prompt = $this->userMessage;
        $this->userMessage = '';

        $userId = auth()->id();

        // 1. Save and show User Message
        $userMsg = AiChatMessage::create([
            'user_id' => $userId,
            'role' => 'user',
            'content' => $prompt
        ]);
        $this->chatMessages[] = ['role' => 'user', 'content' => $prompt, 'time' => $userMsg->created_at->format('d M Y, H:i')];

        // 1. Context injection — rich products data (with 7-day sales, min_stock, buy_price, category, supplier)
        $products = Product::leftJoin('transaction_items', 'products.id', '=', 'transaction_items.product_id')
            ->leftJoin('transactions', function ($join) {
                $join->on('transaction_items.transaction_id', '=', 'transactions.id')
                    ->where('transactions.created_at', '>=', now()->subDays(7));
            })
            ->leftJoin('categories', 'products.category_id', '=', 'categories.id')
            ->leftJoin('suppliers', 'products.supplier_id', '=', 'suppliers.id')
            ->select(
                'products.name',
                'products.stock',
                'products.min_stock',
                'products.buy_price',
                'products.sell_price',
                'categories.name as category',
                'suppliers.name as supplier',
                DB::raw('COALESCE(SUM(CASE WHEN transactions.id IS NOT NULL THEN transaction_items.quantity ELSE 0 END), 0) as sold_last_7_days')
            )
            ->groupBy('products.id', 'products.name', 'products.stock', 'products.min_stock', 'products.buy_price', 'products.sell_price', 'categories.name', 'suppliers.name')
            ->orderBy(DB::raw('products.stock * products.sell_price'), 'desc')
            ->limit(50)
            ->get()
            ->toArray();

        $todaySales = Transaction::whereDate('created_at', today())->sum('total_price');
        $todayTransactionCount = Transaction::whereDate('created_at', today())->count();

        // Today's discount total
        $todayDiscount = Transaction::whereDate('created_at', today())->sum('total_discount');

        // Today's profit (COGS-based)
        $todayCogs = \App\Models\TransactionItem::whereHas('transaction', function ($q) {
                $q->whereDate('created_at', today());
            })->sum(DB::raw('cogs * quantity'));
        $todayProfit = $todaySales - $todayCogs - $todayDiscount;

        // Top selling products (last 30 days)
        $topSelling = \App\Models\TransactionItem::select('product_id', \DB::raw('SUM(quantity) as total_sold'))
            ->whereHas('transaction', function ($q) {
                $q->where('created_at', '>=', now()->subDays(30));
            })
            ->groupBy('product_id')
            ->orderByDesc('total_sold')
            ->with('product:id,name')
            ->take(10)
            ->get()
            ->map(fn($i) => ['name' => $i->product->name ?? 'Unknown', 'sold' => $i->total_sold])
            ->toArray();

        // Calculate average daily revenue dynamically based on actual days since start (up to 30 days)
        $firstTransaction = Transaction::oldest()->first();
        $daysRange = 30;
        if ($firstTransaction) {
            $daysSinceStart = $firstTransaction->created_at->diffInDays(now()) + 1;
            $daysRange = min(30, max(1, $daysSinceStart));
        }
        $total30DayRevenue = Transaction::where('created_at', '>=', now()->subDays($daysRange - 1)->startOfDay())->sum('total_price');
        $avgRevenue = $total30DayRevenue / $daysRange;

        // Payment method distribution (last 30 days)
        $paymentMethods = Transaction::where('created_at', '>=', now()->subDays(30))
            ->select('payment_method', DB::raw('COUNT(*) as count'), DB::raw('SUM(total_price) as total'))
            ->groupBy('payment_method')
            ->get()
            ->toArray();

        // Daily revenue trend (last 7 days)
        $dailyTrend = Transaction::where('created_at', '>=', now()->subDays(6)->startOfDay())
            ->select(DB::raw('DATE(created_at) as date'), DB::raw('SUM(total_price) as revenue'), DB::raw('COUNT(*) as transactions'))
            ->groupBy(DB::raw('DATE(created_at)'))
            ->orderBy('date')
            ->get()
            ->toArray();

        // Category performance (last 30 days)
        $categoryPerformance = \App\Models\TransactionItem::whereHas('transaction', function ($q) {
                $q->where('created_at', '>=', now()->subDays(30));
            })
            ->join('products', 'transaction_items.product_id', '=', 'products.id')
            ->join('categories', 'products.category_id', '=', 'categories.id')
            ->select(
                'categories.name as category',
                DB::raw('SUM(transaction_items.quantity) as total_sold'),
                DB::raw('SUM(transaction_items.subtotal) as total_revenue'),
                DB::raw('SUM(transaction_items.cogs * transaction_items.quantity) as total_cogs')
            )
            ->groupBy('categories.name')
            ->orderByDesc('total_revenue')
            ->get()
            ->map(fn($c) => [
                'category' => $c->category,
                'total_sold' => $c->total_sold,
                'revenue' => $c->total_revenue,
                'profit' => $c->total_revenue - $c->total_cogs
            ])
            ->toArray();

        // Current cash shift status
        $currentShift = \App\Models\CashShift::where('user_id', $userId)
            ->latest()
            ->first();
        $shiftInfo = $currentShift ? [
            'status' => $currentShift->status,
            'starting_cash' => $currentShift->starting_cash,
            'expected_ending_cash' => $currentShift->expected_ending_cash,
            'actual_ending_cash' => $currentShift->actual_ending_cash,
            'opened_at' => $currentShift->created_at->format('d M Y, H:i'),
            'closed_at' => $currentShift->closed_at ? $currentShift->closed_at->format('d M Y, H:i') : null,
        ] : null;

        // Cash in/out summary today
        $cashInOut = \App\Models\CashTransaction::whereHas('shift', function ($q) use ($userId) {
                $q->where('user_id', $userId);
            })
            ->whereDate('created_at', today())
            ->select('type', DB::raw('SUM(amount) as total'), DB::raw('COUNT(*) as count'))
            ->groupBy('type')
            ->get()
            ->toArray();

        // Supplier summary
        $supplierSummary = \App\Models\Supplier::withCount('products')
            ->orderByDesc('products_count')
            ->limit(10)
            ->get()
            ->map(fn($s) => ['name' => $s->name, 'total_products' => $s->products_count])
            ->toArray();

        // Total products and low stock count
        $totalProducts = Product::count();
        $lowStockCount = Product::whereColumn('stock', '<=', 'min_stock')->count();
        $outOfStockCount = Product::where('stock', 0)->count();

        $fullPrompt = "You are an AI assistant for a Point of Sale system named Swiftbill. 
Answer concisely and cleanly. Use Markdown formatting. Always reply in the same language as the user (Indonesian if they ask in Indonesian, English if in English).

Context about the current database:
- Today's Date: " . now()->format('d M Y (l)') . "
- Today's Sales Revenue: Rp. " . number_format($todaySales, 0, ',', '.') . "
- Today's Transaction Count: {$todayTransactionCount} transactions
- Today's Total Discount Given: Rp. " . number_format($todayDiscount, 0, ',', '.') . "
- Today's Estimated Profit (Revenue - COGS - Discount): Rp. " . number_format($todayProfit, 0, ',', '.') . "
- Average Daily Revenue (over the last {$daysRange} days since store start): Rp. " . number_format($avgRevenue, 0, ',', '.') . "
- Total Products in Inventory: {$totalProducts}
- Low Stock Products (stock <= min_stock): {$lowStockCount}
- Out of Stock Products (stock = 0): {$outOfStockCount}
- Top Selling Products (last 30 days, by quantity): " . json_encode($topSelling) . "
- Payment Method Distribution (last 30 days): " . json_encode($paymentMethods) . "
- Daily Revenue Trend (last 7 days): " . json_encode($dailyTrend) . "
- Category Performance (last 30 days, includes revenue & profit): " . json_encode($categoryPerformance) . "
- Current Stock Levels (includes name, stock, min_stock, buy_price, sell_price, category, supplier, sold_last_7_days): " . json_encode($products) . "
- Current Cash Shift Info: " . json_encode($shiftInfo) . "
- Today's Cash In/Out Transactions: " . json_encode($cashInOut) . "
- Supplier Summary: " . json_encode($supplierSummary) . "

Guidelines for answering:
- For questions about whether stock is sufficient for next week, compare 'stock' with 'sold_last_7_days'.
- If the current 'stock' of a product is less than 'sold_last_7_days', warn the user that the stock is INSUFFICIENT for next week.
- If 'stock' is close to or below 'min_stock', suggest restocking it.
- Estimate how many days the remaining stock will last based on the weekly sales rate ('sold_last_7_days').
- For profit analysis, use (sell_price - buy_price) as margin per unit, or use COGS data for historical accuracy.
- For payment trends, analyze the distribution of cash vs QRIS vs transfer.
- For daily trend analysis, compare today's revenue with the 7-day trend to identify growth or decline.
- For category analysis, identify the most profitable and most sold categories.

User's Question: " . $prompt;

        // Dispatch AI response job asynchronously with user's customized API Key (if set)
        $userApiKey = auth()->user()->gemini_api_key;
        ProcessAiResponse::dispatch($fullPrompt, $userId, $userApiKey);

        $this->isWaiting = true;
    }

    public function clearChat()
    {
        $userId = auth()->id();
        AiChatMessage::where('user_id', $userId)->delete();
        $this->chatMessages = [];
        $this->isWaiting = false;

        // Re-initialize greeting message
        $this->mount();

        session()->flash('success', 'Chat history cleared.');
    }

    public function render()
    {
        return view('livewire.ai-dashboard')->layout('layouts.app');
    }
}
