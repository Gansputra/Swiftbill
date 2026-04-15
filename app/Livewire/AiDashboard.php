<?php

namespace App\Livewire;

use Livewire\Component;
use App\Services\GeminiAiService;
use App\Models\Transaction;
use App\Models\Product;
use App\Models\AiChatMessage;

class AiDashboard extends Component
{

    public $chatMessages = [];
    public $userMessage = '';

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
            foreach ($history as $msg) {
                $this->chatMessages[] = [
                    'role' => $msg->role,
                    'content' => $msg->content,
                    'time' => $msg->created_at->format('d M Y, H:i')
                ];
            }
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

        // 2. Save and show AI Response
        $aiMsg = AiChatMessage::create([
            'user_id' => $userId,
            'role' => 'ai',
            'content' => $response
        ]);
        $this->chatMessages[] = ['role' => 'ai', 'content' => $response, 'time' => $aiMsg->created_at->format('d M Y, H:i')];
    }

    public function render()
    {
        return view('livewire.ai-dashboard')->layout('layouts.app');
    }
}
