<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LigneIventaire extends Model
{
    use HasFactory;

    public function inventaire()
    {
        return $this->belongsTo(Inventaire::class);
    }

    public function produit()
    {
        return $this->belongsTo(Produit::class);
    }
}
