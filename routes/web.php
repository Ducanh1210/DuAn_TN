<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\AuthController;
use App\Http\Controllers\Admin\NewsController;
use App\Http\Controllers\Admin\EventController;
use App\Http\Controllers\Client\NewsController as ClientNewsController;
use App\Http\Controllers\Client\EventController as ClientEventController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

// Fix lỗi không hiển thị ảnh (symlink) khi chạy `php artisan serve` trên Windows
if (app()->environment('local') || php_sapi_name() == 'cli-server') {
    Route::get('/storage/{path}', function ($path) {
        $filePath = storage_path('app/public/' . $path);
        if (file_exists($filePath)) {
            $mimeType = mime_content_type($filePath);
            return response()->file($filePath, [
                'Content-Type' => $mimeType,
                'Cache-Control' => 'public, max-age=86400'
            ]);
        }
        abort(404);
    })->where('path', '.*');
}

Route::get('/', function () {
    $locations = \App\Models\Location::with(['category', 'images'])->where('status', 'published')->get();
    if($locations->isEmpty()){
        $locations = \App\Models\Location::with(['category', 'images'])->get();
    }
    
    // Resolve full asset URLs for icons to ensure they load correctly on any port/domain
    $locations->each(function($loc) {
        if ($loc->category && $loc->category->icon) {
            $loc->category->icon_url = asset($loc->category->icon);
        }
        
        if ($loc->thumbnail_url && !str_starts_with($loc->thumbnail_url, 'http')) {
            $loc->thumbnail_url = asset('storage/' . ltrim($loc->thumbnail_url, '/'));
        } elseif ($loc->images && $loc->images->count() > 0) {
            $thumbnail = $loc->images->where('is_thumbnail', true)->first() ?? $loc->images->first();
            $loc->thumbnail_url = !str_starts_with($thumbnail->image_url, 'http') ? asset('storage/' . ltrim($thumbnail->image_url, '/')) : $thumbnail->image_url;
        }
    });

    // Lấy 3 tin tức mới nhất cho banner
    $newsList = \App\Models\News::where('status', 'published')
                                ->orderBy('published_at', 'desc')
                                ->take(3)
                                ->get();
                                
    // Đảm bảo luôn có 3 item để không hỏng hiệu ứng CSS Animation 400%
    if ($newsList->count() > 0 && $newsList->count() < 3) {
        $padCount = 3 - $newsList->count();
        for ($i = 0; $i < $padCount; $i++) {
            $newsList->push($newsList[$i % $newsList->count()]);
        }
    }

    return view('client.home', compact('locations', 'newsList'));
})->name('home');

// Client 360 Viewer
Route::get('locations/{location:slug}/360', function(\App\Models\Location $location) {
    return view('client.360', compact('location'));
})->name('client.locations.360');

// Client News & Events
Route::get('/tin-tuc', [ClientNewsController::class, 'index'])->name('client.news.index');
Route::get('/tin-tuc/{slug}', [ClientNewsController::class, 'show'])->name('client.news.show');
Route::get('/su-kien', [ClientEventController::class, 'index'])->name('client.events.index');
Route::get('/su-kien/{slug}', [ClientEventController::class, 'show'])->name('client.events.show');

// Admin Auth Routes
Route::get('/admin/login', [AuthController::class, 'showLoginForm'])->name('admin.login.form');
Route::post('/admin/login', [AuthController::class, 'login'])->name('admin.login.submit');
Route::post('/admin/logout', [AuthController::class, 'logout'])->name('admin.logout');

// Admin Protected Routes
Route::prefix('admin')->name('admin.')->middleware(['role:admin,moderator'])->group(function () {
    Route::get('/', function () {
        return view('admin.layouts.app');
    })->name('dashboard');

    // Categories
    Route::resource('categories', \App\Http\Controllers\Admin\CategoryController::class);
    
    // Locations
    Route::resource('locations', \App\Http\Controllers\Admin\LocationController::class);
    
    // Location Images Ajax
    Route::post('locations/{location}/upload-image', [\App\Http\Controllers\Admin\LocationController::class, 'uploadImage'])->name('locations.upload_image');
    Route::delete('locations/image/{image}', [\App\Http\Controllers\Admin\LocationController::class, 'deleteImage'])->name('locations.delete_image');
    
    // Location Panoramas Ajax
    Route::post('locations/{location}/upload-pano', [\App\Http\Controllers\Admin\LocationController::class, 'uploadPanorama'])->name('locations.upload_pano');
    Route::delete('locations/pano/{panorama}', [\App\Http\Controllers\Admin\LocationController::class, 'deletePanorama'])->name('locations.delete_pano');

    // 360 Editor
    Route::get('locations/{location}/360-editor', [\App\Http\Controllers\Admin\PanoramaEditorController::class, 'index'])->name('locations.360_editor');
    Route::get('locations/{location}/360-data', [\App\Http\Controllers\Admin\PanoramaEditorController::class, 'getData']);
    Route::post('panoramas/{panorama}/set-default', [\App\Http\Controllers\Admin\PanoramaEditorController::class, 'setDefaultScene']);
    Route::post('panoramas/{panorama}/initial-view', [\App\Http\Controllers\Admin\PanoramaEditorController::class, 'setInitialView']);
    Route::post('panoramas/{panorama}/hotspots', [\App\Http\Controllers\Admin\PanoramaEditorController::class, 'addHotspot']);
    Route::post('hotspots/bulk', [\App\Http\Controllers\Admin\PanoramaEditorController::class, 'bulkSave'])->name('hotspots.bulk');
    Route::put('hotspots/{hotspot}', [\App\Http\Controllers\Admin\PanoramaEditorController::class, 'updateHotspot']);
    Route::delete('hotspots/{hotspot}', [\App\Http\Controllers\Admin\PanoramaEditorController::class, 'deleteHotspot']);
    Route::put('panoramas/{panorama}/name', [\App\Http\Controllers\Admin\PanoramaEditorController::class, 'updateSceneName']);

    // News Management
    Route::resource('news', NewsController::class);
    Route::patch('news/{news}/toggle', [NewsController::class, 'toggleVisibility'])->name('news.toggle');
    Route::post('news/upload-image', [NewsController::class, 'uploadImage'])->name('news.upload_image');

    // Events Management
    Route::resource('events', EventController::class);
    Route::patch('events/{event}/toggle', [EventController::class, 'toggleVisibility'])->name('events.toggle');

    // Panorama Audio
    Route::post('locations/{location}/upload-audio', [\App\Http\Controllers\Admin\LocationController::class, 'uploadAudio'])->name('locations.upload_audio');
    Route::delete('locations/{location}/delete-audio', [\App\Http\Controllers\Admin\LocationController::class, 'deleteAudio'])->name('locations.delete_audio');
});

