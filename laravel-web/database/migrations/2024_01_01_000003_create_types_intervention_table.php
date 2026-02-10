<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('types_intervention', function (Blueprint $table) {
            $table->id('type_id');
            $table->string('nom', 50)->unique();
            $table->integer('duree_secondes');
            $table->decimal('prix_unitaire', 10, 2);
            $table->text('description')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('types_intervention');
    }
};