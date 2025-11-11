<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('commande_achats', function (Blueprint $table) {
            $table->text('motif_annulation')->nullable();
            $table->timestamp('date_annulation')->nullable();
            $table->unsignedBigInteger('annule_par')->nullable();
            $table->timestamp('date_reception')->nullable();

            $table->foreign('annule_par')->references('id')->on('users')->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::table('commande_achats', function (Blueprint $table) {
            $table->dropForeign(['annule_par']);
            $table->dropColumn(['motif_annulation', 'date_annulation', 'annule_par', 'date_reception']);
        });
    }
};
