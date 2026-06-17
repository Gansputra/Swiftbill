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

        // 1. Context injection — rich products data (with 7-day sales and min_stock levels)
        $products = Product::leftJoin('transaction_items', 'products.id', '=', 'transaction_items.product_id')
            ->leftJoin('transactions', function ($join) {
                $join->on('transaction_items.transaction_id', '=', 'transactions.id')
                    ->where('transactions.created_at', '>=', now()->subDays(7));
            })
            ->select(
                'products.name',
                'products.stock',
                'products.min_stock',
                'products.sell_price',
                DB::raw('COALESCE(SUM(CASE WHEN transactions.id IS NOT NULL THEN transaction_items.quantity ELSE 0 END), 0) as sold_last_7_days')
            )
            ->groupBy('products.id', 'products.name', 'products.stock', 'products.min_stock', 'products.sell_price')
            ->orderBy(DB::raw('products.stock * products.sell_price'), 'desc')
            ->limit(50)
            ->get()
            ->toArray();

        $todaySales = Transaction::whereDate('created_at', today())->sum('total_price');

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

        $fullPrompt = "You are an AI assistant for a Point of Sale system named Swiftbill. 
Answer concisely and cleanly. Use Markdown formatting. Always reply in the same language as the user (Indonesian if they ask in Indonesian, English if in English).

Context about the current database:
- Today's Sales Revenue: Rp. " . number_format($todaySales, 0, ',', '.') . "
- Average Daily Revenue (over the last {$daysRange} days since store start): Rp. " . number_format($avgRevenue, 0, ',', '.') . "
- Top Selling Products (last 30 days, by quantity): " . json_encode($topSelling) . "
- Current Stock Levels (includes name, stock, min_stock, sell_price, and units sold in the last 7 days): " . json_encode($products) . "

Guidelines for answering stock sufficiency:
- For questions about whether stock is sufficient for next week, compare 'stock' with 'sold_last_7_days'.
- If the current 'stock' of a product is less than 'sold_last_7_days', warn the user that the stock is INSUFFICIENT for next week.
- If 'stock' is close to or below 'min_stock', suggest restocking it.
- Estimate how many days the remaining stock will last based on the weekly sales rate ('sold_last_7_days').

User's Question: " . $prompt;

        // Dispatch AI response job asynchronously
        ProcessAiResponse::dispatch($fullPrompt, $userId);

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
