<?php

namespace App\Services;

use Illuminate\Support\Facades\Log;
use App\Models\Location;
use App\Models\Itinerary;
use App\Models\ItineraryDay;
use App\Models\ItineraryItem;
use Illuminate\Support\Facades\DB;

/**
 * Dịch vụ lập lịch trình du lịch bằng AI.
 * Quy trình: đọc khảo sát -> lọc & gom cụm GPS -> Gemini sinh JSON
 * -> kiểm tra location_id / số ngày / ngân sách -> lưu DB.
 */
class TripPlannerService
{
    protected GeminiClient $gemini;

    /** @var array<int, Location>|null */
    protected $locationsById = null;

    public function __construct(?GeminiClient $gemini = null)
    {
        $this->gemini = $gemini ?? new GeminiClient();
    }

    /**
     * Phân tích câu trả lời khảo sát thành bộ sở thích chuẩn hóa
     * (số ngày, ngân sách, nhịp độ, sở thích, ẩm thực...) để dựng prompt.
     */
    public function parsePreferences(array $answers, ?string $tripType = null): array
    {
        $prefs = [
            'trip_type' => $tripType ?: null,
            'days' => 2,
            'need_hotel' => true,
            'budget_key' => 'tieu_chuan',
            'budget_min' => 1000000,
            'budget_max' => 2500000,
            'budget_label' => '1.000.000 - 2.500.000 VNĐ / người',
            'transport' => null,
            'pace' => 'can_bang',
            'slots_per_day' => [3, 4],
            'interests' => [],
            'food' => null,
            'focus' => null,
            'who' => null,
            'preferred_location_ids' => [],
        ];

        foreach ($answers as $a) {
            $key = $a['key'] ?? null;
            $value = $a['value'] ?? null;
            $answer = (string) ($a['answer'] ?? '');
            $blob = mb_strtolower($key . ' ' . $value . ' ' . $answer);

            if ($key === 'duration_hotel' || str_contains($blob, 'bao lâu') || str_contains($blob, 'khách sạn') || str_contains($blob, 'ngày')) {
                if ($value === '1_day' || preg_match('/\b1\s*ngày\b|không ở lại/', $blob)) {
                    $prefs['days'] = 1;
                    $prefs['need_hotel'] = false;
                } elseif ($value === '3d2n_hotel' || preg_match('/3\s*ngày|2\s*đêm/', $blob)) {
                    $prefs['days'] = 3;
                    $prefs['need_hotel'] = true;
                } elseif ($value === '2d1n_hotel' || preg_match('/2\s*ngày|1\s*đêm/', $blob)) {
                    $prefs['days'] = 2;
                    $prefs['need_hotel'] = true;
                }
            }

            if ($key === 'budget' || str_contains($blob, 'ngân sách') || str_contains($blob, 'chi phí')) {
                if ($value === 'tiet_kiem' || str_contains($blob, 'tiết kiệm') || str_contains($blob, 'dưới 1')) {
                    $prefs['budget_key'] = 'tiet_kiem';
                    $prefs['budget_min'] = 400000;
                    $prefs['budget_max'] = 999000;
                    $prefs['budget_label'] = '400.000 - 999.000 VNĐ / người';
                } elseif ($value === 'cao_cap' || str_contains($blob, 'cao cấp') || str_contains($blob, 'thoải mái') || str_contains($blob, '> 2.5') || str_contains($blob, '2.5 triệu')) {
                    $prefs['budget_key'] = 'cao_cap';
                    $prefs['budget_min'] = 2500000;
                    $prefs['budget_max'] = 5500000;
                    $prefs['budget_label'] = '2.500.000 - 5.500.000 VNĐ / người';
                } else {
                    $prefs['budget_key'] = 'tieu_chuan';
                    $prefs['budget_min'] = 1000000;
                    $prefs['budget_max'] = 2500000;
                    $prefs['budget_label'] = '1.000.000 - 2.500.000 VNĐ / người';
                }
            }

            if ($key === 'transport' || str_contains($blob, 'phương tiện')) {
                $prefs['transport'] = $value ?: $answer;
            }

            if ($key === 'who' || str_contains($blob, 'đi cùng')) {
                $prefs['who'] = $value ?: $answer;
            }

            if ($key === 'pace' || str_contains($blob, 'nhịp độ') || str_contains($blob, 'dày hay')) {
                if ($value === 'cham_rai' || str_contains($blob, 'chậm rãi') || str_contains($blob, 'thư giãn')) {
                    $prefs['pace'] = 'cham_rai';
                    $prefs['slots_per_day'] = [2, 3];
                } elseif ($value === 'dap_dong' || str_contains($blob, 'dồn dập') || str_contains($blob, 'nhiều điểm')) {
                    $prefs['pace'] = 'dap_dong';
                    $prefs['slots_per_day'] = [4, 5];
                } else {
                    $prefs['pace'] = 'can_bang';
                    $prefs['slots_per_day'] = [3, 4];
                }
            }

            if ($key === 'interests' || str_contains($blob, 'ưu tiên trải nghiệm')) {
                $vals = array_filter(array_map('trim', explode(',', (string) ($value ?: ''))));
                if (empty($vals) && $answer !== '') {
                    $mapLabel = [
                        'tâm linh' => 'tam_linh',
                        'ẩm thực' => 'am_thuc',
                        'check-in' => 'check_in',
                        'sống ảo' => 'check_in',
                        'thiên nhiên' => 'thien_nhien',
                        'sinh thái' => 'thien_nhien',
                        'văn hóa' => 'van_hoa',
                        'nghỉ dưỡng' => 'nghi_duong',
                    ];
                    foreach ($mapLabel as $needle => $slug) {
                        if (str_contains(mb_strtolower($answer), $needle)) {
                            $vals[] = $slug;
                        }
                    }
                }
                $prefs['interests'] = array_values(array_unique($vals));
            }

            if ($key === 'food' || str_contains($blob, 'ẩm thực nào')) {
                $prefs['food'] = $value ?: $answer;
            }

            if ($key === 'focus' || str_contains($blob, 'quan trọng nhất')) {
                $prefs['focus'] = $value ?: $answer;
            }

            if (!$prefs['trip_type'] && ($key === 'trip_type' || str_contains(mb_strtolower($a['question'] ?? ''), 'kiểu chuyến'))) {
                $prefs['trip_type'] = $value ?: $answer;
            }

            if ($key === 'preferred_locations' && $value) {
                $prefs['preferred_location_ids'] = array_filter(array_map('intval', explode(',', $value)));
            }
        }

        if (!$prefs['trip_type'] && $tripType) {
            $prefs['trip_type'] = $tripType;
        }

        return $prefs;
    }

    /**
     * Ánh xạ các key sở thích -> slug danh mục địa điểm tương ứng.
     */
    protected function categorySlugsForInterests(array $interests): array
    {
        $map = [
            'tam_linh' => ['tam-linh'],
            'am_thuc' => ['am-thuc'],
            'check_in' => ['check-in'],
            'thien_nhien' => ['sinh-thai'],
            'van_hoa' => ['van-hoa-lich-su'],
            'nghi_duong' => ['luu-tru', 'sinh-thai'],
        ];
        $slugs = [];
        foreach ($interests as $interest) {
            $key = mb_strtolower(trim($interest));
            if (isset($map[$key])) {
                $slugs = array_merge($slugs, $map[$key]);
            }
        }
        return array_values(array_unique($slugs));
    }

    /**
     * Ánh xạ kiểu chuyến đi -> danh sách slug danh mục cần ưu tiên.
     */
    protected function categorySlugsForTripType(?string $tripType): array
    {
        $t = mb_strtolower((string) $tripType);
        $map = [
            'spiritual' => ['tam-linh', 'van-hoa-lich-su'],
            'tâm linh' => ['tam-linh', 'van-hoa-lich-su'],
            'food_tour' => ['am-thuc', 'check-in'],
            'food tour' => ['am-thuc', 'check-in'],
            'check_in' => ['check-in', 'sinh-thai', 'van-hoa-lich-su'],
            'check-in' => ['check-in', 'sinh-thai', 'van-hoa-lich-su'],
            'family' => ['sinh-thai', 'check-in', 'van-hoa-lich-su', 'am-thuc'],
            'gia đình' => ['sinh-thai', 'check-in', 'van-hoa-lich-su', 'am-thuc'],
            'couple' => ['check-in', 'am-thuc', 'sinh-thai'],
            'resort' => ['luu-tru', 'sinh-thai', 'check-in'],
            'nghỉ dưỡng' => ['luu-tru', 'sinh-thai', 'check-in'],
            'team_building' => ['sinh-thai', 'check-in', 'am-thuc'],
            'team building' => ['sinh-thai', 'check-in', 'am-thuc'],
            'backpacking' => ['sinh-thai', 'check-in', 'am-thuc', 'van-hoa-lich-su'],
            'phượt' => ['sinh-thai', 'check-in', 'am-thuc', 'van-hoa-lich-su'],
        ];

        foreach ($map as $key => $slugs) {
            if ($t === $key || str_contains($t, $key)) {
                return $slugs;
            }
        }

        return [];
    }

    /**
     * Lọc địa điểm theo preference + gom cụm GPS (ưu tiên vùng điểm dày đặc gần nhau).
     */
    protected function getFilteredLocations(array $prefs)
    {
        $query = Location::with(['category', 'images'])->where('status', 'published');
        $locations = $query->get();
        if ($locations->isEmpty()) {
            $locations = Location::with(['category', 'images'])->get();
        }

        $prioritySlugs = $this->categorySlugsForTripType($prefs['trip_type'] ?? null);
        $interestSlugs = $this->categorySlugsForInterests($prefs['interests'] ?? []);
        $alwaysInclude = ['am-thuc'];
        if (!empty($prefs['need_hotel'])) {
            $alwaysInclude[] = 'luu-tru';
        }

        $wanted = array_unique(array_merge($prioritySlugs, $interestSlugs, $alwaysInclude));

        if (!empty($wanted)) {
            $filtered = $locations->filter(function ($loc) use ($wanted) {
                $slug = $loc->category->slug ?? '';
                return in_array($slug, $wanted, true);
            });

            if ($filtered->count() < 8) {
                $filtered = $locations;
            }
        } else {
            $filtered = $locations;
        }

        // Gom cụm GPS: bán kính đủ bao phủ các điểm nổi tiếng gần nhau (vd Tam Chúc ~ Bà Đanh ~ 6km)
        $clusterRadiusKm = (($prefs['focus'] ?? '') === 'it_di_chuyen') ? 12.0 : 20.0;
        $clustered = $this->pickNearbyCluster($filtered, $locations, $clusterRadiusKm, $alwaysInclude);

        $sorted = $clustered->sortByDesc(function ($loc) {
            $hasGps = ($loc->lat && $loc->lng) ? 1000 : 0;
            return $hasGps + (float) ($loc->average_rating ?? 0) * 10 + (int) ($loc->view_count ?? 0) / 100;
        })->values();

        if ($sorted->count() > 28) {
            $hotels = $sorted->filter(fn ($l) => ($l->category->slug ?? '') === 'luu-tru')->take(4);
            $food = $sorted->filter(fn ($l) => ($l->category->slug ?? '') === 'am-thuc')->take(8);
            $rest = $sorted->reject(fn ($l) => in_array($l->category->slug ?? '', ['luu-tru', 'am-thuc'], true))->take(16);
            $sorted = $hotels->concat($food)->concat($rest)->unique('id')->values();
        }

        if ($this->wantsVegetarian($prefs)) {
            $sorted = $sorted->reject(function ($loc) {
                $slug = $loc->category->slug ?? '';
                return $slug === 'am-thuc' && $this->isMeatHeavyName((string) $loc->name);
            })->values();
        }

        $preferredIds = $prefs['preferred_location_ids'] ?? [];
        if (!empty($preferredIds)) {
            $preferred = $locations->filter(fn ($l) => in_array((int) $l->id, $preferredIds, true));
            $sorted = $preferred->concat($sorted)->unique('id')->values();
        }

        $this->locationsById = $sorted->keyBy('id')->all();

        return $sorted;
    }

    /**
     * Chọn cụm địa điểm dày nhất (seed = điểm có nhiều neighbor trong bán kính R).
     */
    protected function pickNearbyCluster($priorityLocations, $allLocations, float $radiusKm, array $alwaysIncludeSlugs)
    {
        $withGps = $priorityLocations->filter(fn ($l) => $l->lat && $l->lng)->values();
        if ($withGps->count() < 3) {
            return $priorityLocations;
        }

        // Chỉ dùng điểm tham quan (không tính quán/ks) làm seed để tránh cụm lệch về ẩm thực
        $seeds = $withGps->reject(fn ($l) => in_array($l->category->slug ?? '', ['am-thuc', 'luu-tru'], true))->values();
        if ($seeds->isEmpty()) {
            $seeds = $withGps;
        }

        $bestSeed = null;
        $bestScore = -1;
        $bestMembers = collect();

        foreach ($seeds as $seed) {
            $members = $withGps->filter(function ($loc) use ($seed, $radiusKm) {
                return $this->distanceKm(
                    (float) $seed->lat,
                    (float) $seed->lng,
                    (float) $loc->lat,
                    (float) $loc->lng
                ) <= $radiusKm;
            });

            $score = 0.0;
            foreach ($members as $m) {
                $slug = $m->category->slug ?? '';
                if (in_array($slug, ['am-thuc', 'luu-tru'], true)) {
                    $score += 0.25;
                    continue;
                }
                $score += 1.0;
                $score += min(3.0, (float) ($m->average_rating ?? 0) / 2);
                $score += min(4.0, log10(1 + (int) ($m->view_count ?? 0)));
            }
            $score += min(3.0, log10(1 + (int) ($seed->view_count ?? 0)));

            if ($score > $bestScore) {
                $bestScore = $score;
                $bestSeed = $seed;
                $bestMembers = $members;
            }
        }

        if (!$bestSeed) {
            return $priorityLocations;
        }

        $foodHotelRadius = $radiusKm + 5;
        $extras = $allLocations->filter(function ($loc) use ($bestSeed, $foodHotelRadius, $alwaysIncludeSlugs) {
            $slug = $loc->category->slug ?? '';
            if (!in_array($slug, $alwaysIncludeSlugs, true)) {
                return false;
            }
            if (!$loc->lat || !$loc->lng) {
                return false;
            }
            return $this->distanceKm(
                (float) $bestSeed->lat,
                (float) $bestSeed->lng,
                (float) $loc->lat,
                (float) $loc->lng
            ) <= $foodHotelRadius;
        });

        return $bestMembers->concat($extras)->unique('id')->values();
    }

    protected function buildLocationContext($locations): string
    {
        if ($locations->isEmpty()) {
            return '';
        }

        $context = "\n--- ĐỊA ĐIỂM ĐƯỢC PHÉP (id|tên|loại|gps) ---\n";
        foreach ($locations as $loc) {
            $catName = $loc->category->name ?? 'Địa điểm';
            $lat = $loc->lat ? round((float) $loc->lat, 4) : 'N/A';
            $lng = $loc->lng ? round((float) $loc->lng, 4) : 'N/A';
            $context .= "{$loc->id}|{$loc->name}|{$catName}|{$lat},{$lng}\n";
        }
        $context .= "--------------------------------\n";

        return $context;
    }

    /**
     * Gọi Gemini: trả về nội dung text (đã gỡ code fence) hoặc null nếu thất bại.
     */
    protected function callAI(string $systemPrompt, string $userPrompt, int $maxTokens = 8192, float $temperature = 0.4): ?string
    {
        if (!$this->gemini->isConfigured()) {
            Log::error('TripPlannerService: Chưa cấu hình GEMINI_API_KEY.');
            return null;
        }

        $content = $this->gemini->generate([
            ['role' => 'system', 'content' => $systemPrompt],
            ['role' => 'user', 'content' => $userPrompt],
        ], $temperature, $maxTokens, 50, [
            'json' => true,
            'models' => ['gemini-flash-latest', 'gemini-3.5-flash-lite'],
        ]);

        if ($content) {
            $content = preg_replace('/^```(?:json)?\s*/i', '', trim($content));
            $content = preg_replace('/\s*```$/i', '', $content);
            return $content;
        }

        return null;
    }

    /**
     * Giải mã JSON từ Gemini: gỡ fence rồi decode; cắt phần thừa sau } cuối nếu cần.
     */
    protected function cleanAndDecodeJson(string $rawContent): ?array
    {
        $clean = trim($rawContent);
        $clean = preg_replace('/^```(?:json)?\s*/i', '', $clean);
        $clean = preg_replace('/\s*```$/i', '', $clean);
        $clean = trim($clean);

        $decoded = json_decode($clean, true);
        if (is_array($decoded)) {
            return $decoded;
        }

        if (preg_match('/\{[\s\S]*/', $clean, $matches)) {
            $jsonCandidate = $matches[0];
            $lastBrace = strrpos($jsonCandidate, '}');
            if ($lastBrace !== false) {
                $jsonCandidate = substr($jsonCandidate, 0, $lastBrace + 1);
            }
            $decoded = json_decode($jsonCandidate, true);
            if (is_array($decoded)) {
                return $decoded;
            }

            $fixed = preg_replace('/[\x00-\x1F\x7F]/', ' ', $jsonCandidate);
            $fixed = preg_replace('/,\s*([\]\}])/', '$1', $fixed);
            $decoded = json_decode($fixed, true);
            if (is_array($decoded)) {
                return $decoded;
            }
        }

        return null;
    }

    /**
     * Tính khoảng cách giữa 2 tọa độ GPS theo công thức Haversine (đơn vị: km).
     */
    public function distanceKm(float $lat1, float $lng1, float $lat2, float $lng2): float
    {
        $earth = 6371;
        $dLat = deg2rad($lat2 - $lat1);
        $dLng = deg2rad($lng2 - $lng1);
        $a = sin($dLat / 2) ** 2
            + cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * sin($dLng / 2) ** 2;
        return $earth * 2 * atan2(sqrt($a), sqrt(1 - $a));
    }

    /**
     * Kiểm tra lịch trình Gemini trả về: location_id có trong DB, tên thật, đúng số ngày/ngân sách.
     */
    public function postProcessItinerary(array $itinerary, array $prefs): array
    {
        $catalog = Location::with(['category', 'images'])->where('status', 'published')->get()->keyBy('id');
        if ($catalog->isEmpty()) {
            $catalog = Location::with(['category', 'images'])->get()->keyBy('id');
        }

        $allowedIds = $this->locationsById ? array_map('intval', array_keys($this->locationsById)) : $catalog->keys()->all();
        $allowedSet = array_fill_keys($allowedIds, true);

        $itinerary['estimated_cost'] = $prefs['budget_label'] ?? ($itinerary['estimated_cost'] ?? null);
        $daysWanted = max(1, (int) ($prefs['days'] ?? 2));
        $days = is_array($itinerary['days'] ?? null) ? $itinerary['days'] : [];

        $cleanDays = [];
        foreach ($days as $index => $day) {
            if (!is_array($day)) {
                continue;
            }

            $slots = [];
            $prevLoc = null;
            foreach ($day['slots'] ?? [] as $slot) {
                if (!is_array($slot)) {
                    continue;
                }

                $lid = isset($slot['location_id']) ? (int) $slot['location_id'] : 0;
                $loc = ($lid > 0 && isset($catalog[$lid]) && isset($allowedSet[$lid])) ? $catalog[$lid] : null;

                if (!$loc && !empty($slot['location'])) {
                    $loc = $this->findLocationByName((string) $slot['location'], $catalog, $allowedSet);
                }

                $type = (string) ($slot['type'] ?? 'visit');
                $slot['type'] = in_array($type, ['visit', 'food', 'transport', 'rest', 'photo'], true)
                    ? $type
                    : 'visit';

                $isFood = $slot['type'] === 'food' || ($loc && ($loc->category->slug ?? '') === 'am-thuc');
                if ($loc && $isFood && $this->wantsVegetarian($prefs) && $this->isMeatHeavyName((string) $loc->name)) {
                    $anchor = ($prevLoc && $prevLoc->lat) ? $prevLoc : $loc;
                    $alt = $this->findNearbyFood($anchor, $catalog, $allowedSet, true);
                    if ($alt) {
                        $loc = $alt;
                    } else {
                        $activity = (string) ($slot['activity'] ?? '');
                        $loc = null;
                        $slot['location'] = 'Quán chay / quán thanh đạm gần khu vực đang tham quan';
                        if ($activity !== '' && $this->isMeatHeavyName($activity)) {
                            $slot['activity'] = 'Ăn món chay / thanh đạm tại quán gần khu vực đang tham quan.';
                        }
                    }
                }

                if ($loc) {
                    $slot['location_id'] = (int) $loc->id;
                    $slot['location'] = $loc->name;
                    $slot['place'] = $this->serializePlace($loc);
                    if ($prevLoc && $prevLoc->lat && $prevLoc->lng && $loc->lat && $loc->lng) {
                        $km = $this->distanceKm(
                            (float) $prevLoc->lat,
                            (float) $prevLoc->lng,
                            (float) $loc->lat,
                            (float) $loc->lng
                        );
                        if ($km >= 0.1) {
                            $slot['distance_from_prev_km'] = round($km, 1);
                        }
                    }
                    $prevLoc = $loc;
                } else {
                    unset($slot['location_id'], $slot['place']);
                }

                $slots[] = $slot;
            }

            $day['day'] = (int) ($day['day'] ?? ($index + 1));
            $day['slots'] = $slots;
            $cleanDays[] = $day;
        }

        if (count($cleanDays) > $daysWanted) {
            $cleanDays = array_slice($cleanDays, 0, $daysWanted);
        }

        foreach ($cleanDays as $i => $day) {
            $cleanDays[$i]['day'] = $i + 1;
        }

        $itinerary['days'] = $cleanDays;
        $itinerary['stats'] = $this->buildItineraryStats($cleanDays, $prefs);

        return $itinerary;
    }

    protected function serializePlace(Location $loc): array
    {
        return [
            'id' => (int) $loc->id,
            'name' => $loc->name,
            'slug' => $loc->slug,
            'lat' => $loc->lat !== null ? (float) $loc->lat : null,
            'lng' => $loc->lng !== null ? (float) $loc->lng : null,
            'address' => $loc->address,
            'category' => $loc->category->name ?? null,
            'category_slug' => $loc->category->slug ?? null,
            'image' => $loc->resolveThumbnailUrl(),
            'rating' => $loc->average_rating !== null ? round((float) $loc->average_rating, 1) : null,
            'url' => $loc->slug ? route('client.locations.360', $loc->slug) : null,
        ];
    }

    protected function buildItineraryStats(array $days, array $prefs): array
    {
        $stops = 0;
        $meals = 0;
        $distance = 0.0;
        $seen = [];

        foreach ($days as $day) {
            foreach ($day['slots'] ?? [] as $slot) {
                if (($slot['type'] ?? '') === 'food') {
                    $meals++;
                }
                if (!empty($slot['distance_from_prev_km'])) {
                    $distance += (float) $slot['distance_from_prev_km'];
                }
                $lid = (int) ($slot['location_id'] ?? 0);
                if ($lid > 0 && !isset($seen[$lid])) {
                    $seen[$lid] = true;
                    $stops++;
                }
            }
        }

        return [
            'days' => count($days),
            'stops' => $stops,
            'meals' => $meals,
            'distance_km' => round($distance, 1),
            'budget' => $prefs['budget_label'] ?? null,
        ];
    }

    protected function wantsVegetarian(array $prefs): bool
    {
        $blob = mb_strtolower((string) ($prefs['food'] ?? '') . ' ' . implode(' ', $prefs['interests'] ?? []));

        return str_contains($blob, 'chay')
            || str_contains($blob, 'thanh đạm')
            || str_contains($blob, 'thanh dam')
            || str_contains($blob, 'healthy');
    }

    protected function isMeatHeavyName(string $name): bool
    {
        $n = mb_strtolower($name);
        foreach (['thịt dê', 'thit de', 'lẩu dê', 'thịt lợn', 'thịt heo', 'thịt bò', 'nem chua', 'tiết canh'] as $bad) {
            if (str_contains($n, $bad)) {
                return true;
            }
        }

        return (bool) preg_match('/\bdê\b/u', $n);
    }

    /**
     * @param  \Illuminate\Support\Collection<int, Location>  $catalog
     * @param  array<int, true>  $allowedSet
     */
    protected function findNearbyFood(?Location $near, $catalog, array $allowedSet, bool $vegetarian): ?Location
    {
        $best = null;
        $bestScore = -1;

        foreach ($catalog as $loc) {
            if (!isset($allowedSet[(int) $loc->id])) {
                continue;
            }
            if (($loc->category->slug ?? '') !== 'am-thuc') {
                continue;
            }
            if ($vegetarian && $this->isMeatHeavyName((string) $loc->name)) {
                continue;
            }

            $score = 1.0;
            $name = mb_strtolower((string) $loc->name);
            if ($vegetarian && str_contains($name, 'chay')) {
                $score += 40;
            }
            if ($near && $near->lat && $near->lng && $loc->lat && $loc->lng) {
                $km = $this->distanceKm(
                    (float) $near->lat,
                    (float) $near->lng,
                    (float) $loc->lat,
                    (float) $loc->lng
                );
                $score += max(0, 25 - $km);
            }

            if ($score > $bestScore) {
                $bestScore = $score;
                $best = $loc;
            }
        }

        return $best;
    }

    /**
     * @param  \Illuminate\Support\Collection<int, Location>  $catalog
     * @param  array<int, true>  $allowedSet
     */
    protected function findLocationByName(string $name, $catalog, array $allowedSet): ?Location
    {
        $norm = mb_strtolower(trim($name));
        if ($norm === '') {
            return null;
        }

        foreach ($catalog as $loc) {
            if (!isset($allowedSet[(int) $loc->id])) {
                continue;
            }
            if (mb_strtolower((string) $loc->name) === $norm) {
                return $loc;
            }
        }

        foreach ($catalog as $loc) {
            if (!isset($allowedSet[(int) $loc->id])) {
                continue;
            }
            $locName = mb_strtolower((string) $loc->name);
            if ($locName !== '' && (str_contains($locName, $norm) || str_contains($norm, $locName))) {
                return $loc;
            }
        }

        return null;
    }

    /**
     * Gộp toàn bộ quy trình phân tích -> lọc địa điểm -> gọi AI -> hậu xử lý.
     */
    public function generateItinerary(array $answers, ?string $tripType = null): array
    {
        $prefs = $this->parsePreferences($answers, $tripType);
        $locations = $this->getFilteredLocations($prefs);
        $locationInfo = $this->buildLocationContext($locations);

        $answersText = '';
        foreach ($answers as $a) {
            $q = $a['question'] ?? '';
            $ans = $a['answer'] ?? '';
            if ($q && $ans) {
                $answersText .= "- {$q}: {$ans}\n";
            }
        }

        $days = (int) $prefs['days'];
        $slotMin = (int) ($prefs['slots_per_day'][0] ?? 3);
        $slotMax = (int) ($prefs['slots_per_day'][1] ?? 4);
        $hotelRule = $prefs['need_hotel']
            ? "Cần xếp khách sạn (loại Lưu trú) gần cụm tham quan vào cuối ngày 1" . ($days > 2 ? ' và các đêm còn lại' : '') . '.'
            : 'KHÔNG xếp khách sạn / lưu trú (đi trong ngày).';

        $paceLabel = match ($prefs['pace'] ?? 'can_bang') {
            'cham_rai' => 'Chậm rãi / thư giãn — ít điểm, nhiều thời gian mỗi chỗ',
            'dap_dong' => 'Dồn dập — tối đa điểm tham quan hợp lý',
            default => 'Cân bằng — vừa phải số điểm mỗi ngày',
        };

        $extraRules = [];
        if (!empty($prefs['who'])) {
            $extraRules[] = "Đối tượng đi cùng: {$prefs['who']} — điều chỉnh hoạt động phù hợp (trẻ nhỏ / người lớn tuổi / couple / nhóm).";
        }
        if (!empty($prefs['food'])) {
            $extraRules[] = "Ẩm thực ưu tiên: {$prefs['food']} — chọn quán/ăn uống tương ứng.";
        }
        if (!empty($prefs['focus'])) {
            $extraRules[] = "Ưu tiên số 1 của khách: {$prefs['focus']}.";
        }
        if (!empty($prefs['interests'])) {
            $extraRules[] = 'Sở thích đã chọn: ' . implode(', ', $prefs['interests']) . ' — ưu tiên địa điểm thuộc các nhóm này.';
        }
        $preferredIds = $prefs['preferred_location_ids'] ?? [];
        if (!empty($preferredIds)) {
            $preferredNames = $locations->filter(fn ($l) => in_array((int) $l->id, $preferredIds, true))->pluck('name')->all();
            if ($preferredNames) {
                $extraRules[] = 'Khách ĐÃ CHỌN muốn ghé: ' . implode(', ', $preferredNames) . ' — BẮT BUỘC đưa vào lịch trình nếu hợp lý về khoảng cách.';
            }
        }

        $extraRulesText = '';
        if ($extraRules) {
            $lines = [];
            foreach ($extraRules as $r) {
                $lines[] = '- ' . $r;
            }
            $extraRulesText = "\nCÁ NHÂN HÓA THEO HỒ SƠ:\n" . implode("\n", $lines);
        }

        $systemPrompt = "Bạn là chuyên gia lập kế hoạch du lịch. Trả về đúng 1 object JSON, không markdown.

QUY TẮC:
0. SỐ NGÀY: mảng \"days\" ĐÚNG {$days} phần tử (day: 1..{$days}).
1. NGÂN SÁCH: estimated_cost BẮT BUỘC là \"{$prefs['budget_label']}\".
2. LƯU TRÚ: {$hotelRule}
3. NHỊP ĐỘ: {$paceLabel}. Mỗi ngày khoảng {$slotMin}–{$slotMax} slot chính.
4. GOM CỤM GPS:
   - Chỉ chọn điểm GẦN NHAU (ưu tiên < 8–10 km giữa 2 điểm liên tiếp).
   - Không nhảy xa > 12 km trong cùng ngày (trừ khi về).
   - Sắp xếp tuyến tính, không zig-zag.
   - Chỉ dùng địa điểm trong danh sách (đã lọc theo cụm).
5. Mỗi slot visit/food/photo BẮT BUỘC có location_id đúng từ danh sách.
6. ĂN UỐNG + NGHỈ:
   - Mỗi ngày ít nhất 1 slot type \"food\" ăn trưa 11:30–13:30.
   - Không để trống 12:00–13:30.
   - Ăn + nghỉ = 1 slot food (vd: \"Ăn trưa … rồi nghỉ ngơi nhẹ\"). Không tách rest ngay sau bữa cùng chỗ.
   - type \"rest\" chỉ khi nghỉ giữa chừng không kèm bữa.
   - Nếu còn hoạt động sau 17:00: thêm food ăn tối.
   - Ăn sáng 07:00–09:00 nếu bắt đầu sớm / có lưu trú.
7. Ẩm thực gần điểm đang ở. Nếu quán DB xa (> 5–8 km): gợi ý quán gần khu vực hiện tại, không gắn sai quán.
   - Nếu khách chọn đồ chay / thanh đạm: CẤM gắn quán thịt dê, thịt, đặc sản mặn. Chỉ chọn quán có chữ chay; không có thì ghi \"quán chay gần khu vực\" và không gắn location_id quán thịt.
8. Mô tả ngắn 1–2 câu mỗi slot.{$extraRulesText}
9. Schema JSON:
{
  \"title\": \"Tiêu đề chuyến đi\",
  \"summary\": \"Tóm tắt 1-2 câu\",
  \"estimated_cost\": \"{$prefs['budget_label']}\",
  \"days\": [
    {
      \"day\": 1,
      \"title\": \"Ngày 1: ...\",
      \"slots\": [
        {
          \"time\": \"08:00 - 10:00\",
          \"activity\": \"Mô tả\",
          \"location\": \"Tên từ DB\",
          \"location_id\": 1,
          \"type\": \"visit\",
          \"tip\": \"Mẹo\"
        }
      ]
    }
  ],
  \"tips\": [\"Lưu ý 1\", \"Lưu ý 2\"]
}
type chỉ nhận: visit, food, transport, rest, photo.

{$locationInfo}";

        $userPrompt = "Hồ sơ mong muốn của người dùng:\n{$answersText}\nSố ngày bắt buộc: {$days}.\nNgân sách bắt buộc: {$prefs['budget_label']}.\nNhịp độ: {$paceLabel}.\nHãy sinh lịch trình dạng JSON chi tiết, sát ý khách theo hồ sơ trên.";

        for ($attempt = 1; $attempt <= 2; $attempt++) {
            $tokenBudget = $attempt === 1 ? 6144 : 8192;
            $rawResponse = $this->callAI($systemPrompt, $userPrompt, $tokenBudget, 0.35);

            if (!$rawResponse) {
                Log::warning("TripPlanner: AI trả null (lần $attempt)");
                continue;
            }

            $decoded = $this->cleanAndDecodeJson($rawResponse);
            if (is_array($decoded) && !empty($decoded['days']) && is_array($decoded['days'])) {
                $decoded = $this->postProcessItinerary($decoded, $prefs);
                return [
                    'success' => true,
                    'itinerary' => $decoded,
                    'meta' => [
                        'days' => $prefs['days'],
                        'budget' => $prefs['budget_label'],
                        'locations_used' => $locations->count(),
                    ],
                ];
            }

            Log::warning("TripPlanner: JSON parse thất bại (lần $attempt)", [
                'raw' => mb_substr($rawResponse, 0, 500),
                'len' => mb_strlen($rawResponse),
            ]);
        }

        return ['success' => false, 'error' => 'Không thể tạo lịch trình lúc này. Vui lòng bấm "Lên lịch mới" để thử lại.'];
    }

    /**
     * Lưu lịch trình vào DB cho user.
     */
    public function saveItinerary(int $userId, array $itinerary, array $answers = []): Itinerary
    {
        return DB::transaction(function () use ($userId, $itinerary, $answers) {
            $days = $itinerary['days'] ?? [];
            $record = Itinerary::create([
                'user_id' => $userId,
                'title' => $itinerary['title'] ?? 'Lịch trình du lịch',
                'total_days' => max(1, count($days)),
                'description' => $itinerary['summary'] ?? null,
                'summary' => $itinerary['summary'] ?? null,
                'estimated_cost' => $itinerary['estimated_cost'] ?? null,
                'payload' => $itinerary,
                'answers' => $answers,
                'is_public' => false,
            ]);

            foreach ($days as $day) {
                $dayNum = (int) ($day['day'] ?? 1);
                $dayModel = ItineraryDay::create([
                    'itinerary_id' => $record->id,
                    'day_number' => $dayNum,
                    'notes' => $day['title'] ?? null,
                ]);

                $order = 0;
                foreach ($day['slots'] ?? [] as $slot) {
                    $lid = isset($slot['location_id']) ? (int) $slot['location_id'] : null;
                    if ($lid && !Location::where('id', $lid)->exists()) {
                        $lid = null;
                    }

                    ItineraryItem::create([
                        'day_id' => $dayModel->id,
                        'location_id' => $lid,
                        'order_index' => $order++,
                        'activity' => $slot['activity'] ?? null,
                        'slot_type' => $slot['type'] ?? null,
                        'time_label' => $slot['time'] ?? null,
                        'location_label' => $slot['location'] ?? null,
                        'note' => $slot['location'] ?? null,
                        'tip' => $slot['tip'] ?? null,
                    ]);
                }
            }

            return $record->fresh(['days.items']);
        });
    }
}
