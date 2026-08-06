<?php

namespace Database\Seeders;

use App\Models\Reward;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class RewardSeeder extends Seeder
{
    public function run(): void
    {
        $items = [
            [
                'title' => 'Voucher giảm 50K',
                'description' => 'Voucher ưu đãi cho đối tác ẩm thực tại Ninh Bình',
                'category' => 'voucher',
                'cost_points' => 5000,
                'stock' => 50,
            ],
            [
                'title' => 'Voucher quà lưu niệm',
                'description' => 'Đổi quà lưu niệm tại điểm đối tác',
                'category' => 'voucher',
                'cost_points' => 3000,
                'stock' => 30,
            ],
            [
                'title' => 'Huy hiệu Nhà khám phá',
                'description' => 'Huy hiệu thành tích hiển thị trên trang cá nhân',
                'category' => 'badge',
                'cost_points' => 2500,
                'stock' => null,
            ],
            [
                'title' => 'Khung avatar độc quyền',
                'description' => 'Khung avatar giới hạn từ cửa hàng xu',
                'category' => 'exclusive',
                'cost_points' => 4000,
                'stock' => 20,
            ],
        ];

        foreach ($items as $item) {
            Reward::updateOrCreate(
                ['slug' => Str::slug($item['title'])],
                array_merge($item, [
                    'slug' => Str::slug($item['title']),
                    'status' => 'active',
                ])
            );
        }
    }
}
