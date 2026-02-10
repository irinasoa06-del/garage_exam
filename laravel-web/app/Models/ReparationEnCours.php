<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ReparationEnCours extends Model
{
    use HasFactory;

    protected $table = 'reparations_en_cours';
    protected $primaryKey = 'reparation_id';
    public $incrementing = true;
    protected $keyType = 'int';

    protected $fillable = [
        'voiture_id', 'type_id', 'slot_garage', 'date_debut',
        'date_fin_estimee', 'statut', 'progression', 'technicien_id', 'slot_attente'
    ];

    protected $casts = [
        'date_debut' => 'datetime',
        'date_fin_estimee' => 'datetime',
    ];

    public function voiture()
    {
        return $this->belongsTo(Voiture::class, 'voiture_id');
    }

    public function type()
    {
        return $this->belongsTo(TypeIntervention::class, 'type_id');
    }

    public function technicien()
    {
        return $this->belongsTo(User::class, 'technicien_id');
    }

    public function commandeDetails()
    {
        return $this->hasMany(CommandeDetail::class, 'reparation_id');
    }
}