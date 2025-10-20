<?php

// app/Models/Entrepot.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Entrepot extends Model
{
    use HasFactory;

    protected $fillable = [
        'nom',
        'adresse',
        'capacite',
        'type_froid'
    ];

    public function stocks()
    {
        return $this->hasMany(Stock::class);
    }

    public function verifierCapacite()
    {
        $utilisation = $this->stocks()->where('statut', 'Disponible')->sum('quantite');
        return $this->capacite - $utilisation;
    }
}
