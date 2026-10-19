<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('marquee_items', function (Blueprint $table) {
            $table->id();
            $table->string('image')->nullable();
            $table->string('nom', 80);
            $table->string('spec', 120)->nullable();
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('marquee_items');
    }
};