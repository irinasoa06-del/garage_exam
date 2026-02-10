<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Commande extends Model
{
    use HasFactory;

    protected $primaryKey = 'commande_id';
    protected $table = 'commandes';
    
    protected $fillable = [
        'user_id',
        'voiture_id',
        'montant_total',
        'statut_paiement',
        'mode_paiement',
        'date_paiement',
        'transaction_id'
    ];

    protected $casts = [
        'date_commande' => 'datetime',
        'date_paiement' => 'datetime',
        'montant_total' => 'decimal:2',
    ];

    // Relation avec l'utilisateur
    public function client()
    {
        return $this->belongsTo(User::class, 'user_id', 'user_id');
    }

    // Relation avec la voiture
    public function voiture()
    {
        return $this->belongsTo(Voiture::class, 'voiture_id', 'voiture_id');
    }

    // Relation avec les détails de commande
    public function details()
    {
        return $this->hasMany(CommandeDetail::class, 'commande_id', 'commande_id');
    }

    // Scope pour les commandes payées
    public function scopePayees($query)
    {
        return $query->where('statut_paiement', 'paye');
    }

    // Scope pour les commandes en attente de paiement
    public function scopeEnAttente($query)
    {
        return $query->where('statut_paiement', 'en_attente');
    }

    // Vérifier si la commande est payée
    public function estPayee()
    {
        return $this->statut_paiement === 'paye';
    }
}