<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('knockouts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('raceEventId')
                ->constrained(table: 'race_events', indexName: 'knockouts_race_event_id')
                ->onUpdate('cascade')
                ->onDelete('cascade');
            $table->foreignId('categoryId')
                ->constrained(table: 'categories', indexName: 'knockouts_category_id')
                ->onUpdate('cascade')
                ->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('knockouts');
    }
};
