<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Inventaire extends Model
{
    use HasFactory;

     public  function user()
    {
        return $this->belongsTo(User::class);
    }
    
    public function ligne_inventaires()
    {
        return $this->hasMany(LigneInventaire::class, 'inventaire_id', 'id')->orderBy('id');
    }
}
