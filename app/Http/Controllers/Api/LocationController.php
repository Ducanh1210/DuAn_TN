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
        // Đã gỡ bỏ 4 bảng đơn vị hành chính theo yêu cầu hệ thống CSDL 27 bảng chuẩn.
        return;
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
