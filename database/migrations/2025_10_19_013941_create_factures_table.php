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
        Schema::create('factures', function (Blueprint $table) {
            $table->id();
            $table->string('numero')->unique();
            $table->foreignId('vente_id')->constrained()->onDelete('cascade');
            $table->date('date_emission');
            $table->date('date_echeance');
            $table->decimal('montant_ttc', 12, 2);
            $table->enum('statut', ['Impayée', 'Partiellement Payée', 'Payée', 'Annulée']);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('factures');
    }
};
