<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\NewsController;
use App\Http\Controllers\Admin\EventController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Client\NewsController as ClientNewsController;
use App\Http\Controllers\Client\EventController as ClientEventController;
use App\Http\Controllers\Client\LandingController;
use App\Http\Controllers\Client\ChatbotController;

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

Route::get('/', [LandingController::class, 'index'])->name('client.landing');
Route::get('/trang-chu', [LandingController::class, 'index']);
Route::get('/gioi-thieu', [LandingController::class, 'about'])->name('client.about');
Route::get('/dich-vu-tour-360', [LandingController::class, 'panoService'])->name('client.pano_service');
Route::post('/dich-vu-tour-360', [LandingController::class, 'submitPanoService'])->name('client.pano_service.submit');

Route::get('/api/location/provinces', [\App\Http\Controllers\Api\LocationController::class, 'getProvinces']);
Route::get('/api/location/wards/{provinceCode}', [\App\Http\Controllers\Api\LocationController::class, 'getWards']);


Route::get('/ban-do', function () {
    $locations = \App\Models\Location::with(['category', 'images'])->withCount('panoramas')->where('status', 'published')->get();
    if ($locations->isEmpty()) {
        $locations = \App\Models\Location::with(['category', 'images'])->withCount('panoramas')->get();
    }

    $locations->each(function ($loc) {
        if ($loc->category && $loc->category->icon) {
            $loc->category->icon_url = asset($loc->category->icon);
        }
        $loc->thumbnail_url = $loc->resolveThumbnailUrl();
    });

    // Lấy 3 tin tức mới nhất cho banner
    $newsList = \App\Models\News::where('status', 'published')
        ->where('type', '!=', 'event')
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

// Client 360 Viewer / Photo gallery
Route::get('locations/{location:slug}/360', function (\App\Models\Location $location) {
    if (auth()->check()) {
        \App\Services\MissionService::trackProgress(auth()->user(), 'vr_360_view', 1, false, $location->id);
        auth()->user()->loadMissing('equippedFrame');
    }

    $location->increment('view_count');

    $location->load([
        'category',
        'images',
        'panoramas.hotspots',
        'comments.user.equippedFrame',
        'comments.replies.user',
    ]);

    $userIds = $location->comments->pluck('user_id')->filter()->unique()->values();
    $commentCounts = \App\Models\Comment::whereIn('user_id', $userIds)
        ->where('status', 'visible')
        ->whereNull('parent_id')
        ->selectRaw('user_id, COUNT(*) as aggregate')
        ->groupBy('user_id')
        ->pluck('aggregate', 'user_id');
    foreach ($location->comments as $c) {
        if ($c->user) {
            $c->user->setAttribute('comments_count_cached', (int) ($commentCounts[$c->user_id] ?? 1));
        }
    }

    $galleryImages = $location->resolveImageUrls();
    $heroImage = $location->resolveThumbnailUrl();

    return view('client.360', compact('location', 'galleryImages', 'heroImage'));
})->name('client.locations.360');

// Client News & Events
Route::get('/tin-tuc', [ClientNewsController::class, 'index'])->name('client.news.index');
Route::get('/tin-tuc/{slug}', [ClientNewsController::class, 'show'])->name('client.news.show');
Route::get('/su-kien', [ClientEventController::class, 'index'])->name('client.events.index');
Route::get('/su-kien/{slug}', [ClientEventController::class, 'show'])->name('client.events.show');

// Client Interactions
Route::post('/chat/message', [ChatbotController::class, 'sendMessage'])->name('client.chat.message');
Route::post('/trip-planner/generate', [\App\Http\Controllers\Client\TripPlannerController::class, 'generate'])->name('client.trip_planner.generate');

Route::middleware('auth')->group(function () {
    Route::post('/trip-planner/save', [\App\Http\Controllers\Client\TripPlannerController::class, 'save'])->name('client.trip_planner.save');
    Route::get('/trip-planner/{id}', [\App\Http\Controllers\Client\TripPlannerController::class, 'show'])->name('client.trip_planner.show');
    Route::delete('/trip-planner/{id}', [\App\Http\Controllers\Client\TripPlannerController::class, 'destroy'])->name('client.trip_planner.destroy');
});

Route::middleware('auth')->group(function () {
    Route::post('/locations/{location}/favorite', [\App\Http\Controllers\Client\InteractionController::class, 'toggleFavorite'])->name('client.locations.favorite');
    Route::post('/locations/{location}/comment', [\App\Http\Controllers\Client\InteractionController::class, 'storeComment'])->name('client.locations.comment');
    Route::put('/comments/{comment}', [\App\Http\Controllers\Client\InteractionController::class, 'updateComment'])->name('client.comments.update');
    Route::delete('/comments/{comment}', [\App\Http\Controllers\Client\InteractionController::class, 'deleteComment'])->name('client.comments.destroy');
    Route::get('/ca-nhan/dia-diem-yeu-thich', [\App\Http\Controllers\Client\InteractionController::class, 'myFavorites'])->name('client.favorites.index');
    Route::post('/report', [\App\Http\Controllers\Client\InteractionController::class, 'report'])->name('client.report');

    // Community Contributions
    Route::get('/ca-nhan/dong-gop', [\App\Http\Controllers\Client\InteractionController::class, 'myContributions'])->name('client.contributions.index');
    Route::post('/locations/suggest', [\App\Http\Controllers\Client\InteractionController::class, 'suggestLocation'])->name('client.locations.suggest');

    // Góp ý / báo lỗi: bắt buộc đăng nhập
    Route::post('/feedback', [\App\Http\Controllers\Client\InteractionController::class, 'submitFeedback'])->name('client.feedback.submit');
});

// Auth Routes
Route::middleware('guest')->group(function () {
    Route::get('/login', [\App\Http\Controllers\Client\AuthController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [\App\Http\Controllers\Client\AuthController::class, 'login']);
    Route::get('/register', [\App\Http\Controllers\Client\AuthController::class, 'showRegisterForm'])->name('register');
    Route::post('/register', [\App\Http\Controllers\Client\AuthController::class, 'register']);

    // Google OAuth
    Route::get('/auth/google', [\App\Http\Controllers\Client\AuthController::class, 'redirectToGoogle'])->name('client.login.google');
    Route::get('/auth/google/callback', [\App\Http\Controllers\Client\AuthController::class, 'handleGoogleCallback']);
});

Route::post('/logout', [\App\Http\Controllers\Client\AuthController::class, 'logout'])->name('logout');

// Client Profile & Settings Routes
Route::middleware('auth')->group(function () {
    Route::get('/profile', [\App\Http\Controllers\Client\ProfileController::class, 'index'])->name('client.profile');
    Route::post('/profile/update', [\App\Http\Controllers\Client\ProfileController::class, 'update'])->name('client.profile.update');
    Route::post('/profile/password', [\App\Http\Controllers\Client\ProfileController::class, 'updatePassword'])->name('client.profile.password');
    Route::post('/profile/avatar', [\App\Http\Controllers\Client\ProfileController::class, 'updateAvatar'])->name('client.profile.avatar');
    Route::post('/profile/delete', [\App\Http\Controllers\Client\ProfileController::class, 'deleteAccount'])->name('client.profile.delete');
    Route::get('/profile/business/upgrade', [\App\Http\Controllers\Client\ProfileController::class, 'showBusinessUpgradeForm'])->name('client.profile.business.upgrade');
    Route::get('/profile/business/locations/suggest', [\App\Http\Controllers\Client\ProfileController::class, 'suggestClaimableLocations'])->name('client.profile.business.locations.suggest');
    Route::post('/profile/business/register', [\App\Http\Controllers\Client\ProfileController::class, 'businessRegister'])->name('client.profile.business.register');
    Route::post('/profile/business/upload-photo', [\App\Http\Controllers\Client\ProfileController::class, 'uploadBusinessPhoto'])->name('client.profile.business.upload_photo');
    Route::post('/profile/business/cancel', [\App\Http\Controllers\Client\ProfileController::class, 'cancelBusinessRegistration'])->name('client.profile.business.cancel');
    Route::post('/profile/pano-service', [\App\Http\Controllers\Client\LandingController::class, 'submitPanoService'])->name('client.profile.pano_service');
    Route::post('/profile/favorite/toggle', [\App\Http\Controllers\Client\ProfileController::class, 'toggleFavorite'])->name('client.profile.favorite.toggle');
    Route::delete('/profile/comments/{comment}', [\App\Http\Controllers\Client\ProfileController::class, 'destroyComment'])->name('client.profile.comments.destroy');
    Route::post('/profile/heartbeat', [\App\Http\Controllers\Client\ProfileController::class, 'heartbeat'])->name('client.profile.heartbeat');
    Route::post('/profile/claim-daily', [\App\Http\Controllers\Client\ProfileController::class, 'claimDaily'])->name('client.profile.claim_daily');
    
    // Missions & Avatar Frame Routes
    Route::post('/missions/claim/{mission}', [\App\Http\Controllers\Client\ProfileController::class, 'claimMissionReward'])->name('client.missions.claim');
    Route::post('/missions/claim-milestone', [\App\Http\Controllers\Client\ProfileController::class, 'claimMilestone100'])->name('client.missions.claim_milestone');
    Route::post('/avatar-frames/equip', [\App\Http\Controllers\Client\ProfileController::class, 'equipAvatarFrame'])->name('client.avatar_frames.equip');
    Route::post('/avatar-frames/buy/{frame}', [\App\Http\Controllers\Client\ProfileController::class, 'buyAvatarFrame'])->name('client.avatar_frames.buy');
    Route::post('/rewards/redeem/{reward}', [\App\Http\Controllers\Client\ProfileController::class, 'redeemReward'])->name('client.rewards.redeem');

    // Dedicated Business Dashboard Portal (Portal Dành Cho Chủ Doanh Nghiệp)
    Route::get('/doanh-nghiep/dashboard', [\App\Http\Controllers\Client\BusinessDashboardController::class, 'index'])->name('business.dashboard');
    Route::post('/doanh-nghiep/update-info', [\App\Http\Controllers\Client\BusinessDashboardController::class, 'updateInfo'])->name('business.update_info');
    Route::post('/doanh-nghiep/update-contact', [\App\Http\Controllers\Client\BusinessDashboardController::class, 'updateContact'])->name('business.update_contact');
    Route::post('/doanh-nghiep/comments/{comment}/reply', [\App\Http\Controllers\Client\BusinessDashboardController::class, 'replyComment'])->name('business.reply_comment');
    Route::delete('/doanh-nghiep/comments/{comment}/reply', [\App\Http\Controllers\Client\BusinessDashboardController::class, 'deleteReply'])->name('business.delete_reply');
    Route::post('/doanh-nghiep/upload-photo', [\App\Http\Controllers\Client\BusinessDashboardController::class, 'uploadPhoto'])->name('business.upload_photo');
    Route::delete('/doanh-nghiep/photos', [\App\Http\Controllers\Client\BusinessDashboardController::class, 'deletePhoto'])->name('business.delete_photo');
});

// Public Missions Route
Route::get('/missions', [\App\Http\Controllers\Client\ProfileController::class, 'missions'])->name('client.missions');

// Admin Protected Routes
Route::prefix('admin')->name('admin.')->middleware(['role:admin,moderator'])->group(function () {
    Route::get('/', function () {
        return view('admin.dashboard');
    })->name('dashboard');

    // Categories
    Route::resource('categories', \App\Http\Controllers\Admin\CategoryController::class);
    // Locations TTS
    Route::get('locations/tts-voices', [\App\Http\Controllers\Admin\LocationController::class, 'getTtsVoices'])->name('locations.tts_voices');


    // Locations
    Route::resource('locations', \App\Http\Controllers\Admin\LocationController::class);
    Route::post('locations/{id}/restore', [\App\Http\Controllers\Admin\LocationController::class, 'restore'])
        ->whereNumber('id')
        ->name('locations.restore');
    Route::delete('locations/{id}/force', [\App\Http\Controllers\Admin\LocationController::class, 'forceDestroy'])
        ->whereNumber('id')
        ->name('locations.force_destroy');
    Route::post('locations/{location}/revoke-business', [\App\Http\Controllers\Admin\LocationController::class, 'revokeBusiness'])
        ->name('locations.revoke_business');

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
    // Users Management
    Route::resource('users', UserController::class);
    Route::patch('users/{user}/toggle-status', [UserController::class, 'toggleStatus'])->name('users.toggle_status');
    Route::post('locations/{location}/generate-tts', [\App\Http\Controllers\Admin\LocationController::class, 'generateTtsAudio'])->name('locations.generate_tts');

    // Comments Management
    Route::resource('comments', \App\Http\Controllers\Admin\CommentController::class)->only(['index', 'destroy']);
    Route::patch('comments/{comment}/toggle-status', [\App\Http\Controllers\Admin\CommentController::class, 'toggleStatus'])->name('comments.toggle_status');
    Route::post('comments/scan-ai', [\App\Http\Controllers\Admin\CommentController::class, 'scanAi'])->name('comments.scan_ai');

    // Reports Management
    Route::resource('reports', \App\Http\Controllers\Admin\ReportController::class)->only(['index']);
    Route::patch('reports/{report}/status', [\App\Http\Controllers\Admin\ReportController::class, 'updateStatus'])->name('reports.update_status');
    Route::delete('reports/feedbacks/{id}', [\App\Http\Controllers\Admin\ReportController::class, 'destroyFeedback'])->name('reports.feedbacks.destroy');
    Route::delete('reports/{report}', [\App\Http\Controllers\Admin\ReportController::class, 'destroy'])->whereNumber('report')->name('reports.destroy');
    Route::get('reports/feedbacks/{id}', [\App\Http\Controllers\Admin\ReportController::class, 'showFeedback'])->name('reports.feedbacks.show');
    Route::put('reports/feedbacks/{id}', [\App\Http\Controllers\Admin\ReportController::class, 'updateFeedback'])->name('reports.feedbacks.update');

    // Đóng góp người dùng — đề xuất địa điểm (tham khảo)
    Route::get('contributions', [\App\Http\Controllers\Admin\ContributionController::class, 'index'])->name('contributions.index');
    Route::get('contributions/suggestions/{id}', [\App\Http\Controllers\Admin\ContributionController::class, 'showSuggestion'])->name('contributions.suggestions.show');
    Route::put('contributions/suggestions/{id}', [\App\Http\Controllers\Admin\ContributionController::class, 'updateSuggestion'])->name('contributions.suggestions.update');
    Route::delete('contributions/suggestions/{id}', [\App\Http\Controllers\Admin\ContributionController::class, 'destroySuggestion'])->name('contributions.suggestions.destroy');

    // Legacy redirects
    Route::redirect('location_suggestions', '/admin/contributions');
    Route::redirect('feedbacks', '/admin/reports?tab=feedbacks');
    Route::get('contributions/feedbacks/{id}', fn ($id) => redirect()->route('admin.reports.feedbacks.show', $id));

    // Business Upgrade Requests Management (Quản lý yêu cầu doanh nghiệp)
    Route::resource('business-profiles', \App\Http\Controllers\Admin\BusinessProfileController::class)->only(['index', 'show']);
    Route::post('business-profiles/{id}/approve', [\App\Http\Controllers\Admin\BusinessProfileController::class, 'approve'])->name('business-profiles.approve');
    Route::post('business-profiles/{id}/reject', [\App\Http\Controllers\Admin\BusinessProfileController::class, 'reject'])->name('business-profiles.reject');

    // Yêu cầu làm tour 360 thuê
    Route::get('panorama-requests', [\App\Http\Controllers\Admin\PanoramaServiceRequestController::class, 'index'])->name('panorama-requests.index');
    Route::patch('panorama-requests/{panoramaRequest}', [\App\Http\Controllers\Admin\PanoramaServiceRequestController::class, 'updateStatus'])->name('panorama-requests.update');
});
