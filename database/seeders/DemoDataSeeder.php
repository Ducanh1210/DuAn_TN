<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Category;
use App\Models\Location;
use Illuminate\Support\Str;

class DemoDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Tạo Danh mục Demo
        $category = Category::create([
            'name' => 'Khu Du lịch Tâm linh',
            'slug' => Str::slug('Khu Du lịch Tâm linh'),
            'description' => 'Các khu du lịch kết hợp giữa sinh thái, thiên nhiên và yếu tố tâm linh.',
            'status' => 'active',
            'display_order' => 1,
        ]);

        // Tạo Địa điểm Tam Chúc
        Location::create([
            'category_id' => $category->id,
            'name' => 'Quần thể khu du lịch Tam Chúc',
            'slug' => Str::slug('Quan the khu du lich Tam Chuc') . '-' . time(),
            'short_description' => 'Chùa Tam Chúc là ngôi chùa lớn nhất thế giới, nằm trong quần thể khu du lịch sinh thái ngập nước ở thị trấn Ba Sao, huyện Kim Bảng, tỉnh Hà Nam.',
            'address' => 'Thị trấn Ba Sao, Huyện Kim Bảng, Tỉnh Hà Nam',
            'province' => 'Hà Nam',
            'lat' => 20.55217,
            'lng' => 105.795005,
            'status' => 'published',
            'source' => 'admin',
            // Giả định Admin có id = 1
            'created_by' => 1,
            'updated_by' => 1,
        ]);
    }
}
