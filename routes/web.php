<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\AuthController;
use App\Http\Controllers\Admin\NewsController;
use App\Http\Controllers\Admin\EventController;

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

    return view('client.home', compact('locations'));
})->name('home');

// Client 360 Viewer
Route::get('locations/{location:slug}/360', function(\App\Models\Location $location) {
    return view('client.360', compact('location'));
})->name('client.locations.360');


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

