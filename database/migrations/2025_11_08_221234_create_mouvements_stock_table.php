<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('mouvements_stock', function (Blueprint $table) {
            $table->id();
            $table->enum('type', ['Entrée', 'Sortie', 'Ajustement']);
            $table->foreignId('stock_id')->nullable()->constrained()->onDelete('cascade');
            $table->foreignId('produit_id')->constrained()->onDelete('cascade');
            $table->foreignId('entrepot_id')->constrained()->onDelete('cascade');
            $table->integer('quantite'); // Positif ou négatif
            $table->string('numero_lot')->nullable();
            $table->text('motif')->nullable();
            $table->string('reference_type')->nullable(); // Vente, CommandeAchat, Ajustement
            $table->unsignedBigInteger('reference_id')->nullable(); // ID de la référence
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('set null');
            $table->timestamp('date');
            $table->timestamps();

            $table->index(['produit_id', 'entrepot_id', 'date']);
            $table->index(['reference_type', 'reference_id']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('mouvements_stock');
    }
};
