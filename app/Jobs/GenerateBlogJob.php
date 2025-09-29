<?php

namespace App\Jobs;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;

class GenerateBlogJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected string $topic;
    protected string $cacheKey;

    public function __construct(string $topic, string $cacheKey)
    {
        $this->topic = $topic;
        $this->cacheKey = $cacheKey;
    }

    public function handle()
    {
        if (Cache::has($this->cacheKey)) {
            return; // لو النتيجة موجودة في الكاش، مننساش نعمل exit
        }

        $input = "Generate a blog title about: " . $this->topic;
        $attempts = 0;
        $maxAttempts = 5;
        $response = null;

        while ($attempts < $maxAttempts) {
            try {
                $response = Http::withHeaders([
                    'Authorization' => 'Bearer ' . config('gemini.api_key'),
                    'Content-Type' => 'application/json',
                ])->post(config('gemini.base_url') . '/chat:generate', [
                    'model' => 'gemini-1.5-turbo',
                    'messages' => [
                        ['role' => 'system', 'content' => 'You are a blog title generator.'],
                        ['role' => 'user', 'content' => $input],
                    ],
                ]);

                if ($response->successful()) {
                    break;
                } else {
                    throw new \Exception('Gemini API error: ' . $response->body());
                }
            } catch (\Exception $e) {
                if (str_contains($e->getMessage(), 'rate limit')) {
                    $attempts++;
                    sleep(pow(2, $attempts));
                } else {
                    throw $e;
                }
            }
        }

        Log::info('Gemini full response: ', (array) $response->json());

        $result = $response->json()['candidates'][0]['content'] ?? '⚠️ No result returned.';
        Cache::put($this->cacheKey, $result, now()->addMinutes(10));
    }
}
