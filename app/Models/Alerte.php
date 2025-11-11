<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Alerte extends Model
{
    protected $fillable = [
        'type',
        'message',
        'produit_id',
        'lue'
    ];

    protected $casts = [
        'lue' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
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
