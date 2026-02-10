<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('interventions', function (Blueprint $table) {
            $table->id('intervention_id');
            $table->foreignId('voiture_id')->constrained('voitures', 'voiture_id')->onDelete('cascade');
            $table->foreignId('type_id')->constrained('types_intervention', 'type_id');
            $table->text('description_panne');
            $table->string('priorite')->default('moyen');
            $table->timestamp('date_signalement')->useCurrent();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('interventions');
    }
};