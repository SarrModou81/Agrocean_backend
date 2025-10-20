<?php

// app/Models/Categorie.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Categorie extends Model
{
    use HasFactory;

    protected $fillable = [
        'nom',
        'description',
        'type_stockage'
    ];

    public function produits()
    {
        return $this->hasMany(Produit::class);
    }
}
