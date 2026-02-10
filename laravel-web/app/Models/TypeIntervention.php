<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TypeIntervention extends Model
{
    use HasFactory;

    protected $table = 'types_intervention';
    protected $primaryKey = 'type_id';
    public $incrementing = true;
    protected $keyType = 'int';

    protected $fillable = [
        'nom', 'duree_secondes', 'prix_unitaire', 'description'
    ];

    public function interventions()
    {
        return $this->hasMany(Intervention::class, 'type_id');
    }

    public function reparationsEnCours()
    {
        return $this->hasMany(ReparationEnCours::class, 'type_id');
    }

    public function commandeDetails()
    {
        return $this->hasMany(CommandeDetail::class, 'type_id');
    }
}