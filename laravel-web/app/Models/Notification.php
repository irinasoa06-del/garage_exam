<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Notification extends Model
{
    use HasFactory;

    protected $primaryKey = 'notification_id';
    protected $table = 'notifications';
    
    protected $fillable = [
        'user_id',
        'titre',
        'message',
        'type',
        'lue',
        'firebase_token'
    ];

    protected $casts = [
        'lue' => 'boolean',
        'date_envoi' => 'datetime',
    ];

    // Relation avec l'utilisateur
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'user_id');
    }

    // Scope pour les notifications non lues
    public function scopeNonLues($query)
    {
        return $query->where('lue', false);
    }

    // Scope pour les notifications par type
    public function scopeParType($query, $type)
    {
        return $query->where('type', $type);
    }

    // Marquer comme lue
    public function marquerCommeLue()
    {
        $this->lue = true;
        return $this->save();
    }

    // Getter pour la date formatée
    public function getDateFormateeAttribute()
    {
        return $this->date_envoi->format('d/m/Y H:i');
    }
}