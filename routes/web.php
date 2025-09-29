<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProfileController;
use App\Models\Generation;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});



// 📌 صفحة الـ Dashboard (GET فقط)
Route::get('/dashboard', [DashboardController::class,'index'])
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

// 📌 Blog Generator
Route::get('/blog-generator', function () {
    return view('blog_generator');
})->name('blog.page')->middleware('auth');

Route::post('/generate/text', [DashboardController::class, 'generateText'])
    ->name('generate.text')
    ->middleware('auth');

// 📌 Image Generator
Route::get('/image-generator', function () {
    return view('image-generator');
})->name('image.page')->middleware('auth');

Route::post('/generate-image', [DashboardController::class, 'ImageGenerator'])
    ->name('ImageGenerator')
    ->middleware('auth');


    // 📌 API polling (لو عايزة تعملي متابعة للـ status)
Route::middleware('auth')->get('/generation/{id}/status', function(Generation $generation) {
    return response()->json($generation->only(['status','result','image_path']));
})->name('generation.status');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});


Route::get('/download-image/{filename}', function ($filename) {
    $filePath = storage_path('app/public/generated_images/' . $filename);

    if (!file_exists($filePath)) {
        abort(404);
    }

    return response()->download($filePath);
})->name('image.download');





require __DIR__.'/auth.php';
