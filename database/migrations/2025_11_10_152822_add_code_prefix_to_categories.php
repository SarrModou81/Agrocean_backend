<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('categories', function (Blueprint $table) {
            $table->string('code_prefix', 10)->nullable()->after('nom');
        });

        // Mettre à jour les catégories existantes
        DB::table('categories')->update([
            'code_prefix' => DB::raw("UPPER(LEFT(nom, 3))")
        ]);

        Schema::table('categories', function (Blueprint $table) {
            $table->string('code_prefix', 10)->nullable(false)->change();
        });
    }

    public function down(): void
    {
        Schema::table('categories', function (Blueprint $table) {
            $table->dropColumn('code_prefix');
        });
    }
};
