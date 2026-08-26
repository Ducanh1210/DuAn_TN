<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;

class LocationController extends Controller
{
    private function ensureTablesExist()
    {
        try {
            // 1. Tạo bảng administrative_regions nếu chưa có
            if (!Schema::hasTable('administrative_regions')) {
                Schema::create('administrative_regions', function (Blueprint $table) {
                    $table->integer('id')->primary();
                    $table->string('name');
                    $table->string('name_en');
                    $table->string('code_name')->nullable();
                    $table->string('code_name_en')->nullable();
                });
            }

            // 2. Tạo bảng administrative_units nếu chưa có
            if (!Schema::hasTable('administrative_units')) {
                Schema::create('administrative_units', function (Blueprint $table) {
                    $table->integer('id')->primary();
                    $table->string('full_name')->nullable();
                    $table->string('full_name_en')->nullable();
                    $table->string('short_name')->nullable();
                    $table->string('short_name_en')->nullable();
                    $table->string('code_name')->nullable();
                    $table->string('code_name_en')->nullable();
                });
            }

            // 3. Tạo bảng provinces nếu chưa có
            if (!Schema::hasTable('provinces')) {
                Schema::create('provinces', function (Blueprint $table) {
                    $table->string('code', 20)->primary();
                    $table->string('name');
                    $table->string('name_en')->nullable();
                    $table->string('full_name');
                    $table->string('full_name_en')->nullable();
                    $table->string('code_name')->nullable();
                    $table->integer('administrative_unit_id')->nullable();
                    
                    $table->foreign('administrative_unit_id')->references('id')->on('administrative_units');
                });
            }

            // 4. Tạo bảng wards nếu chưa có
            if (!Schema::hasTable('wards')) {
                Schema::create('wards', function (Blueprint $table) {
                    $table->string('code', 20)->primary();
                    $table->string('name');
                    $table->string('name_en')->nullable();
                    $table->string('full_name')->nullable();
                    $table->string('full_name_en')->nullable();
                    $table->string('code_name')->nullable();
                    $table->string('province_code', 20)->nullable();
                    $table->integer('administrative_unit_id')->nullable();

                    $table->foreign('administrative_unit_id')->references('id')->on('administrative_units');
                    $table->foreign('province_code')->references('code')->on('provinces');
                });
            }

            // 5. Nạp dữ liệu SQL nếu provinces đang rỗng
            if (Schema::hasTable('provinces') && DB::table('provinces')->count() === 0) {
                $sqlPath = base_path('vietnamese-provinces-database/mysql/mysql_ImportData_vn_units.sql');
                if (!file_exists($sqlPath)) {
                    $sqlPath = 'd:\laragon\www\vietnamese-provinces-database\mysql\mysql_ImportData_vn_units.sql';
                }
                if (file_exists($sqlPath)) {
                    DB::unprepared(file_get_contents($sqlPath));
                }
            }
        } catch (\Throwable $e) {
            \Log::error('Lỗi khởi tạo bảng location: ' . $e->getMessage());
        }
    }

    public function getProvinces()
    {
        $this->ensureTablesExist();

        if (Schema::hasTable('provinces')) {
            $provinces = DB::table('provinces')
                ->select('code', 'name', 'full_name')
                ->orderBy('name', 'asc')
                ->get();
            return response()->json($provinces);
        }

        return response()->json([]);
    }

    public function getWards($provinceCode)
    {
        $this->ensureTablesExist();

        if (Schema::hasTable('wards')) {
            $wards = DB::table('wards')
                ->leftJoin('administrative_units', 'wards.administrative_unit_id', '=', 'administrative_units.id')
                ->where('wards.province_code', $provinceCode)
                ->select(
                    'wards.code',
                    'wards.name',
                    'wards.full_name',
                    'wards.administrative_unit_id',
                    'administrative_units.short_name as unit_type'
                )
                ->orderBy('wards.administrative_unit_id', 'asc')
                ->orderBy('wards.name', 'asc')
                ->get();

            if ($wards->isEmpty()) {
                $paddedCode = str_pad($provinceCode, 2, '0', STR_PAD_LEFT);
                $wards = DB::table('wards')
                    ->leftJoin('administrative_units', 'wards.administrative_unit_id', '=', 'administrative_units.id')
                    ->where('wards.province_code', $paddedCode)
                    ->select(
                        'wards.code',
                        'wards.name',
                        'wards.full_name',
                        'wards.administrative_unit_id',
                        'administrative_units.short_name as unit_type'
                    )
                    ->orderBy('wards.administrative_unit_id', 'asc')
                    ->orderBy('wards.name', 'asc')
                    ->get();
            }

            return response()->json($wards);
        }

        return response()->json([]);
    }
}
