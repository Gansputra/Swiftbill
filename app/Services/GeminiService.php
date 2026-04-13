<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use App\Models\Transaction;
use App\Models\Product;
use App\Models\Category;

class GeminiService
{
    protected $apiKey;
    protected $apiUrl = 'https://generativelanguage.googleapis.com/v1/models/gemini-2.5-flash:generateContent';

    public function __construct()
    {
        $this->apiKey = config('services.gemini.key');
    }

    public function ask($question)
    {
        if (!$this->apiKey) {
            return "Gemini API Key is not configured. Please add GEMINI_API_KEY to your .env file.";
        }

        $context = $this->getSystemContext();

        try {
            $response = Http::withoutVerifying()->post($this->apiUrl . '?key=' . $this->apiKey, [
                'contents' => [
                    [
                        'parts' => [
                            ['text' => $context . "\n\nUser Question: " . $question]
                        ]
                    ]
                ]
            ]);

            if ($response->successful()) {
                $data = $response->json();
                return $data['candidates'][0]['content']['parts'][0]['text'] ?? "Sorry, I couldn't generate a response.";
            }

            return "Error from Gemini: " . $response->body();
        } catch (\Exception $e) {
            return "Error: " . $e->getMessage();
        }
    }

    protected function getSystemContext()
    {
        $totalSales = Transaction::sum('total_price');
        $transactionCount = Transaction::count();
        $products = Product::with('category')->get()->map(function ($p) {
            return "{$p->name} (SKU: {$p->sku}, Stock: {$p->stock}, Price: {$p->sell_price})";
        })->implode(", ");

        return "You are SwiftBill AI, a business assistant for a retail store. 
        Here is the current store data:
        - Total Lifetime Sales: Rp " . number_format($totalSales, 0) . "
        - Total Transactions: $transactionCount
        - Current Inventory: $products
        
        Answer user questions based on this data. Be professional and give business advice if asked.";
    }
}
