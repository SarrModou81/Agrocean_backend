<?php

// app/Models/BilanFinancier.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BilanFinancier extends Model
{
    use HasFactory;

    protected $table = 'bilan_financiers';

    protected $fillable = [
        'periode',
        'date_debut',
        'date_fin',
        'chiffre_affaires',
        'charges_exploitation',
        'benefice_net',
        'marge_globale'
    ];

    protected $casts = [
        'date_debut' => 'date',
        'date_fin' => 'date',
        'chiffre_affaires' => 'decimal:2',
        'charges_exploitation' => 'decimal:2',
        'benefice_net' => 'decimal:2',
        'marge_globale' => 'decimal:2',
    ];
}
