<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id('user_id');
            $table->string('nom', 100);
            $table->string('prenom', 100);
            $table->string('email')->unique();
            $table->string('mot_de_passe_hash');
            $table->string('telephone', 20)->nullable();
            $table->text('adresse')->nullable();
            $table->timestamp('date_inscription')->useCurrent();
            $table->string('role')->default('client');
            $table->string('firebase_uid')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};