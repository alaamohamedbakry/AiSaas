<?php

namespace App\Jobs;

use App\Models\Generation;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class GenerateContentJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $generation;

    public function __construct(Generation $generation)
    {
        $this->generation = $generation;
    }

    public function handle()
    {
        $prompt = $this->generation->prompt;

        try {
            // نرسل POST request للـ Flask API المحلي
            $resp = Http::timeout(120)
                ->post('http://127.0.0.1:5000/generate', [
                    'prompt' => $prompt,
                ]);

            if ($resp->successful()) {
                $data = $resp->json();
                $text = $data['generated_text'] ?? null;

                if ($text) {
                    $this->generation->update([
                        'result' => $text,
                        'status' => 'completed',
                    ]);
                    return;
                }
            }

            $this->failAndRefund('Unexpected response: '.substr($resp->body(),0,200));

        } catch (\Exception $e) {
            $this->failAndRefund('Exception: '.$e->getMessage());
        }
    }

    protected function failAndRefund($message)
    {
        Log::error('Generation failed: '.$message.' | id: '.$this->generation->id);

        $this->generation->update([
            'status' => 'failed',
            'result' => $message,
        ]);

        if ($this->generation->user) {
            $this->generation->user->increment('credits', $this->generation->cost);
        }
    }
}
