<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use App\Models\Location;
use App\Models\Itinerary;
use App\Models\ItineraryDay;
use App\Models\ItineraryItem;
use Illuminate\Support\Facades\DB;

class TripPlannerService
{
    protected $openRouterModel;
    protected $openRouterBaseUrl;

    /** @var array<int, Location>|null */
    protected $locationsById = null;

    public function __construct()
    {
        $this->openRouterModel = env('OPENROUTER_MODEL', 'google/gemini-2.5-flash-lite');
        $this->openRouterBaseUrl = env('OPENROUTER_BASE_URL', 'https://openrouter.ai/api/v1');
    }

    protected function getApiKeys(): array
    {
        $rawKeys = env('OPENROUTER_API_KEYS') ?: env('OPENROUTER_API_KEY');
        if (!$rawKeys) return [];
        $keys = array_map('trim', explode(',', $rawKeys));
        return array_values(array_filter($keys));
    }

    /**
     * Parse preferences from wizard answers.
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
                    // fallback từ label
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
        }

        if (!$prefs['trip_type'] && $tripType) {
            $prefs['trip_type'] = $tripType;
        }

        return $prefs;
    }

    /**
     * Map interest keys → category slugs.
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
     * Map trip type → category slugs to prioritize.
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
        $query = Location::with('category')->where('status', 'published');
        $locations = $query->get();
            if ($locations->isEmpty()) {
                $locations = Location::with('category')->get();
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

        if ($sorted->count() > 40) {
            $hotels = $sorted->filter(fn ($l) => ($l->category->slug ?? '') === 'luu-tru')->take(6);
            $food = $sorted->filter(fn ($l) => ($l->category->slug ?? '') === 'am-thuc')->take(10);
            $rest = $sorted->reject(fn ($l) => in_array($l->category->slug ?? '', ['luu-tru', 'am-thuc'], true))->take(24);
            $sorted = $hotels->concat($food)->concat($rest)->unique('id')->values();
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

            // Điểm số: số lượng + độ nổi bật (rating / lượt xem) để không bỏ cụm điểm lớn
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
            // Thưởng nhẹ nếu seed là điểm nổi tiếng
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

        // Bổ sung quán ăn / khách sạn gần cụm (bán kính hơi rộng hơn)
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

        $context = "\n--- DANH SÁCH BẮT BUỘC 100% CÁC ĐỊA ĐIỂM ĐƯỢC PHÉP DÙNG (KÈM GPS) ---\n";
            foreach ($locations as $loc) {
                $catName = $loc->category->name ?? 'Địa điểm';
            $lat = $loc->lat ? round((float) $loc->lat, 4) : 'N/A';
            $lng = $loc->lng ? round((float) $loc->lng, 4) : 'N/A';
                $context .= "- ID:{$loc->id} | \"{$loc->name}\" | Loại:{$catName} | GPS:({$lat},{$lng}) | Địa chỉ:" . ($loc->address ?? '') . "\n";
            }
            $context .= "---------------------------------------------------------\n";
            return $context;
    }

    protected function callAI(string $systemPrompt, string $userPrompt, int $maxTokens = 800, float $temperature = 0.7): ?string
    {
        $apiKeys = $this->getApiKeys();
        if (empty($apiKeys)) {
            Log::error('TripPlannerService: No API Key configured.');
            return null;
        }

        $modelsToTry = array_unique([
            $this->openRouterModel,
            'google/gemini-2.5-flash-lite',
            'openrouter/auto'
        ]);

        foreach ($apiKeys as $keyIndex => $apiKey) {
            foreach ($modelsToTry as $modelName) {
                try {
                    $response = Http::withHeaders([
                        'Authorization' => 'Bearer ' . $apiKey,
                        'HTTP-Referer' => 'http://localhost',
                        'X-Title' => 'POI Trip Planner',
                        'Content-Type' => 'application/json',
                    ])
                    ->timeout(45)
                    ->post($this->openRouterBaseUrl . '/chat/completions', [
                        'model' => $modelName,
                        'messages' => [
                            ['role' => 'system', 'content' => $systemPrompt],
                            ['role' => 'user', 'content' => $userPrompt],
                        ],
                        'temperature' => $temperature,
                        'max_tokens' => $maxTokens,
                    ]);

                    if ($response->successful()) {
                        $data = $response->json();
                        $content = $data['choices'][0]['message']['content'] ?? '';
                        if (!empty($content)) {
                            $content = preg_replace('/^```(?:json)?\s*/i', '', trim($content));
                            $content = preg_replace('/\s*```$/i', '', $content);
                            return $content;
                        }
                    }

                    Log::warning("TripPlanner Key #" . ($keyIndex + 1) . " model {$modelName} warning: " . $response->body());
                } catch (\Exception $e) {
                    Log::warning("TripPlanner Key #" . ($keyIndex + 1) . " model {$modelName} exception: " . $e->getMessage());
                }
            }
        }

        return null;
    }

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

            $jsonCandidate = preg_replace('/[\x00-\x1F\x7F]/', ' ', $jsonCandidate);
            $decoded = json_decode($jsonCandidate, true);
            if (is_array($decoded)) {
                return $decoded;
            }

            $sanitized = preg_replace('/,\s*([\]\}])/', '$1', $jsonCandidate);
            $decoded = json_decode($sanitized, true);
            if (is_array($decoded)) {
                return $decoded;
            }
        }

        return null;
    }

    /**
     * Haversine distance in km.
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
     * Validate location_id, enforce days/budget, optimize route by real distance.
     */
    public function postProcessItinerary(array $itinerary, array $prefs): array
    {
        // Validate theo toàn bộ DB (không chỉ tập đã filter vào prompt)
        $allLocations = Location::with('category')->get()->keyBy('id');
        $validIds = $allLocations->keys()->flip()->all();

        // Merge để resolve GPS / tên
        foreach ($allLocations as $id => $loc) {
            if (!isset($this->locationsById[$id])) {
                $this->locationsById[$id] = $loc;
            }
        }

        $days = $itinerary['days'] ?? [];
        if (!is_array($days)) {
            $days = [];
        }

        // Enforce số ngày khớp câu trả lời
        $expectedDays = max(1, (int) ($prefs['days'] ?? 2));
        if (count($days) > $expectedDays) {
            $days = array_slice($days, 0, $expectedDays);
        }
        while (count($days) < $expectedDays) {
            $n = count($days) + 1;
            $days[] = [
                'day' => $n,
                'title' => "Ngày {$n}: Tiếp tục khám phá",
                'slots' => [],
            ];
        }

        $longJumps = [];
        $processedDays = [];

        foreach ($days as $di => $day) {
            $dayNum = $di + 1;
            $slots = is_array($day['slots'] ?? null) ? $day['slots'] : [];
            $cleanSlots = [];

            foreach ($slots as $slot) {
                if (!is_array($slot)) continue;
                $lid = isset($slot['location_id']) ? (int) $slot['location_id'] : null;
                $type = $slot['type'] ?? 'visit';

                if ($lid && !isset($validIds[$lid])) {
                    // Thử match theo tên
                    $matched = $this->findLocationByName($slot['location'] ?? '');
                    if ($matched) {
                        $lid = $matched->id;
                        $slot['location_id'] = $lid;
                        $slot['location'] = $matched->name;
                    } else {
                        $lid = null;
                        unset($slot['location_id']);
                        if (in_array($type, ['visit', 'food', 'photo'], true)) {
                            // Bỏ slot địa điểm không hợp lệ
                            continue;
                        }
                    }
                }

                if ($lid && isset($this->locationsById[$lid])) {
                    $loc = $this->locationsById[$lid];
                    $slot['location_id'] = $lid;
                    $slot['location'] = $loc->name;
                    $slot['_lat'] = $loc->lat ? (float) $loc->lat : null;
                    $slot['_lng'] = $loc->lng ? (float) $loc->lng : null;
                    $slot['_category_slug'] = $loc->category->slug ?? '';
                }

                $cleanSlots[] = $slot;
            }

            $cleanSlots = $this->optimizeDayRoute($cleanSlots);
            $cleanSlots = $this->constrainFarJumps($cleanSlots, $prefs);
            $cleanSlots = $this->ensureMealsAndRest($cleanSlots, $prefs);
            $cleanSlots = $this->fixMealTimes($cleanSlots);
            $cleanSlots = $this->mergeMealWithAdjacentRest($cleanSlots);
            $cleanSlots = $this->softenFarFoodSlots($cleanSlots);
            $cleanSlots = $this->resolveTimelineConflicts($cleanSlots);

            // Đo khoảng cách thật giữa các điểm liên tiếp có GPS
            $prev = null;
            foreach ($cleanSlots as &$slot) {
                if (!empty($slot['_lat']) && !empty($slot['_lng'])) {
                    if ($prev) {
                        $km = $this->distanceKm($prev['_lat'], $prev['_lng'], $slot['_lat'], $slot['_lng']);
                        $slot['distance_from_prev_km'] = round($km, 1);
                        if ($km > 12) {
                            $longJumps[] = sprintf(
                                'Ngày %d: %s → %s ~%.0f km',
                                $dayNum,
                                $prev['location'] ?? '?',
                                $slot['location'] ?? '?',
                                $km
                            );
                        }
                    }
                    $prev = $slot;
                }
                unset($slot['_lat'], $slot['_lng'], $slot['_category_slug']);
            }
            unset($slot);

            $dayTitle = $day['title'] ?? "Ngày {$dayNum}";
            $dayTitle = preg_replace('/^\s*Ngày\s*' . $dayNum . '\s*[:\-–]?\s*/iu', '', (string) $dayTitle);
            $dayTitle = preg_replace('/^\s*Ngày\s*\d+\s*[:\-–]?\s*/iu', '', (string) $dayTitle);
            $dayTitle = trim($dayTitle) !== '' ? trim($dayTitle) : "Hành trình ngày {$dayNum}";

            $processedDays[] = [
                'day' => $dayNum,
                'title' => $dayTitle,
                'slots' => $cleanSlots,
            ];
        }

        $itinerary['days'] = $processedDays;
        $itinerary['estimated_cost'] = $prefs['budget_label'];

        $tips = is_array($itinerary['tips'] ?? null) ? $itinerary['tips'] : [];
        $tips[] = "Lịch trình {$expectedDays} ngày theo lựa chọn của bạn.";
        $tips[] = 'Ngân sách ước tính: ' . $prefs['budget_label'] . '.';
        if (!empty($longJumps)) {
            $tips[] = 'Đã cố gắng gom cụm gần nhau; vẫn còn đoạn hơi xa: ' . implode('; ', array_slice($longJumps, 0, 2)) . '.';
        } else {
            $tips[] = 'Các điểm trong ngày được ưu tiên gom theo khu vực gần nhau để giảm di chuyển.';
        }
        $itinerary['tips'] = array_values(array_unique(array_filter($tips)));

        if (empty($itinerary['title'])) {
            $itinerary['title'] = "Lịch trình {$expectedDays} ngày";
        }

        return $itinerary;
    }

    protected function findLocationByName(string $name): ?Location
    {
        $name = trim($name);
        if ($name === '' || !$this->locationsById) {
            return null;
        }

        $lower = mb_strtolower($name);
        foreach ($this->locationsById as $loc) {
            if (mb_strtolower($loc->name) === $lower) {
                return $loc;
            }
        }
        foreach ($this->locationsById as $loc) {
            if (str_contains(mb_strtolower($loc->name), $lower) || str_contains($lower, mb_strtolower($loc->name))) {
                return $loc;
            }
        }
        return null;
    }

    /**
     * Đảm bảo mỗi ngày có ăn trưa (+ nghỉ / ăn tối khi cần). AI hay bỏ sót.
     */
    protected function ensureMealsAndRest(array $slots, array $prefs): array
    {
        if (empty($slots)) {
            return $slots;
        }

        $hasLunch = false;
        $hasDinner = false;
        $dayStart = null;
        $dayEnd = null;
        $anchorAroundNoon = null;
        $anchorEvening = null;
        $homeTransportStart = null;

        foreach ($slots as $slot) {
            $start = $this->parseTimeStartMinutes($slot['time'] ?? '');
            $end = $this->parseTimeEndMinutes($slot['time'] ?? '') ?? $start;
            if ($start !== null) {
                $dayStart = $dayStart === null ? $start : min($dayStart, $start);
            }
            if ($end !== null) {
                $dayEnd = $dayEnd === null ? $end : max($dayEnd, $end);
            }

            $activity = mb_strtolower((string) ($slot['activity'] ?? ''));
            $type = $slot['type'] ?? '';

            $isFood = $type === 'food'
                || str_contains($activity, 'ăn sáng')
                || str_contains($activity, 'ăn trưa')
                || str_contains($activity, 'ăn tối')
                || str_contains($activity, 'bữa ');

            if ($isFood) {
                if ($start !== null && $start >= 11 * 60 && $start < 15 * 60) {
                    $hasLunch = true;
                }
                if ($start !== null && $start >= 16 * 60) {
                    $hasDinner = true;
                }
                if (str_contains($activity, 'ăn trưa') || str_contains($activity, 'bữa trưa')) {
                    $hasLunch = true;
                }
                if (str_contains($activity, 'ăn tối') || str_contains($activity, 'bữa tối')) {
                    $hasDinner = true;
                }
            }

            if (
                $type === 'transport'
                && $start !== null
                && (str_contains($activity, 'về') || str_contains($activity, 'xuất phát') || str_contains($activity, 'trở về'))
            ) {
                $homeTransportStart = $homeTransportStart === null ? $start : min($homeTransportStart, $start);
            }

            if ($start !== null && $start <= 12 * 60 + 30) {
                $anchorAroundNoon = $slot;
            }
            if ($start !== null && ($type !== 'transport' || $start < 17 * 60)) {
                $anchorEvening = $slot;
            }
        }

        if (!$anchorAroundNoon) {
            $anchorAroundNoon = $slots[0];
        }
        if (!$anchorEvening) {
            $anchorEvening = $slots[count($slots) - 1];
        }

        $foodStyle = $prefs['food'] ?? '';
        $foodNear = 'các quán ăn gần';
        if (str_contains((string) $foodStyle, 'chay')) {
            $foodNear = 'quán chay / món thanh đạm gần';
        } elseif (str_contains((string) $foodStyle, 'dac_san') || str_contains((string) $foodStyle, 'đặc sản')) {
            $foodNear = 'quán đặc sản gần';
        } elseif (str_contains((string) $foodStyle, 'nha_hang') || str_contains((string) $foodStyle, 'nhà hàng')) {
            $foodNear = 'nhà hàng / quán view gần';
        } elseif (str_contains((string) $foodStyle, 'binh_dan') || str_contains((string) $foodStyle, 'bình dân')) {
            $foodNear = 'quán ăn bình dân gần';
        }

        $coversLunchWindow = ($dayStart === null || $dayStart < 13 * 60)
            && ($dayEnd === null || $dayEnd > 11 * 60 + 30);

        if ($coversLunchWindow && !$hasLunch) {
            $area = $this->cleanAreaLabel($anchorAroundNoon['location'] ?? 'điểm tham quan');
            $gap = $this->findFreeGap($slots, 12 * 60, 60, 11 * 60 + 30, 13 * 60 + 30);
            if ($gap) {
                // Ăn + nghỉ gộp 1 slot (~75–90 phút), không tách rest riêng
                $end = min($gap[1] + 20, 13 * 60 + 45);
                $slots[] = [
                    'time' => $this->formatTimeRange($gap[0], max($end, $gap[0] + 75)),
                    'activity' => "Ăn trưa tại {$foodNear} {$area}, rồi nghỉ ngơi nhẹ trước khi đi tiếp.",
                    'location' => $anchorAroundNoon['location'] ?? $area,
                    'location_id' => $anchorAroundNoon['location_id'] ?? null,
                    'type' => 'food',
                    'tip' => 'Gộp ăn và nghỉ tại chỗ để lịch gọn, đỡ mất thời gian di chuyển.',
                    '_lat' => $anchorAroundNoon['_lat'] ?? null,
                    '_lng' => $anchorAroundNoon['_lng'] ?? null,
                ];
            }
        }

        $needsDinner = ($dayEnd !== null && $dayEnd >= 17 * 60)
            || ($homeTransportStart !== null)
            || (!empty($prefs['need_hotel']) && ($prefs['days'] ?? 1) >= 2);

        if ($needsDinner && !$hasDinner) {
            $area = $this->cleanAreaLabel($anchorEvening['location'] ?? 'điểm cuối ngày');
            $dinnerEndLimit = $homeTransportStart ?? (19 * 60);
            $preferredStart = max(16 * 60 + 30, $dinnerEndLimit - 60);
            $gap = $this->findFreeGap($slots, $preferredStart, 60, 16 * 60, $dinnerEndLimit);
            if (!$gap && $homeTransportStart !== null) {
                $end = $homeTransportStart;
                $start = max(16 * 60, $end - 60);
                if ($end - $start >= 30) {
                    $gap = [$start, $end];
                }
            }
            if ($gap) {
                $slots[] = [
                    'time' => $this->formatTimeRange($gap[0], $gap[1]),
                    'activity' => "Ăn tối tại {$foodNear} {$area} trước khi kết thúc ngày.",
                    'location' => $anchorEvening['location'] ?? $area,
                    'location_id' => $anchorEvening['location_id'] ?? null,
                    'type' => 'food',
                    'tip' => 'Ăn tối trước khi lên đường về để không chồng giờ với di chuyển.',
                    '_lat' => $anchorEvening['_lat'] ?? null,
                    '_lng' => $anchorEvening['_lng'] ?? null,
                ];
            }
        }

        usort($slots, function ($a, $b) {
            $am = $this->parseTimeStartMinutes($a['time'] ?? '') ?? 0;
            $bm = $this->parseTimeStartMinutes($b['time'] ?? '') ?? 0;
            return $am <=> $bm;
        });

        return $slots;
    }

    /**
     * Gộp slot ăn + nghỉ liền kề (cùng chỗ / nghỉ ngay sau bữa) thành 1 slot food.
     */
    protected function mergeMealWithAdjacentRest(array $slots): array
    {
        if (count($slots) < 2) {
            return $slots;
        }

        usort($slots, function ($a, $b) {
            $am = $this->parseTimeStartMinutes($a['time'] ?? '') ?? 0;
            $bm = $this->parseTimeStartMinutes($b['time'] ?? '') ?? 0;
            return $am <=> $bm;
        });

        $merged = [];
        $n = count($slots);

        for ($i = 0; $i < $n; $i++) {
            $curr = $slots[$i];
            $next = $slots[$i + 1] ?? null;

            if ($next && $this->isMealSlot($curr) && $this->isPostMealRestSlot($next) && $this->samePlaceOrRest($curr, $next)) {
                $start = $this->parseTimeStartMinutes($curr['time'] ?? '') ?? 0;
                $endFood = $this->parseTimeEndMinutes($curr['time'] ?? '') ?? ($start + 60);
                $endRest = $this->parseTimeEndMinutes($next['time'] ?? '')
                    ?? ($this->parseTimeStartMinutes($next['time'] ?? '') ?? $endFood) + 20;
                $end = max($endFood, $endRest);

                $activity = trim((string) ($curr['activity'] ?? ''));
                $activityLower = mb_strtolower($activity);
                if (!str_contains($activityLower, 'nghỉ')) {
                    $activity = rtrim($activity, ". \t") . ', rồi nghỉ ngơi nhẹ trước khi đi tiếp.';
                }

                $tip = trim((string) ($curr['tip'] ?? ''));
                $restTip = trim((string) ($next['tip'] ?? ''));
                if ($restTip !== '' && $tip !== '' && !str_contains(mb_strtolower($tip), mb_strtolower(mb_substr($restTip, 0, 20)))) {
                    $tip = $tip . ' ' . $restTip;
                } elseif ($tip === '') {
                    $tip = $restTip;
                }

                $curr['time'] = $this->formatTimeRange($start, $end);
                $curr['activity'] = $activity;
                $curr['type'] = 'food';
                $curr['tip'] = $tip !== '' ? $tip : ($curr['tip'] ?? null);
                $merged[] = $curr;
                $i++; // skip rest
                continue;
            }

            $merged[] = $curr;
        }

        return $merged;
    }

    protected function isMealSlot(array $slot): bool
    {
        $type = $slot['type'] ?? '';
        $activity = mb_strtolower((string) ($slot['activity'] ?? ''));
        if ($type === 'food') {
            return true;
        }
        return str_contains($activity, 'ăn sáng')
            || str_contains($activity, 'ăn trưa')
            || str_contains($activity, 'ăn tối')
            || str_contains($activity, 'bữa ');
    }

    protected function isPostMealRestSlot(array $slot): bool
    {
        $type = $slot['type'] ?? '';
        $activity = mb_strtolower((string) ($slot['activity'] ?? ''));
        if ($type === 'transport' || $type === 'visit' || $type === 'photo' || $type === 'food') {
            return false;
        }
        // Nghỉ sau bữa / thư giãn — không phải nghỉ chuẩn bị lên xe về (gộp với dinner tùy case)
        if ($type === 'rest' || str_contains($activity, 'nghỉ')) {
            return true;
        }
        return str_contains($activity, 'thư giãn') || str_contains($activity, 'tĩnh tâm');
    }

    protected function samePlaceOrRest(array $meal, array $rest): bool
    {
        $a = mb_strtolower(trim((string) ($meal['location'] ?? '')));
        $b = mb_strtolower(trim((string) ($rest['location'] ?? '')));
        if ($a === '' || $b === '') {
            return true;
        }
        if ($a === $b) {
            return true;
        }
        return str_contains($a, $b) || str_contains($b, $a);
    }

    protected function cleanAreaLabel(string $area): string
    {
        $area = trim($area);
        $area = preg_replace('/^(khu\s*vực\s*)+/iu', '', $area) ?: $area;
        return trim($area) !== '' ? trim($area) : 'điểm tham quan';
    }

    protected function formatTimeRange(int $startMin, int $endMin): string
    {
        $startMin = max(0, min(23 * 60 + 59, $startMin));
        $endMin = max($startMin + 15, min(23 * 60 + 59, $endMin));
        return sprintf(
            '%02d:%02d - %02d:%02d',
            intdiv($startMin, 60),
            $startMin % 60,
            intdiv($endMin, 60),
            $endMin % 60
        );
    }

    /**
     * @return array{0:int,1:int}|null
     */
    protected function findFreeGap(array $slots, int $preferredStart, int $duration, int $windowStart, int $windowEnd): ?array
    {
        if ($windowEnd - $windowStart < $duration) {
            return null;
        }

        $busy = [];
        foreach ($slots as $slot) {
            $s = $this->parseTimeStartMinutes($slot['time'] ?? '');
            $e = $this->parseTimeEndMinutes($slot['time'] ?? '');
            if ($s === null) {
                continue;
            }
            if ($e === null || $e <= $s) {
                $e = $s + 30;
            }
            $busy[] = [$s, $e];
        }
        usort($busy, fn ($a, $b) => $a[0] <=> $b[0]);

        $tryStarts = [$preferredStart];
        for ($t = $windowStart; $t <= $windowEnd - $duration; $t += 15) {
            if ($t !== $preferredStart) {
                $tryStarts[] = $t;
            }
        }

        foreach ($tryStarts as $start) {
            if ($start < $windowStart || $start + $duration > $windowEnd) {
                continue;
            }
            $end = $start + $duration;
            $overlap = false;
            foreach ($busy as [$bs, $be]) {
                if ($start < $be && $end > $bs) {
                    $overlap = true;
                    break;
                }
            }
            if (!$overlap) {
                return [$start, $end];
            }
        }

        return null;
    }

    /**
     * Sắp xếp lại giờ để không chồng chéo; ăn tối luôn trước giờ về.
     */
    protected function resolveTimelineConflicts(array $slots): array
    {
        if (count($slots) < 2) {
            return $slots;
        }

        $dinnerIdx = null;
        $homeIdx = null;
        foreach ($slots as $i => $slot) {
            $activity = mb_strtolower((string) ($slot['activity'] ?? ''));
            $type = $slot['type'] ?? '';
            if ($type === 'food' && (str_contains($activity, 'ăn tối') || str_contains($activity, 'bữa tối'))) {
                $dinnerIdx = $i;
            }
            if (
                $type === 'transport'
                && (str_contains($activity, 'về') || str_contains($activity, 'trở về') || str_contains($activity, 'xuất phát'))
            ) {
                $homeIdx = $i;
            }
        }

        if ($dinnerIdx !== null && $homeIdx !== null) {
            $dinnerStart = $this->parseTimeStartMinutes($slots[$dinnerIdx]['time'] ?? '');
            $homeStart = $this->parseTimeStartMinutes($slots[$homeIdx]['time'] ?? '');
            if ($dinnerStart !== null && $homeStart !== null && $dinnerStart >= $homeStart) {
                $dur = 60;
                $dEnd = $this->parseTimeEndMinutes($slots[$dinnerIdx]['time'] ?? '');
                if ($dEnd !== null) {
                    $dur = max(30, $dEnd - $dinnerStart);
                }
                $newEnd = $homeStart;
                $newStart = max(16 * 60, $newEnd - $dur);
                $slots[$dinnerIdx]['time'] = $this->formatTimeRange($newStart, $newEnd);
            }
        }

        usort($slots, function ($a, $b) {
            $am = $this->parseTimeStartMinutes($a['time'] ?? '') ?? 0;
            $bm = $this->parseTimeStartMinutes($b['time'] ?? '') ?? 0;
            return $am <=> $bm;
        });

        $prevEnd = null;
        foreach ($slots as &$slot) {
            $start = $this->parseTimeStartMinutes($slot['time'] ?? '');
            $end = $this->parseTimeEndMinutes($slot['time'] ?? '');
            if ($start === null) {
                continue;
            }
            if ($end === null || $end <= $start) {
                $end = $start + 30;
            }
            $duration = max(15, $end - $start);
            $type = $slot['type'] ?? '';

            if ($prevEnd !== null && $start < $prevEnd) {
                // Rest ngắn bị chồng: cắt/ghép sát sau slot trước thay vì đẩy cả chuỗi về
                if ($type === 'rest' && $duration <= 30) {
                    $start = $prevEnd;
                    $end = min($start + $duration, $start + 20);
                    if ($end <= $start) {
                        $end = $start + 15;
                    }
                } else {
                    $start = $prevEnd;
                    $end = $start + $duration;
                }
                if ($end > 21 * 60 + 30) {
                    $end = 21 * 60 + 30;
                    $start = max($prevEnd, $end - min($duration, 60));
                }
                $slot['time'] = $this->formatTimeRange($start, $end);
            }

            $prevEnd = $this->parseTimeEndMinutes($slot['time'] ?? '') ?? ($start + $duration);
        }
        unset($slot);

        return $slots;
    }

    protected function parseTimeEndMinutes(?string $time): ?int
    {
        if (!$time) {
            return null;
        }
        if (preg_match_all('/(\d{1,2})\s*:\s*(\d{2})/', $time, $m, PREG_SET_ORDER) && count($m) >= 2) {
            $h = (int) $m[1][1];
            $min = (int) $m[1][2];
            if ($h >= 0 && $h <= 23 && $min >= 0 && $min <= 59) {
                return $h * 60 + $min;
            }
        }
        return null;
    }

    /**
     * Sửa khung giờ bữa ăn lệch (vd: \"Ăn trưa\" lúc 16:30).
     */
    protected function fixMealTimes(array $slots): array
    {
        foreach ($slots as &$slot) {
            $activity = mb_strtolower((string) ($slot['activity'] ?? ''));
            $type = $slot['type'] ?? '';
            $isFood = $type === 'food'
                || str_contains($activity, 'ăn sáng')
                || str_contains($activity, 'ăn trưa')
                || str_contains($activity, 'ăn tối')
                || str_contains($activity, 'bữa sáng')
                || str_contains($activity, 'bữa trưa')
                || str_contains($activity, 'bữa tối');

            if (!$isFood) {
                continue;
            }

            $meal = null;
            if (str_contains($activity, 'ăn sáng') || str_contains($activity, 'bữa sáng') || str_contains($activity, 'breakfast')) {
                $meal = 'breakfast';
            } elseif (str_contains($activity, 'ăn tối') || str_contains($activity, 'bữa tối') || str_contains($activity, 'dinner')) {
                $meal = 'dinner';
            } elseif (str_contains($activity, 'ăn trưa') || str_contains($activity, 'bữa trưa') || str_contains($activity, 'lunch')) {
                $meal = 'lunch';
            } elseif ($type === 'food') {
                // Food slot không ghi rõ: suy từ giờ hiện tại
                $startMin = $this->parseTimeStartMinutes($slot['time'] ?? '');
                if ($startMin !== null) {
                    if ($startMin < 10 * 60) {
                        $meal = 'breakfast';
                    } elseif ($startMin < 15 * 60) {
                        $meal = 'lunch';
                    } else {
                        $meal = 'dinner';
                    }
                } else {
                    $meal = 'lunch';
                }
            }

            // Dinner: cho phép sớm từ 16:00 (ăn trước khi về), không ép cứng 18:00
            $ranges = [
                'breakfast' => ['07:30 - 08:30', 7 * 60 + 30, 9 * 60],
                'lunch' => ['11:45 - 13:00', 11 * 60 + 30, 13 * 60 + 30],
                'dinner' => ['17:00 - 18:00', 16 * 60, 20 * 60],
            ];

            if (!$meal || !isset($ranges[$meal])) {
                continue;
            }

            [$defaultRange, $minOk, $maxOk] = $ranges[$meal];
            $startMin = $this->parseTimeStartMinutes($slot['time'] ?? '');

            // Chỉ sửa khi giờ lệch khung — không đụng dinner đã đặt trước giờ về
            if ($startMin === null || $startMin < $minOk || $startMin > $maxOk) {
                $slot['time'] = $defaultRange;
            }

            if ($meal === 'lunch' && $startMin !== null && $startMin >= 15 * 60) {
                $slot['time'] = $defaultRange;
            }

            // Chuẩn hóa wording nếu giờ đã là dinner nhưng text vẫn \"ăn trưa\"
            if ($meal === 'dinner' && (str_contains($activity, 'ăn trưa') || str_contains($activity, 'bữa trưa'))) {
                $slot['activity'] = preg_replace('/ăn\s*trưa|bữa\s*trưa/iu', 'Ăn tối', (string) $slot['activity']);
            }
            if ($meal === 'lunch' && (str_contains($activity, 'ăn tối') || str_contains($activity, 'bữa tối'))) {
                $slot['activity'] = preg_replace('/ăn\s*tối|bữa\s*tối/iu', 'Ăn trưa', (string) $slot['activity']);
            }
        }
        unset($slot);

        // Sắp lại theo giờ bắt đầu để timeline không đảo
        usort($slots, function ($a, $b) {
            $am = $this->parseTimeStartMinutes($a['time'] ?? '') ?? 0;
            $bm = $this->parseTimeStartMinutes($b['time'] ?? '') ?? 0;
            return $am <=> $bm;
        });

        return $slots;
    }

    protected function parseTimeStartMinutes(?string $time): ?int
    {
        if (!$time) {
            return null;
        }
        if (preg_match('/(\d{1,2})\s*:\s*(\d{2})/', $time, $m)) {
            $h = (int) $m[1];
            $min = (int) $m[2];
            if ($h >= 0 && $h <= 23 && $min >= 0 && $min <= 59) {
                return $h * 60 + $min;
            }
        }
        return null;
    }

    /**
     * Nếu slot food quá xa điểm trước: viết lại activity kiểu \"gần khu vực X hoặc tham khảo quán Y\".
     */
    protected function softenFarFoodSlots(array $slots): array
    {
        $farThresholdKm = 6.0;
        $prevVisit = null;

        foreach ($slots as &$slot) {
            $type = $slot['type'] ?? 'visit';
            $hasGps = !empty($slot['_lat']) && !empty($slot['_lng']);

            if (in_array($type, ['visit', 'photo'], true) && $hasGps) {
                $prevVisit = $slot;
            }

            $isFood = $type === 'food'
                || str_contains(mb_strtolower((string) ($slot['activity'] ?? '')), 'ăn ');

            if (!$isFood || !$hasGps || !$prevVisit) {
                continue;
            }

            $km = $this->distanceKm(
                (float) $prevVisit['_lat'],
                (float) $prevVisit['_lng'],
                (float) $slot['_lat'],
                (float) $slot['_lng']
            );

            if ($km < $farThresholdKm) {
                continue;
            }

            $areaName = $prevVisit['location'] ?? 'điểm tham quan';
            $foodName = $slot['location'] ?? 'quán đặc sản';
            $activityLower = mb_strtolower((string) ($slot['activity'] ?? ''));

            $mealWord = 'Ăn';
            if (str_contains($activityLower, 'ăn sáng') || str_contains($activityLower, 'bữa sáng')) {
                $mealWord = 'Ăn sáng';
            } elseif (str_contains($activityLower, 'ăn tối') || str_contains($activityLower, 'bữa tối')) {
                $mealWord = 'Ăn tối';
            } elseif (str_contains($activityLower, 'ăn trưa') || str_contains($activityLower, 'bữa trưa')) {
                $mealWord = 'Ăn trưa';
            } else {
                $startMin = $this->parseTimeStartMinutes($slot['time'] ?? '');
                if ($startMin !== null && $startMin < 10 * 60) {
                    $mealWord = 'Ăn sáng';
                } elseif ($startMin !== null && $startMin >= 16 * 60) {
                    $mealWord = 'Ăn tối';
                } else {
                    $mealWord = 'Ăn trưa';
                }
            }

            $slot['activity'] = "{$mealWord} tại các quán ăn gần khu vực {$areaName} hoặc có thể tham khảo quán {$foodName} nếu tiện đường.";
            $slot['tip'] = trim(($slot['tip'] ?? '') . " Quán {$foodName} cách khoảng " . round($km, 1) . " km — chỉ nên ghé nếu cùng hướng di chuyển.");
            $slot['distance_note'] = 'far_food';

            // Neo location về khu vực đang ở để người dùng không bị zoom sang quán xa
            $slot['location'] = $areaName;
            if (!empty($prevVisit['location_id'])) {
                $slot['location_id'] = $prevVisit['location_id'];
                $slot['_lat'] = $prevVisit['_lat'];
                $slot['_lng'] = $prevVisit['_lng'];
            }
            $slot['reference_food'] = $foodName;
        }
        unset($slot);

        return $slots;
    }

    /**
     * Greedy nearest-neighbor reorder cho các slot có GPS (giữ slot không GPS tại chỗ tương đối).
     */
    protected function optimizeDayRoute(array $slots): array
    {
        $withGps = [];
        $without = [];
        foreach ($slots as $i => $slot) {
            if (!empty($slot['_lat']) && !empty($slot['_lng']) && in_array($slot['type'] ?? 'visit', ['visit', 'food', 'photo'], true)) {
                $withGps[] = $slot;
            } else {
                $without[] = ['index' => $i, 'slot' => $slot];
            }
        }

        if (count($withGps) < 2) {
            return $slots;
        }

        $ordered = [];
        $remaining = $withGps;
        $current = array_shift($remaining);
        $ordered[] = $current;

        while (!empty($remaining)) {
            $bestIdx = 0;
            $bestDist = PHP_FLOAT_MAX;
            foreach ($remaining as $idx => $cand) {
                $d = $this->distanceKm($current['_lat'], $current['_lng'], $cand['_lat'], $cand['_lng']);
                if ($d < $bestDist) {
                    $bestDist = $d;
                    $bestIdx = $idx;
                }
            }
            $current = $remaining[$bestIdx];
            $ordered[] = $current;
            array_splice($remaining, $bestIdx, 1);
        }

        // Ghép lại: thay các slot có GPS bằng thứ tự tối ưu, giữ transport/rest theo vị trí gốc
        $result = [];
        $oi = 0;
        $gpsPositions = [];
        foreach ($slots as $i => $slot) {
            if (!empty($slot['_lat']) && !empty($slot['_lng']) && in_array($slot['type'] ?? 'visit', ['visit', 'food', 'photo'], true)) {
                $gpsPositions[] = $i;
            }
        }

        $result = $slots;
        foreach ($gpsPositions as $pos) {
            if (isset($ordered[$oi])) {
                // Giữ time gốc nếu có
                $newSlot = $ordered[$oi];
                if (!empty($slots[$pos]['time'])) {
                    $newSlot['time'] = $slots[$pos]['time'];
                }
                $result[$pos] = $newSlot;
                $oi++;
            }
        }

        return array_values($result);
    }

    /**
     * Thay điểm quá xa điểm trước bằng địa điểm gần hơn trong cùng cụm DB.
     */
    protected function constrainFarJumps(array $slots, array $prefs): array
    {
        $maxKm = (($prefs['focus'] ?? '') === 'it_di_chuyen') ? 8.0 : 12.0;
        $usedIds = [];
        foreach ($slots as $s) {
            if (!empty($s['location_id'])) {
                $usedIds[(int) $s['location_id']] = true;
            }
        }

        $prevGps = null;
        foreach ($slots as &$slot) {
            $type = $slot['type'] ?? 'visit';
            $hasGps = !empty($slot['_lat']) && !empty($slot['_lng']);

            // food/rest đã xử lý riêng; chỉ siết visit/photo/transport điểm
            if (!in_array($type, ['visit', 'photo'], true) || !$hasGps) {
                if ($hasGps && in_array($type, ['visit', 'photo', 'food'], true)) {
                    $prevGps = $slot;
                }
                continue;
            }

            if ($prevGps) {
                $km = $this->distanceKm(
                    (float) $prevGps['_lat'],
                    (float) $prevGps['_lng'],
                    (float) $slot['_lat'],
                    (float) $slot['_lng']
                );

                if ($km > $maxKm) {
                    $replacement = $this->findNearestAlternative(
                        (float) $prevGps['_lat'],
                        (float) $prevGps['_lng'],
                        $slot['_category_slug'] ?? null,
                        $usedIds,
                        $maxKm
                    );

                    if ($replacement) {
                        $oldName = $slot['location'] ?? '';
                        $oldId = isset($slot['location_id']) ? (int) $slot['location_id'] : null;
                        if ($oldId) {
                            unset($usedIds[$oldId]);
                        }

                        $slot['location_id'] = $replacement->id;
                        $slot['location'] = $replacement->name;
                        $slot['_lat'] = (float) $replacement->lat;
                        $slot['_lng'] = (float) $replacement->lng;
                        $slot['_category_slug'] = $replacement->category->slug ?? ($slot['_category_slug'] ?? '');
                        $usedIds[$replacement->id] = true;

                        $newKm = $this->distanceKm(
                            (float) $prevGps['_lat'],
                            (float) $prevGps['_lng'],
                            (float) $slot['_lat'],
                            (float) $slot['_lng']
                        );
                        $slot['tip'] = trim(($slot['tip'] ?? '') . " Đã ưu tiên điểm gần hơn (~" . round($newKm, 1) . " km) thay vì đi xa tới {$oldName}.");
                        if (!empty($slot['activity'])) {
                            // Giữ mô tả chung, đổi tên địa điểm nếu có trong activity
                            $slot['activity'] = str_replace($oldName, $replacement->name, $slot['activity']);
                        }
                    } else {
                        // Không tìm được điểm thay: gắn lại về khu vực điểm trước để tránh nhảy xa
                        $slot['type'] = 'rest';
                        $slot['activity'] = 'Nghỉ ngắn / dạo quanh khu vực ' . ($prevGps['location'] ?? 'điểm trước') . ' thay vì di chuyển quá xa.';
                        $slot['location'] = $prevGps['location'] ?? $slot['location'];
                        $slot['location_id'] = $prevGps['location_id'] ?? null;
                        $slot['_lat'] = $prevGps['_lat'];
                        $slot['_lng'] = $prevGps['_lng'];
                        $slot['tip'] = 'Bỏ đoạn di chuyển > ' . round($km, 1) . ' km trong cùng buổi để lịch trình gọn hơn.';
                    }
                }
            }

            if (!empty($slot['_lat']) && !empty($slot['_lng'])) {
                $prevGps = $slot;
            }
        }
        unset($slot);

        return $slots;
    }

    /**
     * Tìm địa điểm gần nhất trong bán kính, ưu tiên cùng category, chưa dùng trong ngày.
     */
    protected function findNearestAlternative(float $lat, float $lng, ?string $preferredSlug, array $usedIds, float $maxKm): ?Location
    {
        if (empty($this->locationsById)) {
            return null;
        }

        $best = null;
        $bestDist = PHP_FLOAT_MAX;

        foreach ($this->locationsById as $loc) {
            if (!$loc->lat || !$loc->lng) {
                continue;
            }
            if (isset($usedIds[$loc->id])) {
                continue;
            }
            $slug = $loc->category->slug ?? '';
            if (in_array($slug, ['am-thuc', 'luu-tru'], true)) {
                continue; // thay điểm tham quan, không thay bằng quán
            }

            $d = $this->distanceKm($lat, $lng, (float) $loc->lat, (float) $loc->lng);
            if ($d > $maxKm) {
                continue;
            }

            // Ưu tiên cùng category: giảm khoảng cách ảo
            $score = $d;
            if ($preferredSlug && $slug === $preferredSlug) {
                $score -= 2.0;
            }

            if ($score < $bestDist) {
                $bestDist = $score;
                $best = $loc;
            }
        }

        return $best;
    }

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
        if (($prefs['focus'] ?? '') === 'it_di_chuyen' || ($prefs['focus'] ?? '') === 'anh_dep') {
            // soft hint already in focus
        }
        $extraRulesText = '';
        if ($extraRules) {
            $lines = [];
            foreach ($extraRules as $i => $r) {
                $lines[] = '- ' . $r;
            }
            $extraRulesText = "\nCÁ NHÂN HÓA THEO HỒ SƠ:\n" . implode("\n", $lines);
        }

        $systemPrompt = "Bạn là chuyên gia lập kế hoạch du lịch chuyên nghiệp.
Nhiệm vụ của bạn là lập LỊCH TRÌNH DU LỊCH CHI TIẾT theo dạng Timeline dựa trên hồ sơ mong muốn của người dùng.

QUY TẮC BẮT BUỘC (TUYỆT ĐỐI TUÂN THỦ 100%):

0. SỐ NGÀY: mảng \"days\" BẮT BUỘC có ĐÚNG {$days} phần tử (day: 1..{$days}).
1. NGÂN SÁCH: estimated_cost BẮT BUỘC là \"{$prefs['budget_label']}\" (khớp lựa chọn người dùng).
2. LƯU TRÚ: {$hotelRule}
3. NHỊP ĐỘ: {$paceLabel}. Mỗi ngày khoảng {$slotMin}–{$slotMax} slot chính (không được sơ sài bỏ bữa/nghỉ).
4. GOM CỤM GPS — ƯU TIÊN TUYỆT ĐỐI:
   - Chỉ chọn các điểm GẦN NHAU trong cùng vùng (ưu tiên < 8–10 km giữa 2 điểm liên tiếp).
   - KHÔNG nhảy xa > 12 km trong cùng một ngày/buổi (trừ khi bắt buộc vận chuyển về).
   - Sắp xếp tuyến tính xuôi đường, tuyệt đối tránh zig-zag.
   - Danh sách bên dưới đã được lọc theo cụm gần nhau — hãy dùng điểm trong cụm đó.
5. CHỈ chọn địa điểm từ danh sách bên dưới. Mỗi slot visit/food/photo BẮT BUỘC có location_id đúng.
6. ĂN UỐNG + NGHỈ — BẮT BUỘC ĐỦ (quan trọng):
   - Mỗi ngày PHẢI có ít nhất 1 slot type \"food\" ĂN TRƯA trong khung 11:30–13:30.
   - KHÔNG được để trống khoảng 12:00–13:30 (ví dụ kết thúc 12:00 rồi nhảy 13:30 mà không có ăn trưa).
   - ĂN + NGHỈ = 1 SLOT: gộp nghỉ sau bữa vào cùng slot \"food\" (vd: \"Ăn trưa … rồi nghỉ ngơi nhẹ\"). KHÔNG tách slot \"rest\" ngay sau ăn trưa/ăn tối cùng địa điểm.
   - Chỉ dùng type \"rest\" khi nghỉ giữa chừng KHÔNG kèm bữa ăn (vd: nghỉ chân giữa 2 cụm tham quan, hoặc chuẩn bị lên xe về).
   - Nếu lịch còn hoạt động sau 17:00: thêm slot type \"food\" ĂN TỐI trước giờ về (có thể gộp nghỉ nhẹ trong cùng slot).
   - Ăn sáng 07:00–09:00 nếu bắt đầu sớm / có lưu trú.
7. ẨM THỰC GẦN ĐIỂM ĐANG Ở:
   - Ưu tiên quán ăn GẦN cụm/điểm tham quan vừa xếp.
   - Nếu quán trong DB ở XA (> ~5–8 km): viết \"Ăn trưa tại các quán gần khu vực [điểm đang ở] hoặc có thể tham khảo quán [tên quán] nếu tiện đường.\"
8. Mô tả ngắn 1–2 câu mỗi slot.{$extraRulesText}
9. Trả về ĐÚNG 1 ĐỐI TƯỢNG JSON thuần túy:
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

        $rawResponse = $this->callAI($systemPrompt, $userPrompt, 3500, 0.55);

        if ($rawResponse) {
            $decoded = $this->cleanAndDecodeJson($rawResponse);
            if (is_array($decoded) && (isset($decoded['days']) || isset($decoded['title']))) {
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

            return ['success' => true, 'raw' => $rawResponse];
        }

        return ['success' => false, 'error' => 'Không thể tạo lịch trình lúc này. Vui lòng thử lại.'];
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
