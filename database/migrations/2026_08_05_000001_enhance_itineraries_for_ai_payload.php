<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('itineraries', function (Blueprint $table) {
            if (!Schema::hasColumn('itineraries', 'summary')) {
                $table->text('summary')->nullable()->after('description');
            }
            if (!Schema::hasColumn('itineraries', 'answers')) {
                $table->json('answers')->nullable()->after('payload');
            }
        });

        Schema::table('itinerary_items', function (Blueprint $table) {
            if (!Schema::hasColumn('itinerary_items', 'tip')) {
                $table->text('tip')->nullable()->after('note');
            }
        });
    }

    public function down(): void
    {
        Schema::table('itinerary_items', function (Blueprint $table) {
            if (Schema::hasColumn('itinerary_items', 'tip')) {
                $table->dropColumn('tip');
            }
        });

        Schema::table('itineraries', function (Blueprint $table) {
            $drop = [];
            if (Schema::hasColumn('itineraries', 'summary')) {
                $drop[] = 'summary';
            }
            if (Schema::hasColumn('itineraries', 'answers')) {
                $drop[] = 'answers';
            }
            if ($drop) {
                $table->dropColumn($drop);
            }
        });
    }
};
