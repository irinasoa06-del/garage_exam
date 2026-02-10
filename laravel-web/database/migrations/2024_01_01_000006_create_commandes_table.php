<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('commandes', function (Blueprint $table) {
            $table->id('commande_id');
            $table->foreignId('user_id')->constrained('users', 'user_id');
            $table->foreignId('voiture_id')->constrained('voitures', 'voiture_id');
            $table->timestamp('date_commande')->useCurrent();
            $table->decimal('montant_total', 10, 2);
            $table->string('statut_paiement')->default('en_attente');
            $table->string('mode_paiement')->nullable();
            $table->timestamp('date_paiement')->nullable();
            $table->string('transaction_id')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('commandes');
    }
};