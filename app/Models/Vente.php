<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Vente extends Model
{
    use HasFactory;
    protected $guarded = [];

    public  function user()
    {
        return $this->belongsTo(User::class);
    }
    public  function client()
    {
        return $this->belongsTo(Client::class);
    }
    public  function vente_produits()
    {
        return $this->hasMany(VenteProduit::class, 'vente_id', 'id');
    }
}
