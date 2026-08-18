<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\Event;
use App\Models\News;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

/**
 * Controller trang "Sự kiện & Tin tức": một dòng nội dung gộp chung sự kiện (bảng events)
 * và bài viết (bảng news), lọc theo danh mục và phân trang thủ công vì dữ liệu đến từ hai bảng.
 */
class EventController extends Controller
{
    /** Số mục hiển thị trên mỗi trang của lưới nội dung. */
    private const PER_PAGE = 6;

    /**
     * Danh mục lọc: khóa trên URL => [nhãn hiển thị, loại nguồn].
     * "source" là 'event' (lấy từ bảng events) hoặc một giá trị cột news.type.
     */
    private const CATEGORIES = [
        'all' => ['label' => 'Tất cả', 'source' => null],
        'su-kien' => ['label' => 'Sự kiện & Lễ hội', 'source' => 'event'],
        'tin-tuc' => ['label' => 'Tin tức', 'source' => 'news'],
        'cam-nang' => ['label' => 'Cẩm nang du lịch', 'source' => 'guide'],
        'thong-bao' => ['label' => 'Thông báo', 'source' => 'announcement'],
    ];

    /** Trang danh sách gộp sự kiện + tin tức, có lọc danh mục và phân trang. */
    public function index(Request $request)
    {
        $cat = $request->get('cat', 'all');
        if (!array_key_exists($cat, self::CATEGORIES)) {
            $cat = 'all';
        }

        $ordered = $this->orderItems($this->collectItems($cat));

        // Khối đầu trang kiểu "Cẩm nang": 1 bài lớn + 2 bài phụ.
        $featured = $ordered->first();
        $subFeatured = $ordered->slice(1, 2)->values();
        $rest = $ordered->count() > 3
            ? $ordered->slice(3)->values()
            : $ordered->values();

        $page = LengthAwarePaginator::resolveCurrentPage();
        $items = new LengthAwarePaginator(
            $rest->forPage($page, self::PER_PAGE)->values(),
            $rest->count(),
            self::PER_PAGE,
            $page,
            ['path' => $request->url(), 'query' => $request->query()]
        );

        $categories = $this->categoriesWithCounts();

        return view('client.events.index', compact('items', 'featured', 'subFeatured', 'categories', 'cat'));
    }

    /** Trang chi tiết một sự kiện kèm các sự kiện liên quan. */
    public function show($slug)
    {
        $event = Event::where('slug', $slug)
            ->where('status', 'active')
            ->firstOrFail();

        $relatedEvents = Event::where('status', 'active')
            ->where('id', '!=', $event->id)
            ->orderByDesc('start_time')
            ->take(4)
            ->get();

        return view('client.events.show', compact('event', 'relatedEvents'));
    }

    /** Gom sự kiện và bài viết của danh mục đang chọn về một danh sách có cấu trúc chung. */
    private function collectItems(string $cat): Collection
    {
        $source = self::CATEGORIES[$cat]['source'];
        $items = collect();

        if ($source === null || $source === 'event') {
            $events = Event::query()
                ->where('status', 'active')
                ->orderByDesc('start_time')
                ->get();

            foreach ($events as $event) {
                $items->push($this->mapEvent($event));
            }
        }

        if ($source !== 'event') {
            $news = News::query()
                ->where('status', 'published')
                ->where('type', '!=', 'event')
                ->when($source !== null, fn ($q) => $q->where('type', $source))
                ->orderByDesc('published_at')
                ->get();

            foreach ($news as $article) {
                $items->push($this->mapNews($article));
            }
        }

        return $items;
    }

    /** Chuẩn hóa một sự kiện về cấu trúc chung của lưới nội dung. */
    private function mapEvent(Event $event): array
    {
        $date = $event->start_time;

        return [
            'kind' => 'event',
            'label' => 'Sự kiện',
            'title' => $event->name,
            'url' => route('client.events.show', $event->slug),
            'image' => $event->featured_image_url,
            'date' => $date,
            'excerpt' => Str::limit(trim(strip_tags((string) $event->description)), 180),
            'sort_at' => $date?->timestamp ?? 0,
            'is_upcoming' => $date !== null && $date->isFuture(),
        ];
    }

    /** Chuẩn hóa một bài viết về cấu trúc chung của lưới nội dung. */
    private function mapNews(News $article): array
    {
        $date = $article->published_at ?? $article->created_at;
        $excerpt = $article->summary ?: strip_tags((string) $article->content);

        return [
            'kind' => 'news',
            'label' => $article->type_label,
            'title' => $article->title,
            'url' => route('client.news.show', $article->slug),
            'image' => $article->featured_image_url,
            'date' => $date,
            'excerpt' => Str::limit(trim($excerpt), 180),
            'sort_at' => $date?->timestamp ?? 0,
            'is_upcoming' => false,
        ];
    }

    /**
     * Sắp xếp theo logic biên tập: sự kiện sắp diễn ra lên đầu (gần nhất trước),
     * phần còn lại xếp theo thời gian giảm dần.
     */
    private function orderItems(Collection $items): Collection
    {
        $upcoming = $items->filter(fn ($i) => $i['is_upcoming'])->sortBy('sort_at')->values();
        $others = $items->reject(fn ($i) => $i['is_upcoming'])->sortByDesc('sort_at')->values();

        return $upcoming->concat($others)->values();
    }

    /** Danh sách danh mục kèm số lượng mục, dùng cho thanh lọc bên trái. */
    private function categoriesWithCounts(): array
    {
        $eventCount = Event::where('status', 'active')->count();

        $newsCounts = News::query()
            ->where('status', 'published')
            ->where('type', '!=', 'event')
            ->selectRaw('type, COUNT(*) as total')
            ->groupBy('type')
            ->pluck('total', 'type');

        $result = [];

        foreach (self::CATEGORIES as $key => $meta) {
            $count = match ($meta['source']) {
                null => $eventCount + $newsCounts->sum(),
                'event' => $eventCount,
                default => (int) ($newsCounts[$meta['source']] ?? 0),
            };

            if ($key !== 'all' && $count === 0) {
                continue;
            }

            $result[$key] = [
                'label' => $meta['label'],
                'count' => $count,
            ];
        }

        return $result;
    }
}
