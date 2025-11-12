// core/models/vente.model.ts

import { Produit } from './produit.model';

export interface Client {
  id: number;
  nom: string;
  email?: string;
  telephone?: string;
  adresse?: string;
  type?: string;
  credit_max?: number;
  solde?: number;
  created_at?: string;
  updated_at?: string;
}

export interface Vente {
  id: number;
  numero: string;
  client_id: number;
  client?: Client;
  user_id: number;
  date_vente: string;
  montant_ht: number;
  montant_ttc: number;
  remise?: number;
  statut: StatutVente;
  detail_ventes?: DetailVente[];
  facture?: Facture;
  livraison?: Livraison;
  created_at?: string;
  updated_at?: string;
}

export interface DetailVente {
  id: number;
  vente_id: number;
  produit_id: number;
  produit?: Produit;
  quantite: number;
  prix_unitaire: number;
  sous_total: number;
}

export enum StatutVente {
  BROUILLON = 'Brouillon',
  VALIDEE = 'Validée',
  LIVREE = 'Livrée',
  ANNULEE = 'Annulée'
}

export interface Livraison {
  id: number;
  vente_id: number;
  date_livraison?: string;
  adresse_livraison: string;
  statut: StatutLivraison;
  transporteur?: string;
  numero_suivi?: string;
  created_at?: string;
  updated_at?: string;
}

export enum StatutLivraison {
  PLANIFIEE = 'Planifiée',
  EN_COURS = 'EnCours',
  LIVREE = 'Livrée',
  ANNULEE = 'Annulée'
}

export interface Facture {
  id: number;
  numero: string;
  vente_id?: number;
  date_emission: string;
  date_echeance?: string;
  montant_ttc: number;
  statut: StatutFacture;
  created_at?: string;
  updated_at?: string;
}

export enum StatutFacture {
  EN_ATTENTE = 'EnAttente',
  PAYEE = 'Payée',
  PARTIELLEMENT_PAYEE = 'PartiellementPayée',
  IMPAYEE = 'Impayée',
  ANNULEE = 'Annulée'
}
