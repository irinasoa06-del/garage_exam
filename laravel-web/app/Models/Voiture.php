<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Voiture extends Model
{
    use HasFactory;

    protected $primaryKey = 'voiture_id';
    public $incrementing = true;
    protected $keyType = 'int';

    protected $fillable = [
        'user_id', 'immatriculation', 'marque', 'modele',
        'annee', 'couleur', 'statut'
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function interventions()
    {
        return $this->hasMany(Intervention::class, 'voiture_id');
    }

    public function reparationsEnCours()
    {
        return $this->hasMany(ReparationEnCours::class, 'voiture_id');
    }

    public function commandes()
    {
        return $this->hasMany(Commande::class, 'voiture_id');
    }
}