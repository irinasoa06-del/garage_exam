<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('reparations_en_cours', function (Blueprint $table) {
            $table->id('reparation_id');
            $table->foreignId('voiture_id')->constrained('voitures', 'voiture_id')->onDelete('cascade');
            $table->foreignId('type_id')->constrained('types_intervention', 'type_id');
            $table->integer('slot_garage')->nullable();
            $table->timestamp('date_debut')->nullable();
            $table->timestamp('date_fin_estimee')->nullable();
            $table->string('statut')->default('en_attente');
            $table->integer('progression')->default(0);
            $table->foreignId('technicien_id')->nullable()->constrained('users', 'user_id');
            $table->boolean('slot_attente')->default(false);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('reparations_en_cours');
    }
};