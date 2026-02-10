<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $primaryKey = 'user_id';
    public $incrementing = true;
    protected $keyType = 'int';

    protected $fillable = [
        'nom', 'prenom', 'email', 'mot_de_passe_hash', 
        'telephone', 'adresse', 'role', 'firebase_uid'
    ];

    protected $hidden = [
        'mot_de_passe_hash', 'remember_token',
    ];

    /**
     * Get the password for authentication.
     * Laravel expects 'password' but we use 'mot_de_passe_hash'
     */
    public function getAuthPassword()
    {
        return $this->mot_de_passe_hash;
    }

    public function voitures()
    {
        return $this->hasMany(Voiture::class, 'user_id');
    }

    public function commandes()
    {
        return $this->hasMany(Commande::class, 'user_id');
    }

    public function notifications()
    {
        return $this->hasMany(Notification::class, 'user_id');
    }

    public function reparationsEnCours()
    {
        return $this->hasMany(ReparationEnCours::class, 'technicien_id');
    }
}