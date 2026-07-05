<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use OpenApi\Attributes as OA;

#[OA\Schema(
    schema: "Personne",
    title: "Modèle Personne (Utilisateur)",
    properties: [
        new OA\Property(property: "id", type: "integer", example: 1),
        new OA\Property(property: "nom", type: "string", example: "Dupont"),
        new OA\Property(property: "prenom", type: "string", example: "Jean"),
        new OA\Property(property: "email", type: "string", format: "email", example: "jean.dupont@example.com"),
        new OA\Property(property: "telephone", type: "string", example: "+33612345678"),
        new OA\Property(property: "role", type: "string", enum: ["admin", "client", "bailleur"], example: "client"),
        new OA\Property(property: "is_active", type: "boolean", example: true),
        new OA\Property(property: "email_verified_at", type: "string", format: "date-time", nullable: true)
    ]
)]
class Personne extends Authenticatable implements MustVerifyEmail
{
    use HasApiTokens, HasFactory, Notifiable, SoftDeletes;

    protected $table = 'personnes';

    protected $fillable = [
        'nom',
        'prenom',
        'email',
        'password',
        'telephone',
        'role',
        'is_active',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password'          => 'hashed',
            'is_active'         => 'boolean',
        ];
    }

    public function admin(): HasOne
    {
        return $this->hasOne(Admin::class);
    }

    public function client(): HasOne
    {
        return $this->hasOne(Client::class);
    }

    public function bailleur(): HasOne
    {
        return $this->hasOne(Bailleur::class);
    }

    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    public function isClient(): bool
    {
        return $this->role === 'client';
    }

    public function isBailleur(): bool
    {
        return $this->role === 'bailleur';
    }

    public function nomComplet(): string
    {
        return "{$this->prenom} {$this->nom}";
    }

    public function sendEmailVerificationNotification(): void
    {
        $this->notify(new \App\Notifications\VerifyEmailApi());
    }

    public function sendPasswordResetNotification($token): void
    {
        $url = config('app.frontend_url') . '/reinitialisation?token=' . $token . '&email=' . urlencode($this->email);

        $this->notify(new \App\Notifications\ResetPasswordApi($url));
    }
}
