<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CommandeDetail extends Model
{
    use HasFactory;

    protected $primaryKey = 'detail_id';
    protected $table = 'commande_details';
    
    protected $fillable = [
        'commande_id',
        'reparation_id',
        'type_id',
        'quantite',
        'prix_unitaire',
        'sous_total'
    ];

    protected $casts = [
        'quantite' => 'integer',
        'prix_unitaire' => 'decimal:2',
        'sous_total' => 'decimal:2',
    ];

    // Relation avec la commande
    public function commande()
    {
        return $this->belongsTo(Commande::class, 'commande_id', 'commande_id');
    }

    // Relation avec la réparation
    public function reparation()
    {
        return $this->belongsTo(Reparation::class, 'reparation_id', 'reparation_id');
    }

    // Relation avec le type d'intervention
    public function type()
    {
        return $this->belongsTo(TypeIntervention::class, 'type_id', 'type_id');
    }

    // Calculer le sous-total automatiquement
    public static function boot()
    {
        parent::boot();

        static::saving(function ($model) {
            $model->sous_total = $model->prix_unitaire * $model->quantite;
        });
    }
}