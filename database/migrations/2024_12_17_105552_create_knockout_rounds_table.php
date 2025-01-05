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
        Schema::create('knockout_rounds', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->integer('order');
            $table->foreignId('knockoutsId')
                ->constrained(table: 'knockouts', indexName: 'knockout_rounds_knockout_id')
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
        Schema::dropIfExists('knockout_rounds');
    }
};
