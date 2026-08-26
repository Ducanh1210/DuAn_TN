<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1. Create administrative_regions table
        if (!Schema::hasTable('administrative_regions')) {
            Schema::create('administrative_regions', function (Blueprint $table) {
                $table->integer('id')->primary();
                $table->string('name');
                $table->string('name_en');
                $table->string('code_name')->nullable();
                $table->string('code_name_en')->nullable();
            });
        }

        // 2. Create administrative_units table
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

        // 3. Create provinces table
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
                $table->index('administrative_unit_id');
            });
        }

        // 4. Create wards table
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

                $table->index('province_code');
                $table->index('administrative_unit_id');
            });
        }

        // 5. Populate Data from SQL file
        $sqlPath = 'd:\laragon\www\vietnamese-provinces-database\mysql\mysql_ImportData_vn_units.sql';
        if (file_exists($sqlPath) && DB::table('provinces')->count() === 0) {
            $sql = file_get_contents($sqlPath);
            DB::unprepared($sql);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('wards');
        Schema::dropIfExists('provinces');
        Schema::dropIfExists('administrative_units');
        Schema::dropIfExists('administrative_regions');
    }
};
