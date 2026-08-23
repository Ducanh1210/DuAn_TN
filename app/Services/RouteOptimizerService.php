<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

/**
 * Tính ma trận khoảng cách và tối ưu thứ tự di chuyển trong MỘT ngày.
 *
 * Ưu tiên quãng đường bộ từ OSRM (endpoint /table gọi 1 lần cho cả ngày),
 * nếu không cấu hình hoặc lỗi thì lùi về Haversine. Tuyến được chấm điểm theo
 * quãng đường + thời gian + mức vòng vèo, không dùng ngưỡng cứng.
 */
class RouteOptimizerService
{
    /** Số điểm tự do tối đa còn duyệt vét cạn (có cắt nhánh). */
    protected const BRUTE_FORCE_LIMIT = 8;

    /** 1 km tương đương khoảng 20 phút chạy xe khi so sánh 2 tuyến. */
    protected const DURATION_WEIGHT = 0.05;

    /** Hệ số phạt phần đường dư khi đi qua một điểm rồi vòng ngược lại. */
    protected const DETOUR_WEIGHT = 0.25;

    /** @var array<string, array{distance: array<int, array<int, float>>, duration: array<int, array<int, float>>|null, source: string}> */
    protected array $matrixCache = [];

    /** Nguồn dữ liệu của lần tính ma trận gần nhất: osrm hoặc haversine. */
    protected string $lastMatrixSource = 'haversine';

    public function lastMatrixSource(): string
    {
        return $this->lastMatrixSource;
    }

    /** Khoảng cách đường chim bay giữa 2 tọa độ (km). */
    public function haversineKm(float $lat1, float $lng1, float $lat2, float $lng2): float
    {
        $earth = 6371;
        $dLat = deg2rad($lat2 - $lat1);
        $dLng = deg2rad($lng2 - $lng1);
        $a = sin($dLat / 2) ** 2
            + cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * sin($dLng / 2) ** 2;

        return $earth * 2 * atan2(sqrt($a), sqrt(1 - $a));
    }

    /**
     * Ma trận khoảng cách (km) và thời gian (phút) giữa các điểm.
     *
     * @param  array<int, array{lat: float|string|null, lng: float|string|null}>  $points
     * @return array{distance: array<int, array<int, float>>, duration: array<int, array<int, float>>|null, source: string}
     */
    public function buildMatrix(array $points): array
    {
        $points = array_values($points);
        $key = $this->matrixKey($points);

        if (isset($this->matrixCache[$key])) {
            $this->lastMatrixSource = $this->matrixCache[$key]['source'];

            return $this->matrixCache[$key];
        }

        $matrix = $this->fetchRoadMatrix($points) ?? $this->haversineMatrix($points);
        $this->lastMatrixSource = $matrix['source'];

        return $this->matrixCache[$key] = $matrix;
    }

    /**
     * Sắp lại thứ tự các điểm trong ngày sao cho tuyến hợp lý nhất.
     * Không cần điểm xuất phát và không quay về điểm đầu.
     *
     * @param  array<int, array{lat: float|string|null, lng: float|string|null}>  $points
     * @param  int|null  $fixedLastIndex  Chỉ số điểm bắt buộc nằm cuối (thường là khách sạn).
     * @return array{order: array<int, int>, distance_km: float, duration_min: float|null, source: string, original_distance_km: float, method: string}
     */
    public function optimizeOrder(array $points, ?int $fixedLastIndex = null, ?int $fixedFirstIndex = null): array
    {
        $points = array_values($points);
        $count = count($points);
        $identity = range(0, max(0, $count - 1));

        if ($count < 2) {
            return [
                'order' => $count === 0 ? [] : $identity,
                'distance_km' => 0.0,
                'duration_min' => null,
                'source' => $this->lastMatrixSource,
                'original_distance_km' => 0.0,
                'method' => 'skip',
            ];
        }

        $matrix = $this->buildMatrix($points);
        $distance = $matrix['distance'];
        $duration = $matrix['duration'];

        if ($fixedLastIndex !== null && !isset($points[$fixedLastIndex])) {
            $fixedLastIndex = null;
        }
        if ($fixedFirstIndex !== null && (!isset($points[$fixedFirstIndex]) || $fixedFirstIndex === $fixedLastIndex)) {
            $fixedFirstIndex = null;
        }

        $head = $fixedFirstIndex === null ? [] : [$fixedFirstIndex];
        $tail = $fixedLastIndex === null ? [] : [$fixedLastIndex];
        $free = array_values(array_diff($identity, $head, $tail));

        $original = array_merge($head, $free, $tail);

        if (count($free) <= self::BRUTE_FORCE_LIMIT) {
            $best = ['order' => null, 'cost' => INF];
            $this->searchExactOrder(
                $free,
                $head,
                array_fill_keys($head, true),
                0.0,
                $distance,
                $duration,
                $fixedLastIndex,
                count($head),
                $best
            );
            $order = $best['order'] ?? $original;
            $method = 'exhaustive';
        } else {
            $order = $this->improveWithTwoOpt(
                array_merge($head, $this->nearestNeighborOrder($free, $distance, $duration, $head)),
                $distance,
                $duration,
                $fixedLastIndex,
                count($head)
            );
            $order = array_merge($order, $tail);
            $method = 'nearest_neighbor+2opt';
        }

        return [
            'order' => $order,
            'distance_km' => round($this->totalDistance($order, $distance), 2),
            'duration_min' => $duration ? round($this->totalDuration($order, $duration), 1) : null,
            'source' => $matrix['source'],
            'original_distance_km' => round($this->totalDistance($original, $distance), 2),
            'method' => $method,
        ];
    }

    /**
     * Duyệt vét cạn có cắt nhánh: bỏ sớm mọi nhánh đã đắt hơn tuyến tốt nhất hiện có.
     *
     * @param  array<int, int>  $free
     * @param  array<int, int>  $path
     * @param  array<int, bool>  $used
     * @param  array<int, array<int, float>>  $distance
     * @param  array<int, array<int, float>>|null  $duration
     * @param  int  $prefixSize  Số điểm đầu tuyến đã cố định, không được hoán vị.
     * @param  array{order: array<int, int>|null, cost: float}  $best
     */
    protected function searchExactOrder(
        array $free,
        array $path,
        array $used,
        float $cost,
        array $distance,
        ?array $duration,
        ?int $fixedLastIndex,
        int $prefixSize,
        array &$best
    ): void {
        if (count($path) - $prefixSize === count($free)) {
            $finalCost = $cost;
            if ($fixedLastIndex !== null) {
                $finalCost += $this->legCost($path, $fixedLastIndex, $distance, $duration);
                $path[] = $fixedLastIndex;
            }

            if ($finalCost < $best['cost']) {
                $best = ['order' => $path, 'cost' => $finalCost];
            }

            return;
        }

        foreach ($free as $candidate) {
            if (!empty($used[$candidate])) {
                continue;
            }

            $nextCost = $cost + $this->legCost($path, $candidate, $distance, $duration);
            if ($nextCost >= $best['cost']) {
                continue;
            }

            $used[$candidate] = true;
            $path[] = $candidate;

            $this->searchExactOrder($free, $path, $used, $nextCost, $distance, $duration, $fixedLastIndex, $prefixSize, $best);

            array_pop($path);
            unset($used[$candidate]);
        }
    }

    /**
     * Chi phí khi nối thêm một điểm vào cuối tuyến đang dựng.
     *
     * @param  array<int, int>  $path
     * @param  array<int, array<int, float>>  $distance
     * @param  array<int, array<int, float>>|null  $duration
     */
    protected function legCost(array $path, int $next, array $distance, ?array $duration): float
    {
        $size = count($path);
        if ($size === 0) {
            return 0.0;
        }

        $last = $path[$size - 1];
        $cost = $distance[$last][$next] ?? 0.0;

        if ($duration !== null) {
            $cost += self::DURATION_WEIGHT * ($duration[$last][$next] ?? 0.0);
        }

        if ($size >= 2) {
            $beforeLast = $path[$size - 2];
            $cost += self::DETOUR_WEIGHT * $this->detourExcess($beforeLast, $last, $next, $distance);
        }

        return $cost;
    }

    /**
     * Phần đường dư của đoạn a→b→c so với đi thẳng a→c.
     * Giá trị càng lớn nghĩa là tuyến càng vòng vèo / quay ngược.
     *
     * @param  array<int, array<int, float>>  $distance
     */
    protected function detourExcess(int $a, int $b, int $c, array $distance): float
    {
        $excess = ($distance[$a][$b] ?? 0.0) + ($distance[$b][$c] ?? 0.0) - ($distance[$a][$c] ?? 0.0);

        return max(0.0, $excess);
    }

    /**
     * @param  array<int, int>  $free
     * @param  array<int, array<int, float>>  $distance
     * @param  array<int, array<int, float>>|null  $duration
     * @return array<int, int>
     */
    protected function nearestNeighborOrder(array $free, array $distance, ?array $duration, array $head = []): array
    {
        $best = null;
        $bestCost = INF;

        // Khi đã cố định điểm đầu thì chỉ có một tuyến khởi tạo; ngược lại thử mọi điểm làm mốc.
        $starts = $head ? [null] : $free;

        foreach ($starts as $start) {
            $remaining = $start === null ? $free : array_values(array_diff($free, [$start]));
            $route = $start === null ? $head : [$start];

            while ($remaining) {
                $nearest = null;
                $nearestCost = INF;
                foreach ($remaining as $candidate) {
                    $cost = $this->legCost($route, $candidate, $distance, $duration);
                    if ($cost < $nearestCost) {
                        $nearestCost = $cost;
                        $nearest = $candidate;
                    }
                }

                if ($nearest === null) {
                    break;
                }

                $route[] = $nearest;
                $remaining = array_values(array_diff($remaining, [$nearest]));
            }

            $cost = $this->pathCost($route, $distance, $duration);
            if ($cost < $bestCost) {
                $bestCost = $cost;
                $best = array_slice($route, count($head));
            }
        }

        return $best ?: $free;
    }

    /**
     * Cải thiện tuyến bằng 2-opt: đảo từng đoạn con nếu tổng chi phí giảm.
     *
     * @param  array<int, int>  $route
     * @param  array<int, array<int, float>>  $distance
     * @param  array<int, array<int, float>>|null  $duration
     * @return array<int, int>
     */
    protected function improveWithTwoOpt(array $route, array $distance, ?array $duration, ?int $fixedLastIndex, int $prefixSize = 0): array
    {
        $size = count($route);
        if ($size < 4) {
            return $route;
        }

        $tail = $fixedLastIndex !== null ? [$fixedLastIndex] : [];
        $bestCost = $this->pathCost(array_merge($route, $tail), $distance, $duration);
        $improved = true;
        $guard = 0;

        while ($improved && $guard < 40) {
            $improved = false;
            $guard++;

            for ($i = $prefixSize; $i < $size - 1; $i++) {
                for ($j = $i + 1; $j < $size; $j++) {
                    $candidate = $route;
                    $segment = array_slice($candidate, $i, $j - $i + 1);
                    array_splice($candidate, $i, $j - $i + 1, array_reverse($segment));

                    $cost = $this->pathCost(array_merge($candidate, $tail), $distance, $duration);
                    if ($cost + 1e-9 < $bestCost) {
                        $bestCost = $cost;
                        $route = $candidate;
                        $improved = true;
                    }
                }
            }
        }

        return $route;
    }

    /**
     * @param  array<int, int>  $order
     * @param  array<int, array<int, float>>  $distance
     * @param  array<int, array<int, float>>|null  $duration
     */
    protected function pathCost(array $order, array $distance, ?array $duration): float
    {
        $cost = 0.0;
        $path = [];

        foreach ($order as $index) {
            $cost += $this->legCost($path, $index, $distance, $duration);
            $path[] = $index;
        }

        return $cost;
    }

    /**
     * @param  array<int, int>  $order
     * @param  array<int, array<int, float>>  $distance
     */
    protected function totalDistance(array $order, array $distance): float
    {
        $total = 0.0;
        for ($i = 1; $i < count($order); $i++) {
            $total += $distance[$order[$i - 1]][$order[$i]] ?? 0.0;
        }

        return $total;
    }

    /**
     * @param  array<int, int>  $order
     * @param  array<int, array<int, float>>  $duration
     */
    protected function totalDuration(array $order, array $duration): float
    {
        $total = 0.0;
        for ($i = 1; $i < count($order); $i++) {
            $total += $duration[$order[$i - 1]][$order[$i]] ?? 0.0;
        }

        return $total;
    }

    /**
     * @param  array<int, array{lat: float|string|null, lng: float|string|null}>  $points
     * @return array{distance: array<int, array<int, float>>, duration: array<int, array<int, float>>|null, source: string}|null
     */
    protected function fetchRoadMatrix(array $points): ?array
    {
        $baseUrl = $this->roadRoutingBaseUrl();
        if ($baseUrl === null || count($points) > 60) {
            return null;
        }

        $coordinates = [];
        foreach ($points as $point) {
            if (!$this->hasCoordinates($point)) {
                return null;
            }
            $coordinates[] = ((float) $point['lng']) . ',' . ((float) $point['lat']);
        }

        try {
            $response = Http::timeout((int) config('services.osrm.timeout', 6))
                ->get(rtrim($baseUrl, '/') . '/table/v1/driving/' . implode(';', $coordinates), [
                    'annotations' => 'distance,duration',
                ]);

            if (!$response->ok()) {
                Log::debug('RouteOptimizer: OSRM trả lỗi, dùng Haversine.', ['status' => $response->status()]);

                return null;
            }

            $payload = $response->json();
            $distances = $payload['distances'] ?? null;
            $durations = $payload['durations'] ?? null;

            if (!is_array($distances) || count($distances) !== count($points)) {
                return null;
            }

            $distanceMatrix = [];
            foreach ($distances as $i => $row) {
                foreach ((array) $row as $j => $meters) {
                    $distanceMatrix[$i][$j] = is_numeric($meters)
                        ? (float) $meters / 1000
                        : $this->haversineBetween($points[$i], $points[$j]);
                }
            }

            $durationMatrix = null;
            if (is_array($durations) && count($durations) === count($points)) {
                $durationMatrix = [];
                foreach ($durations as $i => $row) {
                    foreach ((array) $row as $j => $seconds) {
                        $durationMatrix[$i][$j] = is_numeric($seconds) ? (float) $seconds / 60 : 0.0;
                    }
                }
            }

            return [
                'distance' => $distanceMatrix,
                'duration' => $durationMatrix,
                'source' => 'osrm',
            ];
        } catch (\Throwable $e) {
            Log::debug('RouteOptimizer: không gọi được OSRM, dùng Haversine.', ['message' => $e->getMessage()]);

            return null;
        }
    }

    /**
     * @param  array<int, array{lat: float|string|null, lng: float|string|null}>  $points
     * @return array{distance: array<int, array<int, float>>, duration: null, source: string}
     */
    protected function haversineMatrix(array $points): array
    {
        $distance = [];
        foreach ($points as $i => $from) {
            foreach ($points as $j => $to) {
                $distance[$i][$j] = $i === $j ? 0.0 : $this->haversineBetween($from, $to);
            }
        }

        return [
            'distance' => $distance,
            'duration' => null,
            'source' => 'haversine',
        ];
    }

    /**
     * @param  array{lat: float|string|null, lng: float|string|null}  $from
     * @param  array{lat: float|string|null, lng: float|string|null}  $to
     */
    protected function haversineBetween(array $from, array $to): float
    {
        if (!$this->hasCoordinates($from) || !$this->hasCoordinates($to)) {
            return 0.0;
        }

        return $this->haversineKm(
            (float) $from['lat'],
            (float) $from['lng'],
            (float) $to['lat'],
            (float) $to['lng']
        );
    }

    /** @param  array<string, mixed>  $point */
    protected function hasCoordinates(array $point): bool
    {
        return isset($point['lat'], $point['lng']) && is_numeric($point['lat']) && is_numeric($point['lng']);
    }

    protected function roadRoutingBaseUrl(): ?string
    {
        $url = trim((string) config('services.osrm.url', ''));

        return $url === '' ? null : $url;
    }

    /** @param  array<int, array{lat: float|string|null, lng: float|string|null}>  $points */
    protected function matrixKey(array $points): string
    {
        $parts = [];
        foreach ($points as $point) {
            $parts[] = $this->hasCoordinates($point)
                ? round((float) $point['lat'], 6) . ':' . round((float) $point['lng'], 6)
                : 'na';
        }

        return md5(implode('|', $parts));
    }
}
