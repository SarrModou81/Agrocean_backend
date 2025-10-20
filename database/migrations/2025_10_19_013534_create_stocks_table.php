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
        Schema::create('stocks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('produit_id')->constrained()->onDelete('cascade');
            $table->foreignId('entrepot_id')->constrained()->onDelete('cascade');
            $table->integer('quantite');
            $table->string('emplacement');
            $table->date('date_entree');
            $table->string('numero_lot')->nullable();
            $table->date('date_peremption')->nullable();
            $table->enum('statut', ['Disponible', 'Réservé', 'Périmé', 'Endommagé']);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('stocks');
    }
};
