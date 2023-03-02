<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Produit extends Model
{
    use HasFactory;
    protected $guarded = [];
    public  function famille()
    {
        return $this->belongsTo(Famille::class);
    }
    public function vente_produits()
    {
        return $this->hasMany(VenteProduit::class);
    }
    public function proforma_produits()
    {
        return $this->hasMany(ProformaProduit::class);
    }
    public function depots()
    {
        return $this->hasMany(Depot::class);
    }
    public function ligne_approvisionnements()
    {
        return $this->hasMany(LigneApprovisionnement::class);
    }
     public function ligne_inventaires()
    {
        return $this->hasMany(LigneInventaire::class);
    }
}
