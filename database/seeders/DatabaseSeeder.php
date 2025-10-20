<?php
// database/seeders/DatabaseSeeder.php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Categorie;
use App\Models\Entrepot;
use App\Models\Client;
use App\Models\Fournisseur;
use App\Models\Produit;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Créer les utilisateurs
        User::create([
            'nom' => 'Admin',
            'prenom' => 'Système',
            'email' => 'admin@agrocean.sn',
            'password' => Hash::make('password'),
            'telephone' => '771234567',
            'role' => 'Administrateur',
            'is_active' => true
        ]);

        User::create([
            'nom' => 'Diop',
            'prenom' => 'Amadou',
            'email' => 'commercial@agrocean.sn',
            'password' => Hash::make('password'),
            'telephone' => '772345678',
            'role' => 'Commercial',
            'is_active' => true
        ]);

        User::create([
            'nom' => 'Ndiaye',
            'prenom' => 'Fatou',
            'email' => 'gestionnaire@agrocean.sn',
            'password' => Hash::make('password'),
            'telephone' => '773456789',
            'role' => 'GestionnaireStock',
            'is_active' => true
        ]);

        User::create([
            'nom' => 'Sarr',
            'prenom' => 'Moussa',
            'email' => 'comptable@agrocean.sn',
            'password' => Hash::make('password'),
            'telephone' => '774567890',
            'role' => 'Comptable',
            'is_active' => true
        ]);

        User::create([
            'nom' => 'Fall',
            'prenom' => 'Aissatou',
            'email' => 'appro@agrocean.sn',
            'password' => Hash::make('password'),
            'telephone' => '775678901',
            'role' => 'AgentApprovisionnement',
            'is_active' => true
        ]);

        // Créer les catégories
        $categories = [
            [
                'nom' => 'Fruits',
                'description' => 'Fruits frais locaux et importés',
                'type_stockage' => 'Frais'
            ],
            [
                'nom' => 'Légumes',
                'description' => 'Légumes de saison',
                'type_stockage' => 'Frais'
            ],
            [
                'nom' => 'Poissons',
                'description' => 'Poissons frais de la mer',
                'type_stockage' => 'Frais'
            ],
            [
                'nom' => 'Crustacés',
                'description' => 'Crevettes, homards, crabes',
                'type_stockage' => 'Frais'
            ],
            [
                'nom' => 'Produits surgelés',
                'description' => 'Poissons et fruits de mer surgelés',
                'type_stockage' => 'Congelé'
            ]
        ];

        foreach ($categories as $cat) {
            Categorie::create($cat);
        }

        // Créer les entrepôts
        Entrepot::create([
            'nom' => 'Entrepôt Principal Dakar',
            'adresse' => 'Zone Industrielle, Dakar',
            'capacite' => 10000,
            'type_froid' => 'Mixte'
        ]);

        Entrepot::create([
            'nom' => 'Entrepôt Frigorifique',
            'adresse' => 'Port de Pêche, Dakar',
            'capacite' => 5000,
            'type_froid' => 'Congelé'
        ]);

        Entrepot::create([
            'nom' => 'Entrepôt Fruits et Légumes',
            'adresse' => 'Marché Thiaroye, Pikine',
            'capacite' => 3000,
            'type_froid' => 'Frais'
        ]);

        // Créer les clients
        $clients = [
            [
                'nom' => 'Auchan Sénégal',
                'email' => 'contact@auchan.sn',
                'telephone' => '338123456',
                'adresse' => 'Sea Plaza, Dakar',
                'type' => 'GrandeSurface',
                'credit_max' => 5000000
            ],
            [
                'nom' => 'Restaurant Le Lagon',
                'email' => 'lelagon@gmail.com',
                'telephone' => '776543210',
                'adresse' => 'Almadies, Dakar',
                'type' => 'Restaurant',
                'credit_max' => 1000000
            ],
            [
                'nom' => 'Boutique Chez Adama',
                'email' => null,
                'telephone' => '779876543',
                'adresse' => 'Parcelles Assainies, Dakar',
                'type' => 'Boutique',
                'credit_max' => 200000
            ],
            [
                'nom' => 'Hôtel Terrou-Bi',
                'email' => 'achats@terrroubi.com',
                'telephone' => '338692929',
                'adresse' => 'Corniche Ouest, Dakar',
                'type' => 'Restaurant',
                'credit_max' => 3000000
            ],
            [
                'nom' => 'Cantine Lycée Kennedy',
                'email' => 'cantine@lyceekennedy.sn',
                'telephone' => '338254545',
                'adresse' => 'Point E, Dakar',
                'type' => 'Institution',
                'credit_max' => 2000000
            ]
        ];

        foreach ($clients as $client) {
            Client::create($client);
        }

        // Créer les fournisseurs
        $fournisseurs = [
            [
                'nom' => 'Coopérative des Pêcheurs de Yoff',
                'contact' => 'Mamadou Diallo',
                'telephone' => '775432109',
                'adresse' => 'Yoff, Dakar',
                'evaluation' => 4.5,
                'conditions' => 'Paiement à 30 jours, livraison quotidienne'
            ],
            [
                'nom' => 'Ferme Bio des Niayes',
                'contact' => 'Aïssatou Sow',
                'telephone' => '776543210',
                'adresse' => 'Niayes, Pikine',
                'evaluation' => 4.8,
                'conditions' => 'Paiement à la livraison, produits bio certifiés'
            ],
            [
                'nom' => 'Import Fruits Tropicaux',
                'contact' => 'Jean-Pierre Martin',
                'telephone' => '338765432',
                'adresse' => 'Zone Franche, Dakar',
                'evaluation' => 4.2,
                'conditions' => 'Paiement anticipé 50%, délai 7 jours'
            ],
            [
                'nom' => 'Mareyage de Soumbédioune',
                'contact' => 'Ibrahima Fall',
                'telephone' => '774321098',
                'adresse' => 'Soumbédioune, Dakar',
                'evaluation' => 4.6,
                'conditions' => 'Paiement comptant, livraison matin'
            ]
        ];

        foreach ($fournisseurs as $fournisseur) {
            Fournisseur::create($fournisseur);
        }

        // Créer les produits
        $produits = [
            // Fruits
            [
                'code' => 'FRT001',
                'nom' => 'Mangue Kent',
                'description' => 'Mangue locale de qualité supérieure',
                'categorie_id' => 1,
                'prix_achat' => 500,
                'prix_vente' => 750,
                'seuil_minimum' => 50,
                'peremption' => true
            ],
            [
                'code' => 'FRT002',
                'nom' => 'Banane',
                'description' => 'Banane douce',
                'categorie_id' => 1,
                'prix_achat' => 300,
                'prix_vente' => 450,
                'seuil_minimum' => 100,
                'peremption' => true
            ],
            [
                'code' => 'FRT003',
                'nom' => 'Orange',
                'description' => 'Orange juteuse',
                'categorie_id' => 1,
                'prix_achat' => 400,
                'prix_vente' => 600,
                'seuil_minimum' => 80,
                'peremption' => true
            ],
            // Légumes
            [
                'code' => 'LEG001',
                'nom' => 'Tomate',
                'description' => 'Tomate fraîche des Niayes',
                'categorie_id' => 2,
                'prix_achat' => 250,
                'prix_vente' => 400,
                'seuil_minimum' => 100,
                'peremption' => true
            ],
            [
                'code' => 'LEG002',
                'nom' => 'Oignon',
                'description' => 'Oignon local',
                'categorie_id' => 2,
                'prix_achat' => 200,
                'prix_vente' => 350,
                'seuil_minimum' => 150,
                'peremption' => true
            ],
            [
                'code' => 'LEG003',
                'nom' => 'Carotte',
                'description' => 'Carotte bio',
                'categorie_id' => 2,
                'prix_achat' => 300,
                'prix_vente' => 500,
                'seuil_minimum' => 80,
                'peremption' => true
            ],
            // Poissons
            [
                'code' => 'POIS001',
                'nom' => 'Thiof',
                'description' => 'Poisson noble sénégalais',
                'categorie_id' => 3,
                'prix_achat' => 3000,
                'prix_vente' => 4500,
                'seuil_minimum' => 20,
                'peremption' => true
            ],
            [
                'code' => 'POIS002',
                'nom' => 'Capitaine',
                'description' => 'Poisson frais de la mer',
                'categorie_id' => 3,
                'prix_achat' => 2000,
                'prix_vente' => 3000,
                'seuil_minimum' => 30,
                'peremption' => true
            ],
            [
                'code' => 'POIS003',
                'nom' => 'Dorade',
                'description' => 'Dorade rose fraîche',
                'categorie_id' => 3,
                'prix_achat' => 1500,
                'prix_vente' => 2500,
                'seuil_minimum' => 40,
                'peremption' => true
            ],
            // Crustacés
            [
                'code' => 'CRUST001',
                'nom' => 'Crevette rose',
                'description' => 'Crevette fraîche calibre moyen',
                'categorie_id' => 4,
                'prix_achat' => 4000,
                'prix_vente' => 6000,
                'seuil_minimum' => 15,
                'peremption' => true
            ],
            [
                'code' => 'CRUST002',
                'nom' => 'Homard',
                'description' => 'Homard vivant',
                'categorie_id' => 4,
                'prix_achat' => 8000,
                'prix_vente' => 12000,
                'seuil_minimum' => 10,
                'peremption' => true
            ],
            // Surgelés
            [
                'code' => 'SURG001',
                'nom' => 'Calamar surgelé',
                'description' => 'Calamar nettoyé surgelé',
                'categorie_id' => 5,
                'prix_achat' => 1800,
                'prix_vente' => 2700,
                'seuil_minimum' => 50,
                'peremption' => false
            ],
            [
                'code' => 'SURG002',
                'nom' => 'Poulpe surgelé',
                'description' => 'Poulpe découpé surgelé',
                'categorie_id' => 5,
                'prix_achat' => 2500,
                'prix_vente' => 3800,
                'seuil_minimum' => 40,
                'peremption' => false
            ]
        ];

        foreach ($produits as $produit) {
            Produit::create($produit);
        }

        echo "✅ Base de données initialisée avec succès!\n";
        echo "📧 Comptes créés:\n";
        echo "   - Admin: admin@agrocean.sn / password\n";
        echo "   - Commercial: commercial@agrocean.sn / password\n";
        echo "   - Gestionnaire: gestionnaire@agrocean.sn / password\n";
        echo "   - Comptable: comptable@agrocean.sn / password\n";
        echo "   - Appro: appro@agrocean.sn / password\n";
    }
}
