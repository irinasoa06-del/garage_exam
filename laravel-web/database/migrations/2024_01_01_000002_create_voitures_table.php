<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('voitures', function (Blueprint $table) {
            $table->id('voiture_id');
            $table->foreignId('user_id')->constrained('users', 'user_id')->onDelete('cascade');
            $table->string('immatriculation', 20)->unique();
            $table->string('marque', 50);
            $table->string('modele', 50);
            $table->integer('annee')->nullable();
            $table->string('couleur', 30)->nullable();
            $table->timestamp('date_ajout')->useCurrent();
            $table->string('statut')->default('en_attente');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('voitures');
    }
};