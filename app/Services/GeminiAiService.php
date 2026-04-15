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

}
