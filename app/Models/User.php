<?php

// app/Models/User.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Tymon\JWTAuth\Contracts\JWTSubject;

class User extends Authenticatable implements JWTSubject
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'nom',
        'prenom',
        'email',
        'password',
        'telephone',
        'role',
        'is_active'
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'is_active' => 'boolean',
    ];

    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    public function getJWTCustomClaims()
    {
        return [];
    }

    public function ventes()
    {
        return $this->hasMany(Vente::class);
    }

    public function commandeAchats()
    {
        return $this->hasMany(CommandeAchat::class);
    }

    // ===== MÉTHODES DE VÉRIFICATION DES RÔLES =====

    /**
     * Vérifie si l'utilisateur a un rôle spécifique
     */
    public function hasRole(string $role): bool
    {
        return $this->role === $role;
    }

    /**
     * Vérifie si l'utilisateur a l'un des rôles spécifiés
     */
    public function hasAnyRole(array $roles): bool
    {
        return in_array($this->role, $roles);
    }

    /**
     * Vérifie si l'utilisateur est administrateur
     */
    public function isAdministrateur(): bool
    {
        return $this->role === 'Administrateur';
    }

    /**
     * Vérifie si l'utilisateur est commercial
     */
    public function isCommercial(): bool
    {
        return $this->role === 'Commercial';
    }

    /**
     * Vérifie si l'utilisateur est gestionnaire de stock
     */
    public function isGestionnaireStock(): bool
    {
        return $this->role === 'GestionnaireStock';
    }

    /**
     * Vérifie si l'utilisateur est comptable
     */
    public function isComptable(): bool
    {
        return $this->role === 'Comptable';
    }

    /**
     * Vérifie si l'utilisateur est agent d'approvisionnement
     */
    public function isAgentApprovisionnement(): bool
    {
        return $this->role === 'AgentApprovisionnement';
    }

    /**
     * Obtenir les permissions de l'utilisateur selon son rôle
     */
    public function getPermissions(): array
    {
        $permissions = [
            'Administrateur' => [
                'users' => ['view', 'create', 'update', 'delete', 'manage_roles'],
                'products' => ['view', 'create', 'update', 'delete'],
                'categories' => ['view', 'create', 'update', 'delete'],
                'stocks' => ['view', 'create', 'update', 'delete', 'adjust'],
                'warehouses' => ['view', 'create', 'update', 'delete'],
                'sales' => ['view', 'create', 'update', 'delete', 'validate', 'cancel'],
                'clients' => ['view', 'create', 'update', 'delete'],
                'purchases' => ['view', 'create', 'update', 'delete', 'validate', 'receive', 'cancel'],
                'suppliers' => ['view', 'create', 'update', 'delete', 'evaluate'],
                'deliveries' => ['view', 'create', 'update', 'delete', 'start', 'confirm', 'cancel'],
                'invoices' => ['view', 'create', 'update', 'delete', 'generate_pdf', 'send'],
                'payments' => ['view', 'create', 'update', 'delete'],
                'reports' => ['view', 'export'],
                'financial' => ['view', 'generate'],
                'alerts' => ['view', 'manage']
            ],
            'Commercial' => [
                'products' => ['view'],
                'categories' => ['view'],
                'stocks' => ['view'],
                'sales' => ['view', 'create', 'update', 'validate'],
                'clients' => ['view', 'create', 'update'],
                'deliveries' => ['view', 'create', 'update', 'start', 'confirm'],
                'invoices' => ['view', 'create', 'generate_pdf', 'send'],
                'reports' => ['view'],
                'alerts' => ['view']
            ],
            'GestionnaireStock' => [
                'products' => ['view', 'create', 'update'],
                'categories' => ['view', 'create', 'update'],
                'stocks' => ['view', 'create', 'update', 'adjust'],
                'warehouses' => ['view', 'create', 'update'],
                'sales' => ['view'],
                'purchases' => ['view'],
                'suppliers' => ['view'],
                'reports' => ['view'],
                'alerts' => ['view']
            ],
            'Comptable' => [
                'products' => ['view'],
                'clients' => ['view'],
                'suppliers' => ['view'],
                'sales' => ['view'],
                'purchases' => ['view'],
                'invoices' => ['view', 'create', 'update', 'generate_pdf', 'send'],
                'payments' => ['view', 'create', 'update'],
                'reports' => ['view', 'export'],
                'financial' => ['view', 'generate'],
                'alerts' => ['view']
            ],
            'AgentApprovisionnement' => [
                'products' => ['view'],
                'categories' => ['view'],
                'stocks' => ['view'],
                'warehouses' => ['view'],
                'purchases' => ['view', 'create', 'update', 'validate', 'receive'],
                'suppliers' => ['view', 'create', 'update', 'evaluate'],
                'reports' => ['view'],
                'alerts' => ['view']
            ]
        ];

        return $permissions[$this->role] ?? [];
    }
}
