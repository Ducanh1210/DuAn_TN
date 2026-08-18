<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\BusinessProfile;
use App\Models\Event;
use App\Models\Location;
use App\Models\News;
use App\Models\PanoramaServiceRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

/**
 * Controller trang giới thiệu (landing) và dịch vụ tour 360°.
 * Gồm trang chủ giới thiệu (địa điểm nổi bật, tin tức, sự kiện) và trang/dịch vụ
 * nhận yêu cầu chụp tour 360° cho cả khách chưa đăng nhập.
 */
class LandingController extends Controller
{
    /** Trang chủ giới thiệu: địa điểm nổi bật, tin mới, sự kiện sắp tới và vài số liệu tổng quan. */
    public function index()
    {
        $featuredLocations = Location::query()
            ->where('status', 'published')
            ->with(['category', 'images'])
            ->withCount('panoramas')
            ->orderByDesc('view_count')
            ->take(6)
            ->get()
            ->each(fn ($loc) => $loc->setAttribute('display_thumbnail', $loc->resolveThumbnailUrl()));

        $latestNews = News::query()
            ->where('status', 'published')
            ->where('type', '!=', 'event')
            ->orderByDesc('published_at')
            ->take(4)
            ->get();

        $upcomingEvents = Event::query()
            ->where('status', 'active')
            ->where('start_time', '>=', now())
            ->orderBy('start_time')
            ->take(3)
            ->get();

        $stats = [
            'locations' => Location::where('status', 'published')->count(),
            'news' => News::where('status', 'published')->count(),
            'events' => Event::where('status', 'active')->count(),
        ];

        return view('client.landing', compact(
            'featuredLocations',
            'latestNews',
            'upcomingEvents',
            'stats'
        ));
    }

    /**
     * Trang "Giới thiệu": nội dung biên tập về vùng đất, kèm vài địa điểm tiêu biểu
     * lấy từ CSDL để minh họa cho chương "Kiệt tác thiên nhiên".
     */
    public function about()
    {
        $showcase = Location::query()
            ->where('status', 'published')
            ->with('category')
            ->orderByDesc('view_count')
            ->take(3)
            ->get()
            ->each(fn ($loc) => $loc->setAttribute('display_thumbnail', $loc->resolveThumbnailUrl()));

        return view('client.about', compact('showcase'));
    }

    /**
     * Trang dịch vụ tour 360 + form gửi yêu cầu (công khai, không cần đăng nhập).
     */
    public function panoService()
    {
        $demoLocation = Location::query()
            ->whereHas('panoramas')
            ->where('status', 'published')
            ->with(['panoramas' => fn ($q) => $q->orderByDesc('is_default')->orderBy('sort_order')])
            ->first();

        if (!$demoLocation) {
            $demoLocation = Location::query()
                ->whereHas('panoramas')
                ->with(['panoramas' => fn ($q) => $q->orderByDesc('is_default')->orderBy('sort_order')])
                ->first();
        }

        $demoPano = $demoLocation?->panoramas?->first();
        $demoImg = $demoPano?->image_url
            ? asset('storage/' . ltrim($demoPano->image_url, '/'))
            : null;
        $demoUrl = $demoLocation
            ? route('client.locations.360', $demoLocation->slug)
            : null;

        $user = Auth::user();
        $defaultContact = $user?->display_name ?? $user?->username ?? old('contact_name', '');
        $defaultPhone = old('phone', '');
        $defaultPlace = old('place_name', '');

        if ($user) {
            $biz = BusinessProfile::where('user_id', $user->id)->where('status', 'approved')->first();
            if ($biz) {
                $defaultPhone = old('phone', $biz->phone ?: $defaultPhone);
                $defaultPlace = old('place_name', $biz->business_name ?: $defaultPlace);
            }
        }

        return view('client.pano-service', compact(
            'demoLocation',
            'demoImg',
            'demoUrl',
            'defaultContact',
            'defaultPhone',
            'defaultPlace'
        ));
    }

    /**
     * Nhận form yêu cầu tour 360 — khách hoặc đã đăng nhập đều gửi được.
     */
    public function submitPanoService(Request $request)
    {
        $data = $request->validate([
            'contact_name' => 'required|string|max:120',
            'phone' => 'required|string|max:30',
            'place_name' => 'required|string|max:180',
            'place_type' => 'nullable|in:homestay,restaurant,attraction,other',
            'scene_estimate' => 'nullable|in:1-2,3-5,6+,unsure',
            'note' => 'nullable|string|max:800',
            'from' => 'nullable|in:profile,business,public',
        ], [
            'contact_name.required' => 'Vui lòng nhập tên liên hệ.',
            'phone.required' => 'Vui lòng nhập số điện thoại / Zalo.',
            'place_name.required' => 'Vui lòng nhập tên địa điểm.',
        ]);

        $from = $data['from'] ?? 'public';
        $redirectTo = match ($from) {
            'business' => route('business.dashboard') . '#tab-pano',
            'profile' => route('client.profile') . '#tab-pano-service',
            default => route('client.pano_service'),
        };

        $phone = trim($data['phone']);
        $user = Auth::user();

        // Chặn gửi trùng: đã có yêu cầu đang chờ theo cùng SĐT (hoặc cùng tài khoản)
        $pendingQuery = PanoramaServiceRequest::where('status', 'pending')
            ->where(function ($q) use ($user, $phone) {
                $q->where('phone', $phone);
                if ($user) {
                    $q->orWhere('user_id', $user->id);
                }
            });

        if ($pendingQuery->exists()) {
            return redirect()
                ->to($redirectTo)
                ->withInput()
                ->with('error', 'Đã có yêu cầu đang chờ liên hệ với SĐT này. Vui lòng đợi phản hồi trước khi gửi thêm.');
        }

        PanoramaServiceRequest::create([
            'user_id' => $user?->id,
            'contact_name' => $data['contact_name'],
            'phone' => $phone,
            'place_name' => $data['place_name'],
            'place_type' => $data['place_type'] ?? null,
            'scene_estimate' => $data['scene_estimate'] ?? null,
            'note' => $data['note'] ?? null,
            'status' => 'pending',
        ]);

        return redirect()
            ->to($redirectTo)
            ->with('success', 'Đã gửi yêu cầu. Chúng tôi sẽ liên hệ qua SĐT/Zalo để tư vấn và báo giá theo nhu cầu của bạn.');
    }
}
