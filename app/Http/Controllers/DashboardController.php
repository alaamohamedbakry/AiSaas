<?php

namespace App\Http\Controllers;

use App\Jobs\GenerateContentJob;
use App\Models\Generation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
class DashboardController extends Controller
{
    public function index()
    {
        return view('dashboard');
    }
    public function generateText(Request $request)
    {
        $request->validate(['prompt' => 'required|string']);
        $prompt = $request->input('prompt');

        try {
            $response = Http::timeout(120)->post('http://127.0.0.1:5000/generate-text', [
                'prompt' => $prompt
            ]);

            if ($response->successful()) {
                $data = $response->json();
                $generatedText = $data['generated_text'] ?? 'No text returned';

                return back()->with('success', $generatedText);
            } else {
                Log::error('Flask API error: ' . $response->body());
                return back()->with('error', 'Failed to generate text from API.');
            }
        } catch (\Exception $e) {
            Log::error('Exception contacting Flask API: ' . $e->getMessage());
            return back()->with('error', 'Error contacting generation service.');
        }
    }

   public function ImageGenerator(Request $request)
{
    $request->validate([
        'imageprompt' => 'required|string',
    ]);

    $user = $request->user();
    $prompt = $request->input('imageprompt');
    $user->credits = 20;
    $user->save();
    $cost = 5; // تكلفة توليد الصورة



    try {
        DB::beginTransaction();

         if ($user->credits < $cost) {
            DB::rollBack();
            return back()->with('error', 'رصيدك غير كافي. اشحنه.');
        }

        $user->decrement('credits', $cost);

        //  إرسال الطلب إلى Flask
        $response = Http::timeout(120)->post('http://127.0.0.1:5000/generate-image', [
            'prompt' => $prompt,
        ]);

        if ($response->failed()) {
            DB::rollBack();
            return back()->with('error', 'فشل الاتصال بخدمة توليد الصور.');
        }

        $data = $response->json();
        if (!isset($data['image_base64'])) {
            DB::rollBack();
            return back()->with('error', 'لم يتم توليد الصورة.');
        }

        //  تحويل Base64 إلى ملف PNG وحفظه
        $imageData = base64_decode($data['image_base64']);
        $fileName = 'image_' . Str::random(10) . '.png';
        $filePath = 'generated_images/' . $fileName;

        Storage::disk('public')->put($filePath, $imageData);

        DB::commit();

        // إرسال اسم الملف والمسار للواجهة لعرض الصورة وزر التحميل
        return back()
            ->with('success', ' تم توليد الصورة بنجاح!')
            ->with('imagePath', $filePath)
            ->with('fileName', $fileName);

    } catch (\Exception $e) {
        DB::rollBack();
        Log::error('Image generation error: ' . $e->getMessage());
        return back()->with('error', 'حدث خطأ أثناء توليد الصورة.');
    }
}

}
