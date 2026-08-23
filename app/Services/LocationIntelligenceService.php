<?php

namespace App\Services;

use App\Models\Location;
use App\Services\Concerns\ParsesAiJson;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;

/**
 * Hiểu đặc điểm từng địa điểm trước khi lập lịch.
 *
 * Gồm 3 việc: nhờ AI suy luận metadata tương đối (quy mô, thời lượng, trải nghiệm),
 * chấm điểm mức độ hợp nhau giữa hai địa điểm, và gom thành các cụm có thể đi cùng buổi.
 * Metadata chỉ ở mức tương đối phục vụ xếp lịch, không phải dữ liệu xác thực.
 */
class LocationIntelligenceService
{
    use ParsesAiJson;

    protected const SCALES = ['nhỏ', 'vừa', 'lớn'];

    protected const VISIT_LENGTHS = ['ngắn', 'vừa', 'dài'];

    protected const PACES = ['nhẹ', 'vừa', 'nhiều'];

    protected GeminiClient $gemini;

    protected RouteOptimizerService $router;

    public function __construct(?GeminiClient $gemini = null, ?RouteOptimizerService $router = null)
    {
        $this->gemini = $gemini ?? new GeminiClient();
        $this->router = $router ?? new RouteOptimizerService();
    }

    /**
     * Nhờ AI suy luận đặc điểm từng địa điểm; điểm nào AI bỏ sót thì suy ra từ dữ liệu DB.
     *
     * @param  Collection<int, Location>  $locations
     * @return array<int, array<string, mixed>>
     */
    public function analyze(Collection $locations, array $prefs): array
    {
        $metadata = [];
        foreach ($locations as $location) {
            $metadata[(int) $location->id] = $this->heuristicMetadata($location);
        }

        if ($locations->isEmpty() || !$this->gemini->isConfigured()) {
            return $metadata;
        }

        $raw = $this->gemini->generate([
            ['role' => 'system', 'content' => $this->analysisSystemPrompt()],
            ['role' => 'user', 'content' => $this->analysisUserPrompt($locations, $prefs)],
        ], 0.2, 4096, 45, [
            'json' => true,
            'compact_models' => true,
        ]);

        if (!$raw) {
            Log::warning('LocationIntelligence: AI không trả metadata, dùng suy luận từ dữ liệu DB.');

            return $metadata;
        }

        $decoded = $this->decodeAiJson($raw);
        $items = $decoded['locations'] ?? null;
        if (!is_array($items)) {
            Log::warning('LocationIntelligence: metadata AI sai định dạng, dùng suy luận từ dữ liệu DB.');

            return $metadata;
        }

        $aiCount = 0;
        foreach ($items as $item) {
            if (!is_array($item)) {
                continue;
            }

            $id = (int) ($item['location_id'] ?? 0);
            if (!isset($metadata[$id])) {
                continue;
            }

            $metadata[$id] = $this->mergeMetadata($metadata[$id], $item);
            $aiCount++;
        }

        Log::info('LocationIntelligence: đã phân tích địa điểm.', [
            'candidates' => $locations->count(),
            'from_ai' => $aiCount,
            'from_heuristic' => $locations->count() - $aiCount,
        ]);

        return $metadata;
    }

    /**
     * Điểm hợp nhau giữa từng cặp địa điểm (0..1).
     * Khoảng cách 40%, trải nghiệm chung 25%, hợp loại chuyến 15%, hợp nhịp độ 10%, độ nổi bật 10%.
     *
     * @param  Collection<int, Location>  $locations
     * @param  array<int, array<string, mixed>>  $metadata
     * @return array<int, array<int, float>>
     */
    public function compatibilityMatrix(Collection $locations, array $metadata, array $prefs): array
    {
        $list = $locations->values();
        $points = $list->map(fn (Location $l) => ['lat' => $l->lat, 'lng' => $l->lng])->all();
        $distanceMatrix = $this->router->buildMatrix($points)['distance'];

        $tripSlugs = $prefs['trip_type_slugs'] ?? [];
        $scores = [];

        foreach ($list as $i => $from) {
            foreach ($list as $j => $to) {
                $fromId = (int) $from->id;
                $toId = (int) $to->id;

                if ($fromId === $toId) {
                    $scores[$fromId][$toId] = 1.0;
                    continue;
                }

                $km = $distanceMatrix[$i][$j] ?? null;
                $score = 0.40 * $this->distanceScore($km)
                    + 0.25 * $this->experienceOverlap($metadata[$fromId] ?? [], $metadata[$toId] ?? [])
                    + 0.15 * $this->tripTypeScore($from, $to, $tripSlugs)
                    + 0.10 * $this->paceScore($metadata[$fromId] ?? [], $metadata[$toId] ?? [], $prefs)
                    + 0.10 * (($this->prominence($from) + $this->prominence($to)) / 2);

                $scores[$fromId][$toId] = round($score, 4);
            }
        }

        return $scores;
    }

    /**
     * Gom các địa điểm hợp nhau thành cụm ứng viên. Một địa điểm có thể nằm ở nhiều cụm.
     *
     * @param  Collection<int, Location>  $locations
     * @param  array<int, array<int, float>>  $compatibility
     * @param  array<int, array<string, mixed>>  $metadata
     * @return array<int, array{seed: int, label: string, members: array<int, int>, score: float}>
     */
    public function buildClusters(Collection $locations, array $compatibility, array $metadata, array $prefs): array
    {
        $byId = $locations->keyBy('id');
        $seeds = $locations->reject(fn (Location $l) => $this->isService($l))->values();
        if ($seeds->isEmpty()) {
            $seeds = $locations->values();
        }

        $perCluster = max(3, min(7, 2 + (int) ($prefs['slots_per_day'][1] ?? 4)));
        $clusters = [];

        foreach ($seeds as $seed) {
            $seedId = (int) $seed->id;
            $ranked = [];

            foreach ($locations as $candidate) {
                $candidateId = (int) $candidate->id;
                if ($candidateId === $seedId || $this->isService($candidate)) {
                    continue;
                }
                $ranked[$candidateId] = $compatibility[$seedId][$candidateId] ?? 0.0;
            }

            arsort($ranked);
            $members = array_slice(array_keys($ranked), 0, $perCluster - 1, true);
            $members = array_values($members);
            array_unshift($members, $seedId);

            $score = 0.0;
            foreach ($members as $memberId) {
                if ($memberId === $seedId) {
                    $score += $this->prominence($seed);
                    continue;
                }
                $score += ($compatibility[$seedId][$memberId] ?? 0.0)
                    + 0.3 * $this->prominence($byId[$memberId] ?? $seed);
            }

            $clusters[] = [
                'seed' => $seedId,
                'label' => (string) $seed->name,
                'members' => $members,
                'score' => round($score, 3),
            ];
        }

        usort($clusters, fn ($a, $b) => $b['score'] <=> $a['score']);

        return $this->dropOverlappingClusters($clusters, max(2, (int) ($prefs['days'] ?? 2) + 1));
    }

    /**
     * Suy luận đặc điểm từ dữ liệu đang có trong DB khi AI không trả lời.
     *
     * @return array<string, mixed>
     */
    protected function heuristicMetadata(Location $location): array
    {
        $slug = (string) ($location->category->slug ?? '');
        $text = mb_strtolower(trim(
            $location->name . ' ' . $location->short_description . ' ' . $location->description
        ));

        $bigSignals = ['quần thể', 'khu du lịch', 'lớn nhất', 'rộng lớn', 'khu sinh thái', 'resort', 'công viên', 'tam chúc', 'tam chuc'];
        $smallSignals = ['quán', 'cafe', 'cà phê', 'tiệm', 'nhà hàng', 'homestay'];

        $isBig = false;
        foreach ($bigSignals as $signal) {
            if (str_contains($text, $signal)) {
                $isBig = true;
                break;
            }
        }

        $isSmall = false;
        foreach ($smallSignals as $signal) {
            if (str_contains($text, $signal)) {
                $isSmall = true;
                break;
            }
        }

        if ($isBig && !$isSmall) {
            $scale = 'lớn';
        } elseif ($isSmall || in_array($slug, ['am-thuc', 'luu-tru'], true)) {
            // Quán ăn, quán cafe, chỗ nghỉ đều là điểm dừng ngắn.
            $scale = 'nhỏ';
        } elseif (str_contains($text, 'chùa') || str_contains($text, 'đền') || str_contains($text, 'phủ')) {
            // Chùa/đền đơn lẻ mặc định là điểm nhỏ, có thể ghép cặp trong một buổi.
            $scale = 'nhỏ';
        } else {
            // Điểm tham quan mặc định cần một khoảng thời gian vừa phải.
            $scale = 'vừa';
        }

        $visit = match ($scale) {
            'lớn' => 'dài',
            'vừa' => 'vừa',
            default => 'ngắn',
        };

        return [
            'location_id' => (int) $location->id,
            'scale' => $scale,
            'estimated_visit' => $visit,
            'experience_tags' => $this->tagsFromCategory($slug, $text, $location->category->name ?? null),
            'recommended_pace' => $scale === 'lớn' ? 'nhẹ' : 'vừa',
            'can_combine' => $scale !== 'lớn',
            'combine_note' => $scale === 'lớn'
                ? 'Điểm lớn, nên dành phần lớn một buổi.'
                : 'Có thể ghép với các điểm nhỏ gần đó.',
            'suitable_for' => [],
            'source' => 'heuristic',
        ];
    }

    /**
     * @param  array<string, mixed>  $base
     * @param  array<string, mixed>  $fromAi
     * @return array<string, mixed>
     */
    protected function mergeMetadata(array $base, array $fromAi): array
    {
        $scale = $this->normalizeEnum($fromAi['scale'] ?? null, self::SCALES);
        $visit = $this->normalizeEnum($fromAi['estimated_visit'] ?? null, self::VISIT_LENGTHS);
        $pace = $this->normalizeEnum($fromAi['recommended_pace'] ?? null, self::PACES);

        $tags = array_values(array_filter(array_map(
            fn ($tag) => mb_strtolower(trim((string) $tag)),
            (array) ($fromAi['experience_tags'] ?? [])
        )));

        $suitable = array_values(array_filter(array_map(
            fn ($item) => mb_strtolower(trim((string) $item)),
            (array) ($fromAi['suitable_for'] ?? [])
        )));

        return [
            'location_id' => $base['location_id'],
            'scale' => $scale ?? $base['scale'],
            'estimated_visit' => $visit ?? $base['estimated_visit'],
            'experience_tags' => $tags ?: $base['experience_tags'],
            'recommended_pace' => $pace ?? $base['recommended_pace'],
            'can_combine' => array_key_exists('can_combine', $fromAi)
                ? (bool) $fromAi['can_combine']
                : $base['can_combine'],
            'combine_note' => trim((string) ($fromAi['combine_note'] ?? '')) ?: $base['combine_note'],
            'suitable_for' => $suitable ?: $base['suitable_for'],
            'source' => 'ai',
        ];
    }

    /** @param  array<int, string>  $allowed */
    protected function normalizeEnum(mixed $value, array $allowed): ?string
    {
        $text = mb_strtolower(trim((string) $value));
        if ($text === '') {
            return null;
        }

        foreach ($allowed as $option) {
            if ($text === $option || str_contains($text, $option)) {
                return $option;
            }
        }

        return null;
    }

    /** @return array<int, string> */
    protected function tagsFromCategory(string $slug, string $text, ?string $categoryName = null): array
    {
        $tags = match ($slug) {
            'tam-linh' => ['tâm linh', 'văn hóa'],
            'van-hoa-lich-su' => ['văn hóa', 'lịch sử'],
            'sinh-thai' => ['thiên nhiên', 'sinh thái'],
            'check-in' => ['check-in', 'cảnh quan'],
            'am-thuc' => ['ẩm thực'],
            'luu-tru' => ['nghỉ dưỡng'],
            default => [mb_strtolower(trim((string) $categoryName)) ?: 'tham quan'],
        };

        if (str_contains($text, 'kiến trúc')) {
            $tags[] = 'kiến trúc';
        }
        if (str_contains($text, 'làng nghề')) {
            $tags[] = 'làng nghề';
        }

        return array_values(array_unique($tags));
    }

    /** Điểm thuận tiện theo khoảng cách: gần thì cao, xa thì giảm dần chứ không bị cấm. */
    protected function distanceScore(?float $km): float
    {
        if ($km === null) {
            return 0.5;
        }
        if ($km <= 3) {
            return 1.0;
        }
        if ($km <= 8) {
            return 1.0 - 0.2 * (($km - 3) / 5);
        }
        if ($km <= 15) {
            return 0.8 - 0.25 * (($km - 8) / 7);
        }

        return max(0.05, 0.55 - ($km - 15) / 60);
    }

    /**
     * @param  array<string, mixed>  $a
     * @param  array<string, mixed>  $b
     */
    protected function experienceOverlap(array $a, array $b): float
    {
        $tagsA = (array) ($a['experience_tags'] ?? []);
        $tagsB = (array) ($b['experience_tags'] ?? []);
        if (!$tagsA || !$tagsB) {
            return 0.4;
        }

        $shared = count(array_intersect($tagsA, $tagsB));
        $union = count(array_unique(array_merge($tagsA, $tagsB)));

        return $union > 0 ? $shared / $union : 0.0;
    }

    /** @param  array<int, string>  $tripSlugs */
    protected function tripTypeScore(Location $from, Location $to, array $tripSlugs): float
    {
        if (!$tripSlugs) {
            return 0.5;
        }

        $hits = 0;
        foreach ([$from, $to] as $location) {
            if (in_array((string) ($location->category->slug ?? ''), $tripSlugs, true)) {
                $hits++;
            }
        }

        return $hits / 2;
    }

    /**
     * @param  array<string, mixed>  $a
     * @param  array<string, mixed>  $b
     */
    protected function paceScore(array $a, array $b, array $prefs): float
    {
        $pace = (string) ($prefs['pace'] ?? 'can_bang');
        $bigCount = 0;
        foreach ([$a, $b] as $meta) {
            if (($meta['estimated_visit'] ?? 'vừa') === 'dài') {
                $bigCount++;
            }
        }

        return match ($pace) {
            'cham_rai' => $bigCount >= 1 ? 0.9 : 0.6,
            'dap_dong' => $bigCount === 0 ? 1.0 : ($bigCount === 1 ? 0.6 : 0.25),
            default => $bigCount <= 1 ? 0.85 : 0.4,
        };
    }

    /** Độ nổi bật chuẩn hóa 0..1 từ rating và lượt xem. */
    protected function prominence(Location $location): float
    {
        $rating = min(1.0, (float) ($location->average_rating ?? 0) / 5);
        $views = min(1.0, log10(1 + (int) ($location->view_count ?? 0)) / 4);

        return round(0.6 * $rating + 0.4 * $views, 4);
    }

    protected function isService(Location $location): bool
    {
        return in_array((string) ($location->category->slug ?? ''), ['am-thuc', 'luu-tru'], true);
    }

    /**
     * Bỏ bớt các cụm trùng lặp nhiều thành viên để danh sách gợi ý không bị lặp.
     *
     * @param  array<int, array{seed: int, label: string, members: array<int, int>, score: float}>  $clusters
     * @return array<int, array{seed: int, label: string, members: array<int, int>, score: float}>
     */
    protected function dropOverlappingClusters(array $clusters, int $limit): array
    {
        $kept = [];

        foreach ($clusters as $cluster) {
            $isDuplicate = false;
            foreach ($kept as $existing) {
                $shared = count(array_intersect($existing['members'], $cluster['members']));
                if ($shared >= (int) ceil(count($cluster['members']) * 0.7)) {
                    $isDuplicate = true;
                    break;
                }
            }

            if (!$isDuplicate) {
                $kept[] = $cluster;
            }

            if (count($kept) >= $limit) {
                break;
            }
        }

        return $kept;
    }

    protected function analysisSystemPrompt(): string
    {
        return <<<'PROMPT'
Bạn là chuyên gia am hiểu điểm đến du lịch. Với mỗi địa điểm được cung cấp, hãy suy luận đặc điểm tương đối để phục vụ việc xếp lịch.

Chỉ trả về 1 object JSON:
{"locations":[{"location_id":1,"scale":"nhỏ|vừa|lớn","estimated_visit":"ngắn|vừa|dài","experience_tags":["tâm linh"],"recommended_pace":"nhẹ|vừa|nhiều","can_combine":true,"combine_note":"...","suitable_for":["gia đình"]}]}

Nguyên tắc:
- Suy luận từ tên, danh mục, địa chỉ và mô tả đang có. Quần thể lớn như Tam Chúc, khu du lịch rộng thì scale lớn và estimated_visit dài, can_combine false. Chùa nhỏ, đền nhỏ thì scale nhỏ, estimated_visit ngắn và can_combine true.
- combine_note nói ngắn gọn địa điểm này nên đi một mình hay ghép được với điểm nhỏ gần đó.
- Đây là ước lượng tương đối. Tuyệt đối không bịa giờ mở cửa, giá vé, thời gian tham quan chính xác hay sự kiện đang diễn ra.
- Không thêm địa điểm nào ngoài danh sách, không đổi location_id.
PROMPT;
    }

    /** @param  Collection<int, Location>  $locations */
    protected function analysisUserPrompt(Collection $locations, array $prefs): string
    {
        $lines = [];
        foreach ($locations as $location) {
            $description = $this->shorten(
                (string) ($location->short_description ?: $location->description ?: ''),
                150
            );

            $lines[] = implode(' | ', [
                'id=' . $location->id,
                (string) $location->name,
                'loại: ' . ($location->category->name ?? 'Địa điểm'),
                'địa chỉ: ' . $this->shorten((string) $location->address, 70),
                'rating: ' . round((float) ($location->average_rating ?? 0), 1),
                'lượt xem: ' . (int) ($location->view_count ?? 0),
                'mô tả: ' . $description,
            ]);
        }

        $context = 'Chuyến đi: ' . ($prefs['trip_type'] ?: 'chưa xác định')
            . '; sở thích: ' . (implode(', ', $prefs['interests'] ?? []) ?: 'chưa rõ')
            . '; đi cùng: ' . ($prefs['who'] ?: 'chưa rõ') . '.';

        return $context . "\n\nDanh sách địa điểm cần phân tích:\n" . implode("\n", $lines);
    }

    protected function shorten(string $text, int $limit): string
    {
        $text = trim(preg_replace('/\s+/u', ' ', strip_tags($text)));
        if ($text === '') {
            return 'không có';
        }

        return mb_strlen($text) > $limit ? mb_substr($text, 0, $limit - 1) . '…' : $text;
    }
}
