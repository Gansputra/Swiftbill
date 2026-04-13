<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class GeminiAiService
{
    protected $apiKey;
    protected $apiUrl;

    public function __construct()
    {
        $this->apiKey = env('GEMINI_API_KEY');
        $this->apiUrl = 'https://generativelanguage.googleapis.com/v1/models/gemini-2.5-flash:generateContent';
    }

    /**
     * Send a general structured prompt to Gemini.
     */
    public function generateContent($prompt)
    {
        if (empty($this->apiKey)) {
            return "Error: Gemini API Key is not set in .env";
        }

        $maxRetries = 3;
        $retryDelay = 3; // seconds

        for ($attempt = 1; $attempt <= $maxRetries; $attempt++) {
            try {
                $response = Http::timeout(30)->withHeaders([
                    'Content-Type' => 'application/json',
                    'x-goog-api-key' => $this->apiKey,
                ])->post($this->apiUrl, [
                    'contents' => [
                        [
                            'parts' => [
                                ['text' => $prompt]
                            ]
                        ]
                    ],
                    'generationConfig' => [
                        'temperature' => 0.2,
                        'topK' => 20,
                        'topP' => 0.8,
                        'maxOutputTokens' => 512,
                    ]
                ]);

                if ($response->successful()) {
                    $data = $response->json();
                    
                    if (isset($data['candidates'][0]['content']['parts'][0]['text'])) {
                        return $data['candidates'][0]['content']['parts'][0]['text'];
                    }
                    
                    return "No content returned from AI.";
                }

                // If rate limited (429), wait and retry
                if ($response->status() === 429 && $attempt < $maxRetries) {
                    Log::warning("Gemini rate limited, retrying in {$retryDelay}s (attempt {$attempt}/{$maxRetries})");
                    sleep($retryDelay);
                    $retryDelay *= 2; // exponential backoff
                    continue;
                }

                Log::error('Gemini API Error: ' . $response->body());
                return "Error from AI Service: " . $response->status() . ". Please wait a moment and try again.";

            } catch (\Exception $e) {
                Log::error('Gemini exception: ' . $e->getMessage());
                return "Connection error to AI Service.";
            }
        }

        return "AI Service is temporarily busy. Please try again in a few minutes.";
    }

    /**
     * Pre-formulated prompt for Sales Forecasting & Market Basket Analysis
     */
    public function generateBusinessInsights($transactionsData)
    {
        $prompt = "You are a senior data scientist and retail analyst for a Point of Sale (POS) system.
I will provide you with the last 30 days of sales data in JSON format. 
Please analyze this data and generate a comprehensive 'Business Intelligence Report' written in Markdown format.

Your report MUST include these specific sections:
1. **Sales Forecasting**: Analyze the sales volume, revenue trends, and predict what items might need restocking soon based on their velocity. Give concrete advice, avoid vague statements.
2. **Market Basket Analysis**: Find patterns of products that are frequently bought together (e.g. 'Customers who buy Item A often buy Item B'). Mention specific pairs or combinations.
3. **Actionable Recommendations**: Give 3 distinct, practical tips for the store owner to increase profit next month.

Format beautifully with Markdown bolding, lists, and headers (do NOT use h1 #, use h2 ## and h3 ### so it fits in my layout).

Here is the JSON Sales Data:
" . json_encode($transactionsData);

        return $this->generateContent($prompt);
    }
}
