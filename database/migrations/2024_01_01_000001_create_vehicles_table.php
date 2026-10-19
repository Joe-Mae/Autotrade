<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('vehicles', function (Blueprint $table) {
            $table->id();
            $table->string('slug')->unique();
            $table->string('nom');
            $table->string('marque');
            $table->string('categorie'); // Super Sport, Luxe & Confort, Électrique, SUV
            $table->unsignedSmallInteger('annee');
            $table->string('carburant');
            $table->string('boite');
            $table->string('puissance');
            $table->string('km');
            $table->unsignedTinyInteger('places')->default(2);
            $table->string('image');
            $table->json('galerie')->nullable();
            $table->unsignedInteger('prix_vente')->nullable();
            $table->boolean('disponible')->default(true);
            $table->string('badge')->nullable();
            $table->text('description')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('vehicles');
    }
};
