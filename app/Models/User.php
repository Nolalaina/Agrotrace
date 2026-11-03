<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    public const ROLE_ADMIN = 'admin';
    public const ROLE_RESPONSABLE = 'responsable';
    public const ROLE_TECHNICIEN = 'technicien';
    public const ROLES = [self::ROLE_ADMIN, self::ROLE_RESPONSABLE, self::ROLE_TECHNICIEN];

    // Mass assignment : ne jamais faire User::create($request->all()). Les contrôleurs
    // passent uniquement des données validées et fixent role / exploitation_id eux-mêmes.
    protected $fillable = ['name', 'email', 'password', 'exploitation_id', 'role'];

    protected $hidden = ['password', 'remember_token'];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function exploitation(): BelongsTo
    {
        return $this->belongsTo(Exploitation::class);
    }

    public function isAdmin(): bool
    {
        return $this->role === self::ROLE_ADMIN;
    }

    public function isTechnicien(): bool
    {
        return $this->role === self::ROLE_TECHNICIEN;
    }

    /** Admin ou responsable : peut gérer parcelles, campagnes, stocks, lots et finances. */
    public function peutGerer(): bool
    {
        return in_array($this->role, [self::ROLE_ADMIN, self::ROLE_RESPONSABLE], true);
    }
}
