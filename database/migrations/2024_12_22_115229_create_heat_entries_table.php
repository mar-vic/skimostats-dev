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
        Schema::create('heat_entries', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->foreignId('heatId')
                ->constrained(table: 'heats', indexName: 'heat_entries_heat_id')
                ->onUpdate('cascade')
                ->onDelete('cascade');
            $table->string('athleteName')->nullable();
            $table->string('nationality')->nullable();
            $table->string('timeRaw')->nullable();
            $table->integer('time')->nullable();
            $table->integer('rank')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('heat_entries');
    }
};
