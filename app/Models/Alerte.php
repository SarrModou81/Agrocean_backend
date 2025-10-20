<?php

// app/Models/Alerte.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Alerte extends Model
{
    use HasFactory;

    protected $fillable = [
        'type',
        'produit_id',
        'message',
        'lue'
    ];

    protected $casts = [
        'lue' => 'boolean',
    ];

    public function produit()
    {
        return $this->belongsTo(Produit::class);
    }

    public function marquerCommeLue()
    {
        $this->lue = true;
        $this->save();
    }
}
