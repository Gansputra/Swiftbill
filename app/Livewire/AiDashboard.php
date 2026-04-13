<?php

namespace App\Livewire;

use Livewire\Component;
use App\Services\GeminiAiService;
use App\Models\Transaction;
use App\Models\Product;

class AiDashboard extends Component
{
    public $insightsMarkdown = '';

    public $chatMessages = [];
    public $userMessage = '';

    public function mount()
    {
        $this->chatMessages[] = [
            'role' => 'ai', 
            'content' => 'Hello! I am your Swiftbill AI Assistant. You can ask me to analyze sales or check stock levels. Or click **Generate Business Insights** on the left for a deep dive.'
        ];
    }

    public function generateInsights(GeminiAiService $aiService)
    {
        // Fetch last 30 days of transactions with items and products
        $transactions = Transaction::with('items.product')
            ->where('created_at', '>=', now()->subDays(30))
            ->get();

        // Simplify data structure to save tokens
        $simplifiedData = $transactions->map(function ($t) {
            return [
                'date' => $t->created_at->format('Y-m-d'),
                'total' => $t->total_price,
                'items' => $t->items->map(function ($i) {
                    return [
                        'name' => $i->product ? $i->product->name : 'Unknown',
                        'qty' => $i->quantity
                    ];
                })
            ];
        })->toArray();

        $this->insightsMarkdown = $aiService->generateBusinessInsights($simplifiedData);
    }

    public function sendChatMessage(GeminiAiService $aiService)
    {
        $this->validate(['userMessage' => 'required|string|min:2']);

        $prompt = $this->userMessage;
        $this->chatMessages[] = ['role' => 'user', 'content' => $prompt];
        $this->userMessage = '';

        // Context injection — rich data for AI
        $products = Product::select('name', 'stock', 'sell_price')->get()->toArray();
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

        // Average daily revenue (last 30 days)
        $avgRevenue = Transaction::where('created_at', '>=', now()->subDays(30))
            ->selectRaw('DATE(created_at) as date, SUM(total_price) as daily_total')
            ->groupBy('date')
            ->get()
            ->avg('daily_total') ?? 0;

        $fullPrompt = "You are an AI assistant for a Point of Sale system named Swiftbill. 
Answer concisely and cleanly. Use Markdown formatting.

Context about the current database:
- Today's Sales Revenue: Rp. {$todaySales}
- Average Daily Revenue (30 days): Rp. " . number_format($avgRevenue, 0) . "
- Top Selling Products (last 30 days, by quantity): " . json_encode($topSelling) . "
- Current Stock Levels: " . json_encode($products) . "

User's Question: " . $prompt;

        $response = $aiService->generateContent($fullPrompt);

        $this->chatMessages[] = ['role' => 'ai', 'content' => $response];
    }

    public function render()
    {
        return view('livewire.ai-dashboard')->layout('layouts.app');
    }
}
