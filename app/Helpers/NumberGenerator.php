<?php

namespace App\Helpers;

use Illuminate\Support\Facades\DB;

class NumberGenerator
{
    /**
     * Génère un numéro unique pour une entité avec protection contre les race conditions
     *
     * @param string $modelClass Le nom complet de la classe du modèle (ex: \App\Models\Vente::class)
     * @param string $prefix Le préfixe du numéro (ex: 'V', 'CA', 'F')
     * @param string $column Le nom de la colonne contenant le numéro (par défaut 'numero')
     * @param int $padding Le nombre de zéros à gauche (par défaut 6)
     * @return string Le numéro généré
     */
    public static function generate(
        string $modelClass,
        string $prefix,
        string $column = 'numero',
        int $padding = 6
    ): string {
        return DB::transaction(function () use ($modelClass, $prefix, $column, $padding) {
            // Récupérer le dernier numéro existant avec verrou FOR UPDATE
            $lastRecord = $modelClass::lockForUpdate()
                ->where($column, 'LIKE', $prefix . date('Y') . '%')
                ->orderBy($column, 'desc')
                ->first();

            if ($lastRecord) {
                // Extraire le numéro séquentiel du dernier enregistrement
                $lastNumber = (int) substr($lastRecord->$column, -$padding);
                $newNumber = $lastNumber + 1;
            } else {
                // Premier enregistrement de l'année
                $newNumber = 1;
            }

            return $prefix . date('Y') . str_pad($newNumber, $padding, '0', STR_PAD_LEFT);
        });
    }

    /**
     * Génère un numéro de vente (V + année + numéro séquentiel)
     * Exemple: V2025000001
     */
    public static function generateVenteNumber(): string
    {
        return self::generate(\App\Models\Vente::class, 'V');
    }

    /**
     * Génère un numéro de commande d'achat (CA + année + numéro séquentiel)
     * Exemple: CA2025000001
     */
    public static function generateCommandeAchatNumber(): string
    {
        return self::generate(\App\Models\CommandeAchat::class, 'CA');
    }

    /**
     * Génère un numéro de facture (F + année + numéro séquentiel)
     * Exemple: F2025000001
     */
    public static function generateFactureNumber(): string
    {
        return self::generate(\App\Models\Facture::class, 'F');
    }

    /**
     * Génère un numéro de facture fournisseur (FF + année + numéro séquentiel)
     * Exemple: FF2025000001
     */
    public static function generateFactureFournisseurNumber(): string
    {
        return self::generate(\App\Models\FactureFournisseur::class, 'FF');
    }
}
