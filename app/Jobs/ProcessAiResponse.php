<?php

namespace App\Jobs;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use App\Models\AiChatMessage;
use App\Services\GeminiAiService;

class ProcessAiResponse implements ShouldQueue
{
    use Queueable;

    protected $prompt;
    protected $userId;
    protected $apiKey;

    /**
     * Create a new job instance.
     */
    public function __construct($prompt, $userId, ?string $apiKey = null)
    {
        $this->prompt = $prompt;
        $this->userId = $userId;
        $this->apiKey = $apiKey;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $aiService = new GeminiAiService($this->apiKey);
        $response = $aiService->generateContent($this->prompt);

        // Save AI response
        AiChatMessage::create([
            'user_id' => $this->userId,
            'role' => 'ai',
            'content' => $response
        ]);
    }
}

