<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Intervention extends Model
{
    use HasFactory;

    protected $primaryKey = 'intervention_id';
    protected $table = 'interventions';
    
    protected $fillable = [
        'voiture_id',
        'type_id',
        'description_panne',
        'priorite'
    ];

    protected $casts = [
        'date_signalement' => 'datetime',
    ];

    // Relation avec la voiture
    public function voiture()
    {
        return $this->belongsTo(Voiture::class, 'voiture_id', 'voiture_id');
    }

    // Relation avec le type d'intervention
    public function type()
    {
        return $this->belongsTo(TypeIntervention::class, 'type_id', 'type_id');
    }

    // Scope pour les interventions par priorité
    public function scopePriorite($query, $priorite)
    {
        return $query->where('priorite', $priorite);
    }

    // Getter pour le prix estimé
    public function getPrixEstimeAttribute()
    {
        return $this->type ? $this->type->prix_unitaire : 0;
    }
}