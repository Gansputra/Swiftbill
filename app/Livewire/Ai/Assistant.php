<?php

namespace App\Livewire\Ai;

use Livewire\Component;
use App\Services\GeminiService;

class Assistant extends Component
{
    public $question = '';
    public $messages = [];
    public $isLoading = false;

    public function mount()
    {
        $this->messages[] = [
            'role' => 'assistant',
            'content' => 'Hello! I am SwiftBill AI. How can I help you today? You can ask about sales reports, product performance, or stock notifications.'
        ];
    }

    public function askGemini(GeminiService $service)
    {
        if (empty($this->question)) return;

        $userMsg = $this->question;
        $this->messages[] = ['role' => 'user', 'content' => $userMsg];
        $this->question = '';
        $this->isLoading = true;

        // Note: Livewire 3 might not show loading state if the request is very fast or blocking.
        // But we handle it here.
        
        $response = $service->ask($userMsg);

        $this->messages[] = ['role' => 'assistant', 'content' => $response];
        $this->isLoading = false;
    }

    public function render()
    {
        return view('livewire.ai.assistant');
    }
}
