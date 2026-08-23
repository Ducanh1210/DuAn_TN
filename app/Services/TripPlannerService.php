<?php

namespace App\Services;

use App\Models\Category;
use App\Models\Itinerary;
use App\Models\ItineraryDay;
use App\Models\ItineraryItem;
use App\Models\Location;
use App\Services\Concerns\ParsesAiJson;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

/**
 * Lập lịch trình du lịch bằng AI.
 *
 * Luồng: đọc khảo sát -> lấy ứng viên từ DB -> AI phân tích đặc điểm địa điểm
 * -> chấm điểm kết hợp và gom cụm -> AI chọn điểm và chia ngày -> kiểm tra dữ liệu
 * -> backend tối ưu tuyến từng ngày -> dựng lại timeline -> trả về frontend.
 *
 * AI chịu trách nhiệm chọn điểm và chia ngày. Backend chịu trách nhiệm cuối cùng
 * về tính hợp lệ của dữ liệu và thứ tự di chuyển.
 */
class TripPlannerService
{
    use ParsesAiJson;

    /** Trọng số thời lượng tham quan khi chia buổi. */
    protected const VISIT_WEIGHT = ['dài' => 2.0, 'vừa' => 1.2, 'ngắn' => 0.7];

    protected const SLOT_TYPES = ['visit', 'food', 'transport', 'rest', 'photo'];

    protected GeminiClient $gemini;

    protected LocationIntelligenceService $intelligence;

    protected RouteOptimizerService $router;

    /** @var Collection<int, Location> */
    protected Collection $candidates;

    /** @var array<int, array<string, mixed>> */
    protected array $metadata = [];

    public function __construct(
        ?GeminiClient $gemini = null,
        ?LocationIntelligenceService $intelligence = null,
        ?RouteOptimizerService $router = null
    ) {
        $this->gemini = $gemini ?? new GeminiClient();
        $this->router = $router ?? new RouteOptimizerService();
        $this->intelligence = $intelligence ?? new LocationIntelligenceService($this->gemini, $this->router);
        $this->candidates = collect();
    }

    /**
     * Chuẩn hóa câu trả lời khảo sát thành bộ sở thích dùng cho toàn bộ luồng.
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
                $prefs['preferred_location_ids'] = array_values(array_filter(array_map('intval', explode(',', $value))));
            }
        }

        if (!$prefs['trip_type'] && $tripType) {
            $prefs['trip_type'] = $tripType;
        }

        $prefs['trip_type_slugs'] = $this->categorySlugsForTripType($prefs['trip_type']);
        $prefs['interest_slugs'] = $this->categorySlugsForInterests($prefs['interests']);

        return $prefs;
    }

    /**
     * Toàn bộ quy trình: khảo sát -> ứng viên -> phân tích -> AI lập lịch -> tối ưu tuyến.
     */
    public function generateItinerary(array $answers, ?string $tripType = null): array
    {
        $prefs = $this->parsePreferences($answers, $tripType);
        $this->candidates = $this->getCandidateLocations($prefs);

        if ($this->candidates->isEmpty()) {
            return [
                'success' => false,
                'error' => 'Chưa có địa điểm phù hợp trong hệ thống để lập lịch trình.',
            ];
        }

        $this->metadata = $this->intelligence->analyze($this->candidates, $prefs);
        $compatibility = $this->intelligence->compatibilityMatrix($this->candidates, $this->metadata, $prefs);
        $clusters = $this->intelligence->buildClusters($this->candidates, $compatibility, $this->metadata, $prefs);

        Log::info('TripPlanner: chuẩn bị dữ liệu lập lịch.', [
            'candidates' => $this->candidates->count(),
            'scale_summary' => $this->summarizeScales(),
            'clusters' => array_map(
                fn ($cluster) => $cluster['label'] . ' (' . implode(',', $cluster['members']) . ') score=' . $cluster['score'],
                $clusters
            ),
        ]);

        $systemPrompt = $this->buildPlanningSystemPrompt($prefs, $compatibility, $clusters);
        $userPrompt = $this->buildPlanningUserPrompt($answers, $prefs);

        for ($attempt = 1; $attempt <= 2; $attempt++) {
            $raw = $this->callAI($systemPrompt, $userPrompt, $attempt === 1 ? 6144 : 8192);
            if (!$raw) {
                Log::warning("TripPlanner: AI không trả nội dung (lần {$attempt}).");
                continue;
            }

            $decoded = $this->decodeAiJson($raw);
            if (!is_array($decoded)) {
                Log::warning("TripPlanner: JSON hỏng (lần {$attempt}).", ['preview' => mb_substr($raw, 0, 400)]);
                continue;
            }

            $validated = $this->validateAiItinerary($decoded, $prefs, $attempt === 2);
            if ($validated === null) {
                Log::warning("TripPlanner: dữ liệu AI không hợp lệ (lần {$attempt}).");
                continue;
            }

            $itinerary = $this->finalizeItinerary($validated, $prefs);

            return [
                'success' => true,
                'itinerary' => $itinerary,
                'meta' => [
                    'days' => $prefs['days'],
                    'budget' => $prefs['budget_label'],
                    'locations_used' => $this->candidates->count(),
                ],
            ];
        }

        return [
            'success' => false,
            'error' => 'Không thể tạo lịch trình lúc này. Vui lòng bấm "Lên lịch mới" để thử lại.',
        ];
    }

    /**
     * Danh sách ứng viên lấy từ DB theo sở thích, loại chuyến và nhu cầu dịch vụ.
     *
     * @return Collection<int, Location>
     */
    protected function getCandidateLocations(array $prefs): Collection
    {
        $all = Location::with(['category', 'images'])->where('status', 'published')->get();
        if ($all->isEmpty()) {
            $all = Location::with(['category', 'images'])->get();
        }

        $mappable = $all->filter(fn (Location $l) => $l->lat !== null && $l->lng !== null)->values();
        if ($mappable->count() >= 5) {
            $all = $mappable;
        }

        if ($this->wantsVegetarian($prefs)) {
            $all = $all->reject(fn (Location $l) => $this->isFood($l) && $this->isMeatHeavyName((string) $l->name))->values();
        }

        $wanted = array_values(array_unique(array_merge(
            $prefs['trip_type_slugs'] ?? [],
            $prefs['interest_slugs'] ?? []
        )));

        $ranked = $all->sortByDesc(function (Location $location) use ($wanted) {
            $slug = (string) ($location->category->slug ?? '');
            $score = in_array($slug, $wanted, true) ? 2.0 : 0.8;
            $score += min(1.0, (float) ($location->average_rating ?? 0) / 5);
            $score += min(1.0, log10(1 + (int) ($location->view_count ?? 0)) / 4);

            return $score;
        })->values();

        $sights = $this->pickAcrossCategories(
            $ranked->reject(fn (Location $l) => $this->isFood($l) || $this->isLodging($l))->values(),
            $wanted,
            18
        );
        $food = $this->pickAcrossCategories(
            $ranked->filter(fn (Location $l) => $this->isFood($l))->values(),
            $wanted,
            8
        );
        $hotels = !empty($prefs['need_hotel'])
            ? $this->pickAcrossCategories(
                $ranked->filter(fn (Location $l) => $this->isLodging($l))->values(),
                $wanted,
                4
            )
            : collect();

        $preferred = $all->filter(
            fn (Location $l) => in_array((int) $l->id, $prefs['preferred_location_ids'] ?? [], true)
        );

        return $preferred
            ->concat($sights)
            ->concat($food)
            ->concat($hotels)
            ->unique('id')
            ->values();
    }

    /**
     * Kiểm tra kết quả AI: đúng số ngày, đúng location_id, đúng type.
     * Trả về null nếu dữ liệu hỏng tới mức cần gọi lại AI.
     *
     * @return array<string, mixed>|null
     */
    protected function validateAiItinerary(array $decoded, array $prefs, bool $lastAttempt): ?array
    {
        $rawDays = is_array($decoded['days'] ?? null) ? $decoded['days'] : [];
        if (!$rawDays) {
            return null;
        }

        $wantedDays = max(1, (int) $prefs['days']);
        $catalog = $this->candidates->keyBy('id');
        $days = [];
        $validSlots = 0;
        $invalidSlots = 0;
        $selectedIds = [];

        foreach (array_slice($rawDays, 0, $wantedDays) as $index => $day) {
            if (!is_array($day)) {
                continue;
            }

            $slots = [];
            foreach ($day['slots'] ?? [] as $slot) {
                if (!is_array($slot)) {
                    continue;
                }

                $location = $this->resolveSlotLocation($slot, $catalog);
                $type = (string) ($slot['type'] ?? 'visit');
                $slot['type'] = in_array($type, self::SLOT_TYPES, true) ? $type : 'visit';

                if ($location && $this->isFood($location)) {
                    $slot['type'] = 'food';
                }

                if (!$location) {
                    // Slot tham quan mà không khớp địa điểm thật thì bỏ, tránh dữ liệu bịa.
                    if (in_array($slot['type'], ['visit', 'food', 'photo'], true)) {
                        $invalidSlots++;
                        continue;
                    }

                    unset($slot['location_id'], $slot['place']);
                    $slots[] = $slot;
                    continue;
                }

                if ($slot['type'] === 'food' && $this->wantsVegetarian($prefs) && $this->isMeatHeavyName((string) $location->name)) {
                    $replacement = $this->pickFoodNear($location, $prefs, []);
                    if (!$replacement) {
                        $invalidSlots++;
                        continue;
                    }
                    $location = $replacement;
                }

                $slots[] = $this->buildSlot($location, (string) ($slot['time'] ?? ''), (string) ($slot['activity'] ?? ''), $slot['type'], (string) ($slot['tip'] ?? ''));
                $selectedIds[] = (int) $location->id;
                $validSlots++;
            }

            if ($slots) {
                $days[] = [
                    'day' => count($days) + 1,
                    'title' => trim((string) ($day['title'] ?? '')) ?: 'Ngày ' . (count($days) + 1),
                    'slots' => $slots,
                ];
            }
        }

        if (!$days || $validSlots === 0) {
            return null;
        }

        $totalSlots = $validSlots + $invalidSlots;
        if (!$lastAttempt && ($invalidSlots / max(1, $totalSlots)) > 0.4) {
            Log::warning('TripPlanner: AI trả quá nhiều địa điểm không có thật.', [
                'valid' => $validSlots,
                'invalid' => $invalidSlots,
            ]);

            return null;
        }

        if (!$lastAttempt && count($days) < $wantedDays) {
            Log::warning('TripPlanner: AI trả thiếu ngày.', ['wanted' => $wantedDays, 'got' => count($days)]);

            return null;
        }

        Log::info('TripPlanner: AI đã chọn địa điểm.', [
            'days' => count($days),
            'selected_ids' => array_values(array_unique($selectedIds)),
            'dropped_slots' => $invalidSlots,
        ]);

        return [
            'title' => trim((string) ($decoded['title'] ?? '')) ?: 'Lịch trình gợi ý',
            'summary' => trim((string) ($decoded['summary'] ?? '')),
            'estimated_cost' => $prefs['budget_label'],
            'days' => $days,
            'tips' => array_values(array_filter(array_map(
                fn ($tip) => trim((string) $tip),
                (array) ($decoded['tips'] ?? [])
            ))),
        ];
    }

    /**
     * Bổ sung bữa ăn còn thiếu, tối ưu tuyến từng ngày, dựng lại timeline và thống kê.
     *
     * @param  array<string, mixed>  $itinerary
     * @return array<string, mixed>
     */
    protected function finalizeItinerary(array $itinerary, array $prefs): array
    {
        $days = $this->rebalanceDays($itinerary['days']);
        $days = $this->refineDaySights($days);
        $days = $this->bridgeConsecutiveDays($days, $prefs);
        $totalDays = count($days);
        $previousStay = null;
        $usedAcrossTrip = [];

        foreach ($days as $index => $day) {
            // Quán ăn là điểm dịch vụ nên tách ra: tối ưu tuyến tham quan trước,
            // sau đó mới chọn quán nằm sát tuyến và chèn vào đúng nhịp trong ngày.
            [$anchors, $meals, $lodging] = $this->splitDaySlots($day['slots']);

            $slots = $this->optimizeDaySlots(array_merge($anchors, $lodging), $prefs, $index + 1, $previousStay);
            $slots = $this->packSightBlocks($slots);
            $slots = $this->placeMeals($slots, $meals, $index, $totalDays, $prefs, $previousStay, $usedAcrossTrip);
            $slots = $this->assignPeriods($slots, $prefs);

            $lodgingIndex = $this->lastLodgingIndex($slots);
            $previousStay = $lodgingIndex === null ? null : ($slots[$lodgingIndex]['_loc'] ?? null);

            foreach ($slots as $slot) {
                if (!empty($slot['location_id'])) {
                    $usedAcrossTrip[] = (int) $slot['location_id'];
                }
            }

            $slots = $this->attachLegDistances($slots);

            $days[$index]['day'] = $index + 1;
            $days[$index]['slots'] = $slots;
        }

        $itinerary['days'] = $days;
        $itinerary['estimated_cost'] = $prefs['budget_label'];
        $itinerary['stats'] = $this->buildItineraryStats($days, $prefs);

        return $itinerary;
    }

    /**
     * Gom lại địa bàn từng ngày: hoán đổi hoặc chuyển hẳn điểm tham quan sang ngày khác
     * khi việc đó làm các ngày gọn lại rõ rệt. Số điểm mỗi ngày vẫn giữ chênh lệch tối đa 1.
     *
     * @param  array<int, array<string, mixed>>  $days
     * @return array<int, array<string, mixed>>
     */
    protected function rebalanceDays(array $days): array
    {
        if (count($days) < 2) {
            return $days;
        }

        $movable = [];
        $pinned = [];

        foreach ($days as $index => $day) {
            $movable[$index] = [];
            $pinned[$index] = [];

            foreach ($day['slots'] as $slot) {
                $location = $slot['_loc'] ?? null;
                $isVisit = $location instanceof Location
                    && $location->lat !== null
                    && ($slot['type'] ?? '') !== 'food'
                    && !$this->isLodging($location);

                if ($isVisit) {
                    $movable[$index][] = $slot;
                } else {
                    $pinned[$index][] = $slot;
                }
            }
        }

        $before = $this->assignmentCostKm($movable);
        $current = $before;
        $dayKeys = array_keys($movable);
        $guard = 0;
        $improved = true;

        while ($improved && $guard < 20) {
            $improved = false;
            $guard++;

            foreach ($dayKeys as $a) {
                foreach ($dayKeys as $b) {
                    if ($b <= $a) {
                        continue;
                    }

                    foreach ($movable[$a] as $i => $slotA) {
                        foreach ($movable[$b] as $j => $slotB) {
                            $movable[$a][$i] = $slotB;
                            $movable[$b][$j] = $slotA;
                            $candidate = $this->assignmentCostKm($movable);

                            // Chỉ nhận khi cải thiện đủ đáng kể, tránh xáo trộn lịch AI vì vài trăm mét.
                            if ($candidate + 1.0 < $current) {
                                $current = $candidate;
                                $improved = true;
                                continue 2;
                            }

                            $movable[$a][$i] = $slotA;
                            $movable[$b][$j] = $slotB;
                        }
                    }
                }
            }

            // Hoán đổi giữ nguyên số điểm mỗi ngày, nên còn cần chuyển hẳn một điểm
            // từ ngày đang trải rộng sang ngày gọn hơn.
            foreach ($dayKeys as $from) {
                foreach ($dayKeys as $to) {
                    if ($from === $to || count($movable[$from]) < 2) {
                        continue;
                    }

                    foreach ($movable[$from] as $index => $slot) {
                        $candidateFrom = $movable[$from];
                        array_splice($candidateFrom, $index, 1);
                        $candidateTo = array_merge($movable[$to], [$slot]);

                        // Không để một ngày phình ra quá 2 điểm so với ngày ít nhất.
                        if (abs(count($candidateFrom) - count($candidateTo)) > 2) {
                            continue;
                        }

                        $backupFrom = $movable[$from];
                        $backupTo = $movable[$to];
                        $movable[$from] = $candidateFrom;
                        $movable[$to] = $candidateTo;
                        $candidate = $this->assignmentCostKm($movable);

                        if ($candidate + 1.0 < $current) {
                            $current = $candidate;
                            $improved = true;
                            continue 2;
                        }

                        $movable[$from] = $backupFrom;
                        $movable[$to] = $backupTo;
                    }
                }
            }
        }

        $after = $this->assignmentCostKm($movable);
        if ($after + 0.01 >= $before) {
            return $days;
        }

        Log::info('TripPlanner: gom lại địa bàn từng ngày.', [
            'cost_before_km' => round($before, 1),
            'cost_after_km' => round($after, 1),
        ]);

        foreach ($days as $index => $day) {
            $days[$index]['slots'] = array_values(array_merge($movable[$index], $pinned[$index]));
        }

        return $days;
    }

    /**
     * Sau khi gom ngày, thay điểm xa bằng điểm còn trống gần chỗ vừa ở / vừa tham quan,
     * rồi bổ sung cho đủ buổi chiều — nhất là ngày cuối.
     *
     * @param  array<int, array<string, mixed>>  $days
     * @return array<int, array<string, mixed>>
     */
    protected function refineDaySights(array $days): array
    {
        foreach ($days as $index => $day) {
            $start = $this->dayStartLocation($days, $index);
            $used = $this->visitIdsInDays($days);
            $days[$index] = $this->preferNearbyUnusedSights($day, $start, $used);

            $used = $this->visitIdsInDays($days);
            $days[$index] = $this->fillAfternoonSights($days[$index], $start, $used);
        }

        return $days;
    }

    /**
     * Nếu trong ngày đang có một điểm xa, mà DB còn chùa/điểm gần chỗ xuất phát hơn
     * và chưa được dùng, đổi sang điểm gần đó.
     *
     * @param  array<string, mixed>  $day
     * @param  array<int, int>  $usedIds
     * @return array<string, mixed>
     */
    protected function preferNearbyUnusedSights(array $day, ?Location $start, array $usedIds): array
    {
        if (!$start instanceof Location) {
            return $day;
        }

        $guard = 0;

        while ($guard++ < 8) {
            $visits = $this->visitSlots($day['slots']);
            if (!$visits) {
                break;
            }

            $currentCost = $this->pathFromStartKm($start, $visits);
            $unused = $this->unusedVisitLocations($usedIds);
            if ($this->slotsHaveTemple($visits)) {
                $temples = $unused->filter(fn (Location $location) => ($location->category->slug ?? '') === 'tam-linh');
                if ($temples->isNotEmpty()) {
                    $unused = $temples->values();
                }
            }
            $best = null;

            foreach ($visits as $visitIndex => $visit) {
                $current = $visit['_loc'] ?? null;
                if (!$current instanceof Location) {
                    continue;
                }

                foreach ($unused as $candidate) {
                    if (!$this->canReplaceSight($current, $candidate)) {
                        continue;
                    }
                    $trial = $visits;
                    $trial[$visitIndex] = $this->visitSlot($candidate);
                    $cost = $this->pathFromStartKm($start, $trial);

                    // Chỉ đổi khi gần hơn rõ rệt, tránh xáo vì vài trăm mét.
                    if ($cost + 3.0 >= $currentCost) {
                        continue;
                    }

                    if ($best === null || $cost < $best['cost']) {
                        $best = [
                            'cost' => $cost,
                            'visit_index' => $visitIndex,
                            'from' => $current,
                            'to' => $candidate,
                        ];
                    }
                }
            }

            if (!$best) {
                break;
            }

            $replaced = $visits[$best['visit_index']];
            foreach ($day['slots'] as $slotIndex => $slot) {
                if ((int) ($slot['location_id'] ?? 0) === (int) ($replaced['location_id'] ?? 0)) {
                    $day['slots'][$slotIndex] = $this->visitSlot($best['to']);
                    break;
                }
            }

            $usedIds[] = (int) $best['to']->id;
            $usedIds = array_values(array_diff($usedIds, [(int) $best['from']->id]));

            Log::info('TripPlanner: đổi điểm xa sang điểm còn trống gần hơn.', [
                'from' => $best['from']->name,
                'to' => $best['to']->name,
                'cost_before_km' => round($currentCost, 1),
                'cost_after_km' => round($best['cost'], 1),
            ]);
        }

        return $day;
    }

    /**
     * Mỗi ngày cần đủ hai buổi tham quan. Nếu chỉ còn đúng một cặp điểm nhỏ
     * thì lấy thêm điểm còn trống gần tuyến để có buổi chiều.
     *
     * @param  array<string, mixed>  $day
     * @param  array<int, int>  $usedIds
     * @return array<string, mixed>
     */
    protected function fillAfternoonSights(array $day, ?Location $start, array $usedIds): array
    {
        $added = 0;

        while ($added < 2 && $this->needsAfternoonSight($day['slots'])) {
            $anchor = $start ?? $this->lastVisitLocation($day['slots']);
            $preferSlug = $this->slotsHaveTemple($day['slots']) ? 'tam-linh' : $this->dominantVisitSlug($day['slots']);
            $candidate = $this->nearestUnusedVisit($anchor, $usedIds, 12.0, $preferSlug)
                ?? $this->nearestUnusedVisit($anchor, $usedIds, 18.0, $preferSlug);

            if (!$candidate instanceof Location) {
                break;
            }

            $day['slots'][] = $this->visitSlot($candidate);
            $usedIds[] = (int) $candidate->id;
            $added++;

            Log::info('TripPlanner: thêm điểm gần để có buổi chiều.', [
                'location' => $candidate->name,
            ]);
        }

        return $day;
    }

    /**
     * Điểm xuất phát của một ngày: chỗ nghỉ đêm trước, hoặc điểm tham quan cuối ngày trước.
     *
     * @param  array<int, array<string, mixed>>  $days
     */
    protected function dayStartLocation(array $days, int $index): ?Location
    {
        if ($index < 1) {
            return null;
        }

        $previous = $days[$index - 1]['slots'] ?? [];
        $lodging = $this->lastLodgingIndex($previous);
        if ($lodging !== null && ($previous[$lodging]['_loc'] ?? null) instanceof Location) {
            return $previous[$lodging]['_loc'];
        }

        return $this->lastVisitLocation($previous);
    }

    /** @param  array<int, array<string, mixed>>  $slots */
    protected function lastVisitLocation(array $slots): ?Location
    {
        for ($i = count($slots) - 1; $i >= 0; $i--) {
            $location = $slots[$i]['_loc'] ?? null;
            if ($location instanceof Location && !$this->isFood($location) && !$this->isLodging($location)) {
                return $location;
            }
        }

        return null;
    }

    /**
     * @param  array<int, array<string, mixed>>  $days
     * @return array<int, int>
     */
    protected function visitIdsInDays(array $days): array
    {
        $ids = [];
        foreach ($days as $day) {
            foreach ($day['slots'] ?? [] as $slot) {
                $location = $slot['_loc'] ?? null;
                if ($location instanceof Location && !$this->isFood($location) && !$this->isLodging($location)) {
                    $ids[] = (int) $location->id;
                }
            }
        }

        return array_values(array_unique($ids));
    }

    /**
     * @param  array<int, array<string, mixed>>  $slots
     * @return array<int, array<string, mixed>>
     */
    protected function visitSlots(array $slots): array
    {
        $visits = [];
        foreach ($slots as $slot) {
            $location = $slot['_loc'] ?? null;
            if ($location instanceof Location && !$this->isFood($location) && !$this->isLodging($location)) {
                $visits[] = $slot;
            }
        }

        return $visits;
    }

    /**
     * Quãng đường đi từ điểm xuất phát qua hết các điểm trong ngày.
     *
     * @param  array<int, array<string, mixed>>  $visits
     */
    protected function pathFromStartKm(?Location $start, array $visits): float
    {
        $locations = [];
        foreach ($visits as $slot) {
            if (($slot['_loc'] ?? null) instanceof Location) {
                $locations[] = $slot['_loc'];
            }
        }

        if (!$locations) {
            return 0.0;
        }

        if ($start instanceof Location) {
            return $this->nearestNeighborPathKm($start, $locations);
        }

        return $this->routeSpanKm($visits);
    }

    /**
     * @param  array<int, Location>  $locations
     */
    protected function nearestNeighborPathKm(Location $start, array $locations): float
    {
        $remaining = array_values($locations);
        $current = $start;
        $total = 0.0;

        while ($remaining) {
            $bestIndex = 0;
            $bestKm = INF;

            foreach ($remaining as $index => $location) {
                $km = $this->legKm($current, $location);
                if ($km < $bestKm) {
                    $bestKm = $km;
                    $bestIndex = $index;
                }
            }

            $total += $bestKm;
            $current = $remaining[$bestIndex];
            array_splice($remaining, $bestIndex, 1);
        }

        return $total;
    }

    /** @param  array<int, int>  $usedIds */
    protected function unusedVisitLocations(array $usedIds): Collection
    {
        return $this->visitPool()->filter(function (Location $location) use ($usedIds) {
            if (in_array((int) $location->id, $usedIds, true)) {
                return false;
            }

            $this->ensureVisitMetadata($location);

            return true;
        })->values();
    }

    /** Toàn bộ điểm tham quan có tọa độ trong DB, không gồm quán ăn và chỗ nghỉ. */
    protected function visitPool(): Collection
    {
        return Location::with('category')
            ->whereNotNull('lat')
            ->whereNotNull('lng')
            ->get()
            ->filter(fn (Location $location) => !$this->isFood($location) && !$this->isLodging($location))
            ->values();
    }

    /** Chùa chỉ đổi sang chùa; không lấy công viên hay làng nghề để thay điểm tâm linh. */
    protected function canReplaceSight(Location $current, Location $candidate): bool
    {
        $currentSlug = (string) ($current->category->slug ?? '');
        $candidateSlug = (string) ($candidate->category->slug ?? '');

        if ($currentSlug === 'tam-linh') {
            return $candidateSlug === 'tam-linh';
        }

        return true;
    }

    /** @param  array<int, array<string, mixed>>  $slots */
    protected function slotsHaveTemple(array $slots): bool
    {
        foreach ($this->visitSlots($slots) as $slot) {
            if (($slot['_loc']->category->slug ?? '') === 'tam-linh') {
                return true;
            }
        }

        return false;
    }

    /** @param  array<int, array<string, mixed>>  $slots */
    protected function dominantVisitSlug(array $slots): ?string
    {
        $counts = [];
        foreach ($this->visitSlots($slots) as $slot) {
            $slug = (string) ($slot['_loc']->category->slug ?? '');
            if ($slug === '') {
                continue;
            }
            $counts[$slug] = ($counts[$slug] ?? 0) + 1;
        }

        if (!$counts) {
            return null;
        }

        arsort($counts);

        return (string) array_key_first($counts);
    }

    /** @param  array<int, int>  $usedIds */
    protected function nearestUnusedVisit(?Location $anchor, array $usedIds, float $maxKm, ?string $preferSlug = null): ?Location
    {
        $pool = $this->unusedVisitLocations($usedIds);
        if ($preferSlug) {
            $matched = $pool->filter(fn (Location $location) => ($location->category->slug ?? '') === $preferSlug);
            if ($matched->isNotEmpty()) {
                $pool = $matched->values();
            }
        }

        $best = null;
        $bestKm = INF;

        foreach ($pool as $location) {
            if (!$anchor instanceof Location) {
                return $location;
            }

            $km = $this->legKm($anchor, $location);
            if ($km <= $maxKm && $km < $bestKm) {
                $bestKm = $km;
                $best = $location;
            }
        }

        return $best;
    }

    protected function visitSlot(Location $location): array
    {
        $this->ensureVisitMetadata($location);

        return $this->buildSlot(
            $location,
            '',
            'Ghé ' . $location->name . '.',
            'visit',
            ''
        );
    }

    protected function ensureVisitMetadata(Location $location): void
    {
        $id = (int) $location->id;
        if (isset($this->metadata[$id])) {
            return;
        }

        $name = mb_strtolower((string) $location->name);
        $slug = (string) ($location->category->slug ?? '');
        $temple = $slug === 'tam-linh'
            || str_contains($name, 'chùa')
            || str_contains($name, 'đền')
            || str_contains($name, 'phủ');

        $this->metadata[$id] = [
            'scale' => $temple ? 'nhỏ' : 'vừa',
            'estimated_visit' => $temple ? 'ngắn' : 'vừa',
            'can_combine' => true,
            'source' => 'nearby-fill',
        ];
    }

    /** @param  array<int, array<string, mixed>>  $slots */
    protected function sightGroupCount(array $slots): int
    {
        return count($this->groupSightVisits($this->visitSlots($slots)));
    }

    /**
     * Thiếu buổi chiều, hoặc buổi chiều chỉ có một điểm nhỏ đứng một mình.
     *
     * @param  array<int, array<string, mixed>>  $slots
     */
    protected function needsAfternoonSight(array $slots): bool
    {
        return $this->sightGroupCount($slots) < 2;
    }

    /**
     * Chi phí của một cách chia ngày: tổng quãng đường phải đi trong từng ngày,
     * cộng quãng chuyển giữa hai ngày liên tiếp.
     *
     * @param  array<int, array<int, array<string, mixed>>>  $groups
     */
    protected function assignmentCostKm(array $groups): float
    {
        $total = 0.0;
        $previous = null;

        foreach ($groups as $group) {
            $total += $this->routeSpanKm($group);

            $centroid = $this->centroidOf($group);
            if ($centroid && $previous) {
                $total += $this->router->haversineKm(
                    $previous['lat'],
                    $previous['lng'],
                    $centroid['lat'],
                    $centroid['lng']
                );
            }
            $previous = $centroid ?? $previous;
        }

        return $total;
    }

    /**
     * Ước lượng quãng đường phải đi qua hết các điểm trong một ngày, không quay về chỗ xuất phát.
     * Dùng láng giềng gần nhất từ mọi điểm khởi đầu rồi lấy phương án ngắn nhất.
     *
     * @param  array<int, array<string, mixed>>  $slots
     */
    protected function routeSpanKm(array $slots): float
    {
        $locations = [];
        foreach ($slots as $slot) {
            if (($slot['_loc'] ?? null) instanceof Location) {
                $locations[] = $slot['_loc'];
            }
        }

        $count = count($locations);
        if ($count < 2) {
            return 0.0;
        }

        $best = INF;

        for ($start = 0; $start < $count; $start++) {
            $visited = [$start => true];
            $current = $start;
            $total = 0.0;

            for ($step = 1; $step < $count; $step++) {
                $nearest = null;
                $nearestKm = INF;

                for ($next = 0; $next < $count; $next++) {
                    if (isset($visited[$next])) {
                        continue;
                    }
                    $km = $this->legKm($locations[$current], $locations[$next]);
                    if ($km < $nearestKm) {
                        $nearestKm = $km;
                        $nearest = $next;
                    }
                }

                if ($nearest === null) {
                    break;
                }

                $visited[$nearest] = true;
                $total += $nearestKm;
                $current = $nearest;
            }

            $best = min($best, $total);
        }

        return $best === INF ? 0.0 : $best;
    }

    /**
     * Nối ngày có nghỉ đêm: đặt khách sạn sát điểm mở ngày sau, chuyển điểm
     * tham quan đang lệch cuối ngày 1 sang ngày 2, rồi chọn lại điểm đầu ngày 2
     * từ chỗ vừa nghỉ.
     *
     * @param  array<int, array<string, mixed>>  $days
     * @return array<int, array<string, mixed>>
     */
    protected function bridgeConsecutiveDays(array $days, array $prefs): array
    {
        if (empty($prefs['need_hotel']) || count($days) < 2) {
            return $days;
        }

        $hotels = $this->hotelPool();
        if ($hotels->isEmpty()) {
            return $days;
        }

        for ($index = 0; $index < count($days) - 1; $index++) {
            $hotel = $this->chooseBridgeHotel(
                $this->visitSlots($days[$index]['slots']),
                $this->visitSlots($days[$index + 1]['slots']),
                $hotels
            );

            if (!$hotel instanceof Location) {
                continue;
            }

            $days[$index] = $this->setDayLodging($days[$index], $hotel);
            $days = $this->moveOutliersTowardNextDay($days, $index, $hotel);

            $hotel = $this->chooseBridgeHotel(
                $this->visitSlots($days[$index]['slots']),
                $this->visitSlots($days[$index + 1]['slots']),
                $hotels
            ) ?? $hotel;

            $days[$index] = $this->setDayLodging($days[$index], $hotel);
        }

        return $this->refineDaySights($days);
    }

    /**
     * Chọn chỗ nghỉ gần điểm ngày sau hơn — sáng hôm sau không phải chạy ngược
     * về một cụm khác. Ngày hiện tại chỉ cần có một điểm còn trong tầm với khách sạn.
     *
     * @param  array<int, array<string, mixed>>  $today
     * @param  array<int, array<string, mixed>>  $tomorrow
     */
    protected function chooseBridgeHotel(array $today, array $tomorrow, Collection $hotels): ?Location
    {
        $best = null;
        $bestCost = INF;

        foreach ($hotels as $hotel) {
            $toTomorrow = $this->nearestVisitKm($hotel, $tomorrow);
            $toToday = $this->nearestVisitKm($hotel, $today);
            if ($toTomorrow === null && $toToday === null) {
                continue;
            }

            $cost = ($toToday ?? 20.0) + 2.0 * ($toTomorrow ?? 20.0);
            if ($cost < $bestCost) {
                $bestCost = $cost;
                $best = $hotel;
            }
        }

        return $best;
    }

    /**
     * Điểm ngày 1 nằm gần khách sạn / ngày 2 hơn là gần các điểm còn lại của ngày 1
     * thì chuyển sang ngày 2, để cuối ngày 1 không bị kéo ra một cụm lệch.
     *
     * @param  array<int, array<string, mixed>>  $days
     * @return array<int, array<string, mixed>>
     */
    protected function moveOutliersTowardNextDay(array $days, int $index, Location $hotel): array
    {
        $moved = 0;

        while ($moved < 2) {
            $visits = $this->visitSlots($days[$index]['slots']);
            if (count($visits) <= 2) {
                break;
            }

            $outlier = null;
            $outlierKm = 0.0;

            foreach ($visits as $slot) {
                $location = $slot['_loc'] ?? null;
                if (!$location instanceof Location) {
                    continue;
                }

                $toHotel = $this->legKm($location, $hotel);
                $others = array_values(array_filter(
                    $visits,
                    fn (array $other) => (int) ($other['location_id'] ?? 0) !== (int) $location->id
                ));
                $toSiblings = $this->nearestVisitKm($location, $others);

                // Chỉ chuyển khi điểm đó gần khách sạn/ngày sau hơn là gần cụm ngày đang đứng.
                if ($toHotel < 8.0 || $toSiblings === null || $toSiblings <= $toHotel - 2.0) {
                    continue;
                }

                if ($toHotel > $outlierKm) {
                    $outlierKm = $toHotel;
                    $outlier = $slot;
                }
            }

            if (!$outlier) {
                break;
            }

            $outlierId = (int) ($outlier['location_id'] ?? 0);
            $days[$index]['slots'] = array_values(array_filter(
                $days[$index]['slots'],
                fn (array $slot) => (int) ($slot['location_id'] ?? 0) !== $outlierId
            ));
            $days[$index + 1]['slots'][] = $outlier;
            $moved++;

            Log::info('TripPlanner: chuyển điểm lệch cuối ngày sang ngày tiếp theo.', [
                'from_day' => $index + 1,
                'location' => $outlier['location'] ?? '',
                'distance_to_hotel_km' => round($outlierKm, 1),
            ]);
        }

        return $days;
    }

    /**
     * @param  array<string, mixed>  $day
     * @return array<string, mixed>
     */
    protected function setDayLodging(array $day, Location $hotel): array
    {
        $slot = $this->buildSlot(
            $hotel,
            'Buổi tối',
            'Nhận phòng và nghỉ đêm, gần điểm sẽ đi sáng hôm sau.',
            'rest',
            ''
        );

        $index = $this->lastLodgingIndex($day['slots']);
        if ($index === null) {
            $day['slots'][] = $slot;
        } else {
            $day['slots'][$index] = $slot;
        }

        return $day;
    }

    /** @param  array<int, array<string, mixed>>  $visits */
    protected function nearestVisitKm(Location $from, array $visits): ?float
    {
        $best = null;

        foreach ($visits as $slot) {
            $location = $slot['_loc'] ?? null;
            if (!$location instanceof Location) {
                continue;
            }

            $km = $this->legKm($from, $location);
            if ($best === null || $km < $best) {
                $best = $km;
            }
        }

        return $best;
    }

    protected function hotelPool(): Collection
    {
        $fromCandidates = $this->candidates->filter(
            fn (Location $location) => $this->isLodging($location) && $location->lat !== null
        );

        $fromDb = Location::with('category')
            ->whereNotNull('lat')
            ->whereNotNull('lng')
            ->get()
            ->filter(fn (Location $location) => $this->isLodging($location));

        return $fromCandidates->concat($fromDb)->unique('id')->values();
    }

    /**
     * Tâm địa bàn của một ngày, chỉ tính điểm tham quan.
     *
     * @param  array<int, array<string, mixed>>  $slots
     * @return array{lat: float, lng: float}|null
     */
    protected function centroidOf(array $slots): ?array
    {
        $lat = 0.0;
        $lng = 0.0;
        $count = 0;

        foreach ($slots as $slot) {
            $location = $slot['_loc'] ?? null;
            if (!$location instanceof Location || $location->lat === null || $this->isLodging($location)) {
                continue;
            }
            if (($slot['type'] ?? '') === 'food') {
                continue;
            }

            $lat += (float) $location->lat;
            $lng += (float) $location->lng;
            $count++;
        }

        return $count ? ['lat' => $lat / $count, 'lng' => $lng / $count] : null;
    }

    /**
     * Tối ưu thứ tự di chuyển trong một ngày rồi dựng lại mảng slot theo đúng thứ tự mới.
     *
     * @param  array<int, array<string, mixed>>  $slots
     * @return array<int, array<string, mixed>>
     */
    protected function optimizeDaySlots(array $slots, array $prefs, int $dayNumber, ?Location $startFrom = null): array
    {
        $routable = [];
        $floating = [];

        foreach ($slots as $index => $slot) {
            $location = $slot['_loc'] ?? null;
            if ($location instanceof Location && $location->lat !== null && $location->lng !== null) {
                $routable[] = $slot;
                continue;
            }

            // Giữ vị trí tương đối của slot không có tọa độ để chèn lại sau khi tối ưu.
            $floating[] = ['after' => count($routable), 'slot' => $slot];
        }

        if (count($routable) < 2) {
            return $this->mergeFloatingSlots($routable, $floating);
        }

        $fixedLast = null;
        if (!empty($prefs['need_hotel'])) {
            foreach ($routable as $index => $slot) {
                if ($this->isLodging($slot['_loc'])) {
                    $fixedLast = $index;
                }
            }
        }

        $points = array_map(
            fn (array $slot) => ['lat' => $slot['_loc']->lat, 'lng' => $slot['_loc']->lng],
            $routable
        );

        // Ngày tiếp theo bắt đầu từ nơi vừa nghỉ đêm, nên neo tuyến vào đó thay vì
        // để thuật toán tự chọn một điểm bất kỳ làm mốc.
        $offset = 0;
        $fixedFirst = null;
        if ($startFrom instanceof Location && $startFrom->lat !== null && $startFrom->lng !== null) {
            array_unshift($points, ['lat' => $startFrom->lat, 'lng' => $startFrom->lng]);
            $offset = 1;
            $fixedFirst = 0;
            $fixedLast = $fixedLast === null ? null : $fixedLast + 1;
        }

        $result = $this->router->optimizeOrder($points, $fixedLast, $fixedFirst);

        $ordered = [];
        foreach ($result['order'] as $index) {
            $slotIndex = $index - $offset;
            if ($slotIndex >= 0 && isset($routable[$slotIndex])) {
                $ordered[] = $routable[$slotIndex];
            }
        }

        Log::info("TripPlanner: tối ưu tuyến ngày {$dayNumber}.", [
            'before' => $this->describeRoute($routable),
            'after' => $this->describeRoute($ordered),
            'distance_before_km' => $result['original_distance_km'],
            'distance_after_km' => $result['distance_km'],
            'duration_after_min' => $result['duration_min'],
            'distance_source' => $result['source'],
            'method' => $result['method'],
        ]);

        return $this->mergeFloatingSlots($ordered, $floating);
    }

    /**
     * @param  array<int, array<string, mixed>>  $ordered
     * @param  array<int, array{after: int, slot: array<string, mixed>}>  $floating
     * @return array<int, array<string, mixed>>
     */
    protected function mergeFloatingSlots(array $ordered, array $floating): array
    {
        foreach (array_reverse($floating) as $item) {
            $position = min($item['after'], count($ordered));
            array_splice($ordered, $position, 0, [$item['slot']]);
        }

        return array_values($ordered);
    }

    /**
     * Tách slot trong ngày thành điểm tham quan, bữa ăn và nơi lưu trú.
     *
     * @param  array<int, array<string, mixed>>  $slots
     * @return array{0: array<int, array<string, mixed>>, 1: array<int, array<string, mixed>>, 2: array<int, array<string, mixed>>}
     */
    protected function splitDaySlots(array $slots): array
    {
        $anchors = [];
        $meals = [];
        $lodging = [];

        foreach ($slots as $slot) {
            if (($slot['type'] ?? '') === 'food') {
                $meals[] = $slot;
            } elseif ($this->isLodging($slot['_loc'] ?? null)) {
                $lodging[] = $slot;
            } else {
                $anchors[] = $slot;
            }
        }

        return [$anchors, $meals, $lodging];
    }

    /**
     * Gom điểm tham quan thành các buổi: điểm lớn đứng một mình, điểm nhỏ/vừa đi cặp 2
     * nơi gần nhau. Kết quả ghi _block = morning|afternoon để chia bữa và gán buổi.
     *
     * @param  array<int, array<string, mixed>>  $slots
     * @return array<int, array<string, mixed>>
     */
    protected function packSightBlocks(array $slots): array
    {
        $visits = [];
        $lodging = [];
        $other = [];

        foreach ($slots as $slot) {
            $location = $slot['_loc'] ?? null;
            if ($this->isLodging($location)) {
                $lodging[] = $slot;
            } elseif ($location instanceof Location && ($slot['type'] ?? '') !== 'food') {
                $visits[] = $slot;
            } else {
                $other[] = $slot;
            }
        }

        $blocks = $this->groupSightVisits($visits);

        $packed = [];
        foreach ($blocks as $index => $group) {
            $label = $index === 0 ? 'morning' : 'afternoon';
            foreach ($group as $slot) {
                $slot['_block'] = $label;
                $packed[] = $slot;
            }
        }

        Log::info('TripPlanner: chia buổi theo quy mô địa điểm.', [
            'blocks' => array_map(
                fn ($group) => array_map(fn ($slot) => $slot['location'] ?? '', $group),
                $blocks
            ),
        ]);

        return array_values(array_merge($packed, $other, $lodging));
    }

    /**
     * Gom điểm theo quy mô: điểm lớn một mình một buổi, điểm nhỏ/vừa đi cặp nếu gần nhau.
     *
     * @param  array<int, array<string, mixed>>  $visits
     * @return array<int, array<int, array<string, mixed>>>
     */
    protected function groupSightVisits(array $visits): array
    {
        $blocks = [];
        $count = count($visits);
        $i = 0;

        while ($i < $count) {
            $current = $visits[$i];
            $group = [$current];

            if (!$this->isLargeSight($current['_loc'] ?? null) && isset($visits[$i + 1])) {
                $next = $visits[$i + 1];
                $nextLoc = $next['_loc'] ?? null;
                if (
                    $nextLoc instanceof Location
                    && !$this->isLargeSight($nextLoc)
                    && $this->areNearby($current['_loc'], $nextLoc)
                ) {
                    $group[] = $next;
                    $i++;
                } elseif (
                    $this->isSmallSight($current['_loc'] ?? null)
                    && $nextLoc instanceof Location
                    && !$this->isLargeSight($nextLoc)
                ) {
                    $group[] = $next;
                    $i++;
                }
            }

            $blocks[] = $group;
            $i++;
        }

        return $blocks;
    }

    /**
     * Chèn bữa ăn vào tuyến đã tối ưu. Với mỗi bữa, chọn quán làm phát sinh ít đường vòng nhất
     * trong số quán AI đề xuất và các quán gần tuyến, rồi đặt vào đúng nhịp trong ngày.
     *
     * @param  array<int, array<string, mixed>>  $route
     * @param  array<int, array<string, mixed>>  $suggested
     * @return array<int, array<string, mixed>>
     */
    protected function placeMeals(
        array $route,
        array $suggested,
        int $dayIndex,
        int $totalDays,
        array $prefs,
        ?Location $startFrom = null,
        array $usedAcrossTrip = []
    ): array {
        if (!$route) {
            return array_values($suggested);
        }

        $lodgingIndex = $this->lastLodgingIndex($route);
        $hasOvernight = !empty($prefs['need_hotel']) && ($dayIndex < $totalDays - 1 || $lodgingIndex !== null);

        $breakfast = count($suggested) >= 3 ? array_shift($suggested) : null;
        $mealCount = max(1, min(2, count($suggested)));
        if ($hasOvernight) {
            $mealCount = 2;
        }

        // Không lặp lại quán đã dùng ở ngày trước.
        $usedIds = $usedAcrossTrip;
        foreach (array_merge($route, $suggested) as $slot) {
            if (!empty($slot['location_id'])) {
                $usedIds[] = (int) $slot['location_id'];
            }
        }

        // Chèn bữa trưa đúng ranh buổi sáng: sau điểm lớn (1 điểm/buổi) hoặc sau cặp điểm nhỏ.
        $boundary = $this->morningBoundary($route, $prefs);
        $options = $this->mealOptions($route, $suggested, $prefs, $usedIds);

        // Chọn bữa trưa và bữa tối cùng lúc. Nếu chọn lần lượt, bữa trưa dễ chiếm mất
        // quán duy nhất gần chặng cuối, khiến bữa tối phải chạy ngược ra xa.
        $solo = ['cost' => INF, 'lunch' => null];
        $pair = ['cost' => INF, 'lunch' => null, 'dinner' => null];

        foreach ($options as $lunchOption) {
            $lunchCost = $this->insertionCost($route, $lunchOption['location'], $boundary);
            $lunchScore = $lunchOption['from_ai'] ? $lunchCost * 0.9 : $lunchCost;

            if ($lunchScore < $solo['cost']) {
                $solo = ['cost' => $lunchScore, 'lunch' => $lunchOption];
            }

            if ($mealCount < 2) {
                continue;
            }

            $withLunch = $route;
            array_splice($withLunch, $boundary, 0, [$this->mealSlot($lunchOption, 'lunch')]);
            $dinnerPosition = $this->mealInsertLimit($withLunch);

            foreach ($options as $dinnerOption) {
                if ((int) $dinnerOption['location']->id === (int) $lunchOption['location']->id) {
                    continue;
                }

                $dinnerCost = $this->insertionCost($withLunch, $dinnerOption['location'], $dinnerPosition);
                $dinnerScore = $dinnerOption['from_ai'] ? $dinnerCost * 0.9 : $dinnerCost;

                if ($lunchScore + $dinnerScore < $pair['cost']) {
                    $pair = [
                        'cost' => $lunchScore + $dinnerScore,
                        'lunch' => $lunchOption,
                        'dinner' => $dinnerOption,
                        'dinner_position' => $dinnerPosition,
                    ];
                }
            }
        }

        // Bữa thứ hai chỉ giữ khi không kéo tuyến đi vòng: thà ăn quanh chỗ nghỉ
        // còn hơn chạy ngược ra xa rồi quay lại.
        $budget = $hasOvernight
            ? max(8.0, 0.5 * $this->routeDistanceKm($route))
            : max(3.0, 0.3 * $this->routeDistanceKm($route));
        $marginal = $pair['lunch'] ? $pair['cost'] - $solo['cost'] : INF;
        $keepDinner = $pair['lunch'] !== null && $marginal <= $budget;

        $chosenLunch = $keepDinner ? $pair['lunch'] : $solo['lunch'];

        if (!$keepDinner && $pair['lunch'] !== null) {
            Log::info('TripPlanner: bỏ bữa thứ hai vì quán còn lại kéo tuyến đi vòng.', [
                'day' => $dayIndex + 1,
                'detour_km' => round($marginal, 1),
                'budget_km' => round($budget, 1),
            ]);
        }

        if ($chosenLunch) {
            array_splice($route, $boundary, 0, [$this->mealSlot($chosenLunch, 'lunch')]);
            $usedIds[] = (int) $chosenLunch['location']->id;
        }

        if ($keepDinner) {
            array_splice($route, $pair['dinner_position'], 0, [$this->mealSlot($pair['dinner'], 'dinner')]);
            $usedIds[] = (int) $pair['dinner']['location']->id;
        }

        // Ngày nối tiếp sau một đêm nghỉ thì bắt đầu bằng bữa sáng ngay gần chỗ nghỉ.
        $breakfast ??= $startFrom instanceof Location
            ? $this->pickBreakfast($route, $startFrom, $prefs, $usedIds)
            : null;

        if ($breakfast) {
            array_unshift($route, $breakfast);
        }

        return array_values($route);
    }

    /**
     * Danh sách quán ứng viên cho các bữa trong ngày: quán AI đề xuất đứng trước,
     * sau đó là những quán gần tuyến nhất.
     *
     * @param  array<int, array<string, mixed>>  $route
     * @param  array<int, array<string, mixed>>  $suggested
     * @param  array<int, int>  $usedIds
     * @return array<int, array{location: Location, from_ai: bool, slot: array<string, mixed>|null}>
     */
    protected function mealOptions(array $route, array $suggested, array $prefs, array $usedIds, int $limit = 12): array
    {
        $options = [];

        foreach ($suggested as $slot) {
            $location = $slot['_loc'] ?? null;
            if ($location instanceof Location) {
                $options[(int) $location->id] = ['location' => $location, 'from_ai' => true, 'slot' => $slot];
            }
        }

        foreach ($this->foodNearRoute($route, $prefs, $usedIds, $limit) as $location) {
            $options[(int) $location->id] ??= ['location' => $location, 'from_ai' => false, 'slot' => null];
        }

        return array_values($options);
    }

    /**
     * Dựng slot bữa ăn từ một ứng viên, giữ nguyên mô tả của AI nếu có.
     *
     * @param  array{location: Location, from_ai: bool, slot: array<string, mixed>|null}  $option
     * @return array<string, mixed>
     */
    protected function mealSlot(array $option, string $kind): array
    {
        if ($option['slot']) {
            $slot = $option['slot'];
            $slot['_loc'] = $option['location'];

            return $slot;
        }

        $activity = $kind === 'dinner'
            ? 'Dùng bữa tối gần chặng cuối trong ngày.'
            : 'Dùng bữa gần điểm vừa tham quan, tiện đường sang chặng tiếp theo.';

        return $this->buildSlot(
            $option['location'],
            '',
            $activity,
            'food',
            'Nên hỏi trước món sẵn có trong ngày.'
        );
    }

    /**
     * Bữa sáng cạnh chỗ nghỉ: chọn quán ít làm lệch hướng ra điểm đầu tiên trong ngày nhất.
     *
     * @param  array<int, array<string, mixed>>  $route
     * @param  array<int, int>  $usedIds
     * @return array<string, mixed>|null
     */
    protected function pickBreakfast(array $route, Location $stay, array $prefs, array $usedIds): ?array
    {
        $first = null;
        foreach ($route as $slot) {
            if (($slot['_loc'] ?? null) instanceof Location) {
                $first = $slot['_loc'];
                break;
            }
        }

        $best = null;
        $bestCost = INF;

        foreach ($this->foodNearRoute([['_loc' => $stay]], $prefs, $usedIds) as $location) {
            $cost = $this->legKm($stay, $location);
            if ($first instanceof Location) {
                $cost += $this->legKm($location, $first) - $this->legKm($stay, $first);
            }

            if ($cost < $bestCost) {
                $bestCost = $cost;
                $best = $location;
            }
        }

        if (!$best) {
            return null;
        }

        return $this->buildSlot(
            $best,
            'Buổi sáng',
            'Ăn sáng gần nơi nghỉ trước khi bắt đầu ngày mới.',
            'food',
            'Nên đi sớm để tránh đông khách.'
        );
    }

    /**
     * Ranh buổi sáng: sau block morning đã gom. Điểm lớn = 1 chỗ; điểm nhỏ = 2 chỗ.
     *
     * @param  array<int, array<string, mixed>>  $route
     */
    protected function morningBoundary(array $route, array $prefs): int
    {
        $limit = $this->mealInsertLimit($route);
        $boundary = 0;

        foreach ($route as $index => $slot) {
            if ($index >= $limit) {
                break;
            }
            if (($slot['_block'] ?? 'morning') !== 'morning') {
                break;
            }
            $boundary = $index + 1;
        }

        if ($boundary > 0) {
            return min($boundary, $limit);
        }

        // Fallback nếu chưa gom block: điểm lớn đứng một mình, điểm nhỏ đi cặp.
        foreach ($route as $index => $slot) {
            if ($index >= $limit) {
                break;
            }
            $location = $slot['_loc'] ?? null;
            if (!$location instanceof Location || $this->isLodging($location)) {
                continue;
            }
            if ($this->isLargeSight($location)) {
                return min($index + 1, $limit);
            }
            $next = $route[$index + 1]['_loc'] ?? null;
            if ($next instanceof Location && !$this->isLodging($next) && !$this->isLargeSight($next)) {
                return min($index + 2, $limit);
            }

            return min($index + 1, $limit);
        }

        return max(1, $limit);
    }

    /**
     * Vị trí chèn xa nhất được phép: luôn đứng trước nơi lưu trú.
     *
     * @param  array<int, array<string, mixed>>  $route
     */
    protected function mealInsertLimit(array $route): int
    {
        return $this->lastLodgingIndex($route) ?? count($route);
    }

    /**
     * Tổng quãng đường của một chuỗi điểm theo đúng thứ tự hiện tại.
     *
     * @param  array<int, array<string, mixed>>  $route
     */
    protected function routeDistanceKm(array $route): float
    {
        $total = 0.0;
        $previous = null;

        foreach ($route as $slot) {
            $location = $slot['_loc'] ?? null;
            if (!$location instanceof Location) {
                continue;
            }
            if ($previous) {
                $total += $this->legKm($previous, $location);
            }
            $previous = $location;
        }

        return $total;
    }

    /**
     * Quãng đường phát sinh thêm khi chèn một quán vào giữa hai chặng liền kề.
     *
     * @param  array<int, array<string, mixed>>  $route
     */
    protected function insertionCost(array $route, Location $food, int $position): float
    {
        $before = $route[$position - 1]['_loc'] ?? null;
        $after = $route[$position]['_loc'] ?? null;

        if (!$before instanceof Location) {
            return INF;
        }

        if (!$after instanceof Location) {
            return $this->legKm($before, $food);
        }

        return $this->legKm($before, $food)
            + $this->legKm($food, $after)
            - $this->legKm($before, $after);
    }

    /**
     * Các quán ăn nằm sát tuyến trong ngày, sắp theo khoảng cách tới chặng gần nhất.
     *
     * @param  array<int, array<string, mixed>>  $route
     * @param  array<int, int>  $usedIds
     * @return array<int, Location>
     */
    protected function foodNearRoute(array $route, array $prefs, array $usedIds, int $limit = 8): array
    {
        $stops = [];
        foreach ($route as $slot) {
            if (($slot['_loc'] ?? null) instanceof Location) {
                $stops[] = $slot['_loc'];
            }
        }

        if (!$stops) {
            return [];
        }

        $vegetarian = $this->wantsVegetarian($prefs);
        $ranked = [];

        foreach ($this->candidates as $location) {
            if (!$this->isFood($location) || in_array((int) $location->id, $usedIds, true)) {
                continue;
            }
            if ($vegetarian && $this->isMeatHeavyName((string) $location->name)) {
                continue;
            }

            $nearest = INF;
            foreach ($stops as $stop) {
                $nearest = min($nearest, $this->legKm($stop, $location));
            }

            $ranked[] = ['location' => $location, 'km' => $nearest];
        }

        usort($ranked, fn ($a, $b) => $a['km'] <=> $b['km']);

        return array_map(fn ($item) => $item['location'], array_slice($ranked, 0, $limit));
    }

    protected function legKm(Location $from, Location $to): float
    {
        if ($from->lat === null || $from->lng === null || $to->lat === null || $to->lng === null) {
            return 0.0;
        }

        return $this->router->haversineKm(
            (float) $from->lat,
            (float) $from->lng,
            (float) $to->lat,
            (float) $to->lng
        );
    }

    /**
     * Gán Buổi sáng / Trưa / Chiều / Tối dựa trên thứ tự cuối cùng và thời lượng từng điểm.
     *
     * @param  array<int, array<string, mixed>>  $slots
     * @return array<int, array<string, mixed>>
     */
    protected function assignPeriods(array $slots, array $prefs): array
    {
        $mealIndexes = [];
        foreach ($slots as $index => $slot) {
            if (($slot['type'] ?? '') === 'food') {
                $mealIndexes[] = $index;
            }
        }

        $breakfast = ($mealIndexes && $mealIndexes[0] === 0) ? 0 : null;
        $rest = $breakfast === null ? $mealIndexes : array_slice($mealIndexes, 1);
        $midday = $rest[0] ?? null;
        $evening = $rest[1] ?? null;

        foreach ($slots as $index => $slot) {
            $location = $slot['_loc'] ?? null;

            if ($location instanceof Location && $this->isLodging($location)) {
                $slots[$index]['time'] = 'Buổi tối';
                continue;
            }

            if ($index === $breakfast) {
                $slots[$index]['time'] = 'Buổi sáng';
                continue;
            }

            if ($index === $midday) {
                $slots[$index]['time'] = 'Buổi trưa';
                continue;
            }

            if ($index === $evening) {
                $slots[$index]['time'] = 'Buổi tối';
                continue;
            }

            if (!$location instanceof Location) {
                $slots[$index]['time'] = 'Linh hoạt';
                continue;
            }

            if ($midday !== null && $index < $midday) {
                $slots[$index]['time'] = 'Buổi sáng';
                continue;
            }

            $block = $slot['_block'] ?? null;
            $slots[$index]['time'] = $block === 'morning' ? 'Buổi sáng' : 'Buổi chiều';
        }

        return $slots;
    }

    /**
     * Tính khoảng cách giữa hai chặng liên tiếp và dọn dữ liệu nội bộ trước khi trả ra frontend.
     *
     * @param  array<int, array<string, mixed>>  $slots
     * @return array<int, array<string, mixed>>
     */
    protected function attachLegDistances(array $slots): array
    {
        $points = [];
        $pointIndexBySlot = [];

        foreach ($slots as $index => $slot) {
            $location = $slot['_loc'] ?? null;
            if ($location instanceof Location && $location->lat !== null && $location->lng !== null) {
                $pointIndexBySlot[$index] = count($points);
                $points[] = ['lat' => $location->lat, 'lng' => $location->lng];
            }
        }

        $distance = count($points) > 1 ? $this->router->buildMatrix($points)['distance'] : [];
        $previous = null;

        foreach ($slots as $index => $slot) {
            unset($slot['distance_from_prev_km']);

            if (isset($pointIndexBySlot[$index])) {
                $current = $pointIndexBySlot[$index];
                if ($previous !== null) {
                    $km = $distance[$previous][$current] ?? 0.0;
                    if ($km >= 0.1) {
                        $slot['distance_from_prev_km'] = round($km, 1);
                    }
                }
                $previous = $current;
            }

            unset($slot['_loc'], $slot['_block']);
            $slots[$index] = $slot;
        }

        return array_values($slots);
    }

    /**
     * @param  array<int, array<string, mixed>>  $days
     * @return array<string, mixed>
     */
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
                $distance += (float) ($slot['distance_from_prev_km'] ?? 0);

                $id = (int) ($slot['location_id'] ?? 0);
                if ($id > 0 && !isset($seen[$id])) {
                    $seen[$id] = true;
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

    /**
     * @param  array<int, array<int, float>>  $compatibility
     * @param  array<int, array{seed: int, label: string, members: array<int, int>, score: float}>  $clusters
     */
    protected function buildPlanningSystemPrompt(array $prefs, array $compatibility, array $clusters): string
    {
        $days = (int) $prefs['days'];
        $paceLabel = match ($prefs['pace'] ?? 'can_bang') {
            'cham_rai' => 'chậm rãi, ít điểm và nhiều thời gian mỗi nơi',
            'dap_dong' => 'dồn dập, nhiều trải nghiệm hơn bình thường',
            default => 'cân bằng',
        };

        $hotelRule = !empty($prefs['need_hotel'])
            ? 'Chuyến đi có nghỉ đêm nên cần chọn một nơi lưu trú, xếp vào cuối ngày.'
            : 'Chuyến đi trong ngày, không chọn nơi lưu trú.';

        $personal = $this->personalizationLines($prefs);
        $catalog = $this->buildCandidateContext($compatibility);
        $clusterText = $this->buildClusterContext($clusters);

        return <<<PROMPT
Bạn là người lập kế hoạch du lịch giàu kinh nghiệm. Chỉ trả về đúng 1 object JSON, không markdown.

Cách làm việc:
- Chọn địa điểm từ danh sách bên dưới và chia vào {$days} ngày.
- Mọi danh mục trong danh sách đều được dùng. Không bỏ qua danh mục lạ chỉ vì khác với chùa, quán ăn hay khách sạn.
- Ưu tiên các địa điểm gần nhau trong cùng một ngày và cùng một buổi.
- Địa điểm lớn (quần thể, khu du lịch) chiếm trọn một buổi sáng hoặc một buổi chiều, không ghép điểm tham quan khác cùng buổi đó.
- Địa điểm nhỏ phải đi cặp 2 nơi gần nhau trong một buổi.
- Ẩm thực: sau một điểm tham quan thì chọn quán gần nhất, hoặc quán nằm tiện đường sang điểm tiếp theo. Không chọn quán ở khu vực khác rồi phải vòng lại.
- Khi sang ngày mới, ưu tiên địa điểm chưa dùng nằm gần chỗ vừa nghỉ. Không nhảy sang cụm xa nếu còn chùa/điểm gần hơn chưa dùng.
- Mỗi ngày, kể cả ngày cuối, phải có buổi sáng và buổi chiều với điểm tham quan. Không để ngày cuối chỉ ăn trưa rồi kết thúc.
- Backend sẽ sắp lại thứ tự di chuyển cuối cùng nên bạn không cần tự tính tuyến tối ưu.
- Nhịp độ mong muốn: {$paceLabel}.
- {$hotelRule}

Ràng buộc dữ liệu:
- Chỉ dùng location_id có trong danh sách; không tự tạo địa điểm hay id mới.
- Mỗi slot loại visit, food, photo bắt buộc có location_id.
- Không bịa giờ mở cửa, giá vé hay thời gian tham quan chính xác.
- estimated_cost giữ đúng chuỗi "{$prefs['budget_label']}".
- time chỉ nhận: Buổi sáng, Buổi trưa, Buổi chiều, Buổi tối, Linh hoạt.
- type chỉ nhận: visit, food, transport, rest, photo.
- Mỗi slot mô tả hoạt động ngắn gọn 1-2 câu.
{$personal}
Schema JSON:
{
  "title": "Tiêu đề chuyến đi",
  "summary": "Tóm tắt 1-2 câu",
  "estimated_cost": "{$prefs['budget_label']}",
  "days": [
    {
      "day": 1,
      "title": "Ngày 1: ...",
      "slots": [
        {"time": "Buổi sáng", "activity": "Mô tả", "location": "Tên trong danh sách", "location_id": 1, "type": "visit", "tip": "Mẹo"}
      ]
    }
  ],
  "tips": ["Lưu ý 1"]
}

{$clusterText}
{$catalog}
PROMPT;
    }

    protected function buildPlanningUserPrompt(array $answers, array $prefs): string
    {
        $lines = [];
        foreach ($answers as $answer) {
            $question = trim((string) ($answer['question'] ?? ''));
            $value = trim((string) ($answer['answer'] ?? ''));
            if ($question !== '' && $value !== '') {
                $lines[] = "- {$question}: {$value}";
            }
        }

        return "Hồ sơ người dùng:\n" . implode("\n", $lines)
            . "\n\nSố ngày: {$prefs['days']}. Ngân sách: {$prefs['budget_label']}."
            . "\nHãy chọn địa điểm phù hợp và chia lịch trình theo hướng dẫn.";
    }

    /** @param  array<int, array<int, float>>  $compatibility */
    protected function buildCandidateContext(array $compatibility): string
    {
        $lines = ['--- DANH SÁCH ĐỊA ĐIỂM ĐƯỢC PHÉP ---'];

        foreach ($this->candidates as $location) {
            $id = (int) $location->id;
            $meta = $this->metadata[$id] ?? [];
            $tags = implode('/', array_slice((array) ($meta['experience_tags'] ?? []), 0, 3)) ?: 'tham quan';
            $partners = $this->topPartners($id, $compatibility);

            $lines[] = sprintf(
                'id=%d | %s | %s | quy mô %s, thời lượng %s | %s | hợp với: %s',
                $id,
                $location->name,
                $location->category->name ?? 'Địa điểm',
                $meta['scale'] ?? 'vừa',
                $meta['estimated_visit'] ?? 'vừa',
                $tags,
                $partners
            );
        }

        $lines[] = '-------------------------------------';

        return implode("\n", $lines);
    }

    /**
     * @param  array<int, array{seed: int, label: string, members: array<int, int>, score: float}>  $clusters
     */
    protected function buildClusterContext(array $clusters): string
    {
        if (!$clusters) {
            return '';
        }

        $lines = ['--- NHÓM ĐỊA ĐIỂM KẾT HỢP TỐT (gợi ý, không bắt buộc) ---'];
        foreach ($clusters as $cluster) {
            $names = [];
            foreach ($cluster['members'] as $memberId) {
                $member = $this->candidates->firstWhere('id', $memberId);
                if ($member) {
                    $names[] = $member->name . ' (id=' . $memberId . ')';
                }
            }
            $lines[] = '- ' . implode(', ', $names);
        }
        $lines[] = '----------------------------------------------------------';

        return implode("\n", $lines);
    }

    /** @param  array<int, array<int, float>>  $compatibility */
    protected function topPartners(int $locationId, array $compatibility, int $limit = 3): string
    {
        $scores = $compatibility[$locationId] ?? [];
        unset($scores[$locationId]);
        arsort($scores);

        $parts = [];
        foreach (array_slice($scores, 0, $limit, true) as $partnerId => $score) {
            $parts[] = 'id=' . $partnerId . ' (' . number_format($score, 2) . ')';
        }

        return $parts ? implode(', ', $parts) : 'không rõ';
    }

    protected function personalizationLines(array $prefs): string
    {
        $lines = [];

        if (!empty($prefs['who'])) {
            $lines[] = "- Đi cùng: {$prefs['who']}.";
        }
        if (!empty($prefs['interests'])) {
            $lines[] = '- Sở thích: ' . implode(', ', $prefs['interests']) . '.';
        }
        if (!empty($prefs['food'])) {
            $lines[] = "- Ẩm thực mong muốn: {$prefs['food']}.";
        }
        if ($this->wantsVegetarian($prefs)) {
            $lines[] = '- Khách ăn chay hoặc thanh đạm: không chọn quán đặc sản thịt.';
        }
        if (!empty($prefs['focus'])) {
            $lines[] = "- Điều khách quan tâm nhất: {$prefs['focus']}.";
        }

        $preferredIds = $prefs['preferred_location_ids'] ?? [];
        if ($preferredIds) {
            $names = $this->candidates
                ->filter(fn (Location $l) => in_array((int) $l->id, $preferredIds, true))
                ->map(fn (Location $l) => $l->name . ' (id=' . $l->id . ')')
                ->all();

            if ($names) {
                $lines[] = '- Khách đã chọn sẵn và phải giữ trong lịch trình: ' . implode(', ', $names) . '.';
            }
        }

        return $lines ? "\nHồ sơ khách:\n" . implode("\n", $lines) . "\n" : '';
    }

    protected function callAI(string $systemPrompt, string $userPrompt, int $maxTokens): ?string
    {
        if (!$this->gemini->isConfigured()) {
            Log::error('TripPlannerService: chưa cấu hình GEMINI_API_KEY.');

            return null;
        }

        return $this->gemini->generate([
            ['role' => 'system', 'content' => $systemPrompt],
            ['role' => 'user', 'content' => $userPrompt],
        ], 0.35, $maxTokens, 60, [
            'json' => true,
            'compact_models' => true,
        ]);
    }

    /**
     * @param  Collection<int, Location>  $catalog
     * @param  array<string, mixed>  $slot
     */
    protected function resolveSlotLocation(array $slot, Collection $catalog): ?Location
    {
        $id = (int) ($slot['location_id'] ?? 0);
        if ($id > 0 && $catalog->has($id)) {
            return $catalog->get($id);
        }

        $name = mb_strtolower(trim((string) ($slot['location'] ?? '')));
        if ($name === '') {
            return null;
        }

        $exact = $catalog->first(fn (Location $l) => mb_strtolower((string) $l->name) === $name);
        if ($exact) {
            return $exact;
        }

        return $catalog->first(function (Location $l) use ($name) {
            $candidate = mb_strtolower((string) $l->name);

            return $candidate !== '' && (str_contains($candidate, $name) || str_contains($name, $candidate));
        });
    }

    /** @return array<string, mixed> */
    protected function buildSlot(Location $location, string $time, string $activity, string $type, string $tip): array
    {
        return [
            'time' => $time !== '' ? $time : 'Linh hoạt',
            'activity' => $activity,
            'location' => $location->name,
            'location_id' => (int) $location->id,
            'type' => $type,
            'tip' => $tip,
            'place' => $this->serializePlace($location),
            '_loc' => $location,
        ];
    }

    /** @param  array<int, int>  $excludeIds */
    protected function pickFoodNear(?Location $anchor, array $prefs, array $excludeIds): ?Location
    {
        $vegetarian = $this->wantsVegetarian($prefs);
        $best = null;
        $bestScore = -INF;

        foreach ($this->candidates as $location) {
            if (!$this->isFood($location) || in_array((int) $location->id, $excludeIds, true)) {
                continue;
            }
            if ($vegetarian && $this->isMeatHeavyName((string) $location->name)) {
                continue;
            }

            $score = 0.0;
            if ($vegetarian && str_contains(mb_strtolower((string) $location->name), 'chay')) {
                $score += 20;
            }

            if ($anchor && $anchor->lat !== null && $anchor->lng !== null && $location->lat !== null && $location->lng !== null) {
                $km = $this->router->haversineKm(
                    (float) $anchor->lat,
                    (float) $anchor->lng,
                    (float) $location->lat,
                    (float) $location->lng
                );
                $score += max(0.0, 25 - $km);
            }

            $score += (float) ($location->average_rating ?? 0);

            if ($score > $bestScore) {
                $bestScore = $score;
                $best = $location;
            }
        }

        return $best;
    }

    /** @param  array<int, array<string, mixed>>  $slots */
    protected function lastLodgingIndex(array $slots): ?int
    {
        for ($i = count($slots) - 1; $i >= 0; $i--) {
            if ($this->isLodging($slots[$i]['_loc'] ?? null)) {
                return $i;
            }
        }

        return null;
    }

    /** @param  array<int, array<string, mixed>>  $slots */
    protected function describeRoute(array $slots): string
    {
        $names = [];
        foreach ($slots as $slot) {
            $names[] = (string) ($slot['location'] ?? 'Không rõ');
        }

        return implode(' → ', $names);
    }

    protected function visitWeight(Location $location): float
    {
        $meta = $this->metadata[(int) $location->id] ?? [];
        $length = (string) ($meta['estimated_visit'] ?? 'vừa');

        return self::VISIT_WEIGHT[$length] ?? self::VISIT_WEIGHT['vừa'];
    }

    protected function isLargeSight(?Location $location): bool
    {
        if (!$location instanceof Location || $this->isFood($location) || $this->isLodging($location)) {
            return false;
        }

        $meta = $this->metadata[(int) $location->id] ?? [];

        return ($meta['scale'] ?? '') === 'lớn'
            || ($meta['estimated_visit'] ?? '') === 'dài'
            || ($meta['can_combine'] ?? true) === false;
    }

    protected function isSmallSight(?Location $location): bool
    {
        if (!$location instanceof Location || $this->isFood($location) || $this->isLodging($location)) {
            return false;
        }

        $meta = $this->metadata[(int) $location->id] ?? [];

        return ($meta['scale'] ?? '') === 'nhỏ' || ($meta['estimated_visit'] ?? '') === 'ngắn';
    }

    /** Hai điểm được coi là gần nếu cách dưới khoảng 8 km đường chim bay. */
    protected function areNearby(?Location $from, ?Location $to, float $maxKm = 8.0): bool
    {
        if (!$from instanceof Location || !$to instanceof Location) {
            return false;
        }

        return $this->legKm($from, $to) <= $maxKm;
    }

    /** @return array<string, int> */
    protected function summarizeScales(): array
    {
        $summary = ['nhỏ' => 0, 'vừa' => 0, 'lớn' => 0, 'ai' => 0, 'heuristic' => 0];

        foreach ($this->metadata as $meta) {
            $scale = (string) ($meta['scale'] ?? 'vừa');
            if (isset($summary[$scale])) {
                $summary[$scale]++;
            }
            $source = (string) ($meta['source'] ?? 'heuristic');
            if (isset($summary[$source])) {
                $summary[$source]++;
            }
        }

        return $summary;
    }

    protected function isFood(?Location $location): bool
    {
        if (!$location instanceof Location) {
            return false;
        }

        $slug = mb_strtolower((string) ($location->category->slug ?? ''));
        $name = mb_strtolower((string) ($location->category->name ?? ''));

        return $slug === 'am-thuc'
            || str_contains($slug, 'am-thuc')
            || str_contains($name, 'ẩm thực')
            || str_contains($name, 'nhà hàng');
    }

    protected function isLodging(?Location $location): bool
    {
        if (!$location instanceof Location) {
            return false;
        }

        $slug = mb_strtolower((string) ($location->category->slug ?? ''));
        $name = mb_strtolower((string) ($location->category->name ?? ''));

        return $slug === 'luu-tru'
            || str_contains($slug, 'luu-tru')
            || str_contains($name, 'lưu trú')
            || str_contains($name, 'khách sạn')
            || str_contains($name, 'homestay')
            || str_contains($name, 'resort');
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
        $normalized = mb_strtolower($name);
        foreach (['thịt dê', 'thit de', 'lẩu dê', 'thịt lợn', 'thịt heo', 'thịt bò', 'nem chua', 'tiết canh'] as $keyword) {
            if (str_contains($normalized, $keyword)) {
                return true;
            }
        }

        return (bool) preg_match('/\bdê\b/u', $normalized);
    }

    /** @return array<string, mixed> */
    protected function serializePlace(Location $location): array
    {
        return [
            'id' => (int) $location->id,
            'name' => $location->name,
            'slug' => $location->slug,
            'lat' => $location->lat !== null ? (float) $location->lat : null,
            'lng' => $location->lng !== null ? (float) $location->lng : null,
            'address' => $location->address,
            'category' => $location->category->name ?? null,
            'category_slug' => $location->category->slug ?? null,
            'image' => $location->resolveThumbnailUrl(),
            'rating' => $location->average_rating !== null ? round((float) $location->average_rating, 1) : null,
            'url' => $location->slug ? route('client.locations.360', $location->slug) : null,
        ];
    }

    /**
     * Lấy đều điểm từ mọi danh mục, không để danh mục mới bị loại vì ít rating.
     *
     * @param  Collection<int, Location>  $pool
     * @param  array<int, string>  $wanted
     * @return Collection<int, Location>
     */
    protected function pickAcrossCategories(Collection $pool, array $wanted, int $limit): Collection
    {
        if ($pool->isEmpty() || $limit < 1) {
            return collect();
        }

        $picked = collect();
        $groups = $pool->groupBy(fn (Location $location) => (string) ($location->category->slug ?: $location->category->name ?? 'khac'));

        foreach ($groups as $slug => $items) {
            $quota = in_array((string) $slug, $wanted, true) ? 4 : 2;
            $picked = $picked->concat($items->take($quota));
        }

        $remaining = $pool->reject(fn (Location $location) => $picked->contains('id', $location->id));

        return $picked->concat($remaining)->unique('id')->take($limit)->values();
    }

    /** Ánh xạ sở thích -> slug danh mục, gồm cả danh mục mới trong DB. */
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
            $key = mb_strtolower(trim((string) $interest));
            if (isset($map[$key])) {
                $slugs = array_merge($slugs, $map[$key]);
            }
        }

        return array_values(array_unique(array_merge($slugs, $this->matchCategorySlugs($interests))));
    }

    /** Ánh xạ kiểu chuyến đi -> slug danh mục ưu tiên, gồm cả danh mục mới trong DB. */
    protected function categorySlugsForTripType(?string $tripType): array
    {
        $type = mb_strtolower((string) $tripType);
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

        $slugs = [];
        foreach ($map as $key => $mapped) {
            if ($type === $key || ($key !== '' && str_contains($type, $key))) {
                $slugs = $mapped;
                break;
            }
        }

        return array_values(array_unique(array_merge($slugs, $this->matchCategorySlugs([$type]))));
    }

    /**
     * Khớp câu trả lời khảo sát với slug/tên mọi danh mục đang có trong DB.
     *
     * @param  array<int, mixed>  $needles
     * @return array<int, string>
     */
    protected function matchCategorySlugs(array $needles): array
    {
        $categories = Category::query()->get(['name', 'slug']);
        $slugs = [];

        foreach ($needles as $needle) {
            $normalized = mb_strtolower(trim(str_replace(['_', '-'], ' ', (string) $needle)));
            if ($normalized === '') {
                continue;
            }

            foreach ($categories as $category) {
                $slug = mb_strtolower((string) $category->slug);
                $name = mb_strtolower((string) $category->name);
                $slugAsWords = str_replace('-', ' ', $slug);

                if (
                    $slug === str_replace(' ', '-', $normalized)
                    || $slugAsWords === $normalized
                    || ($name !== '' && (str_contains($name, $normalized) || str_contains($normalized, $name)))
                    || ($slugAsWords !== '' && (str_contains($slugAsWords, $normalized) || str_contains($normalized, $slugAsWords)))
                ) {
                    $slugs[] = (string) $category->slug;
                }
            }
        }

        return array_values(array_unique($slugs));
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
