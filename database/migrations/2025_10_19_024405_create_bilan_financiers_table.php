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
        Schema::create('bilan_financiers', function (Blueprint $table) {
            $table->id();
            $table->string('periode'); // Ex: "Janvier 2024", "Q1 2024"
            $table->date('date_debut');
            $table->date('date_fin');
            $table->decimal('chiffre_affaires', 15, 2)->default(0);
            $table->decimal('charges_exploitation', 15, 2)->default(0);
            $table->decimal('benefice_net', 15, 2)->default(0);
            $table->decimal('marge_globale', 15, 2)->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bilan_financiers');
    }
};
