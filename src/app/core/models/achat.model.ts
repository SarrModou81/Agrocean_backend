// core/models/achat.model.ts

import { Produit } from './produit.model';

export interface Fournisseur {
  id: number;
  nom: string;
  contact?: string;
  telephone?: string;
  adresse?: string;
  evaluation?: number;
  conditions?: string;
  created_at?: string;
  updated_at?: string;
}

export interface CommandeAchat {
  id: number;
  numero: string;
  fournisseur_id: number;
  fournisseur?: Fournisseur;
  user_id: number;
  date_commande: string;
  date_livraison_prevue?: string;
  date_reception?: string;
  statut: StatutCommandeAchat;
  montant_total?: number;
  motif_annulation?: string;
  date_annulation?: string;
  annule_par?: number;
  detail_commande_achats?: DetailCommandeAchat[];
  created_at?: string;
  updated_at?: string;
}

export interface DetailCommandeAchat {
  id: number;
  commande_achat_id: number;
  produit_id: number;
  produit?: Produit;
  quantite: number;
  prix_unitaire: number;
  sous_total: number;
}

export enum StatutCommandeAchat {
  BROUILLON = 'Brouillon',
  VALIDEE = 'Validée',
  RECEPTIONNEE = 'Réceptionnée',
  ANNULEE = 'Annulée'
}

export interface FactureFournisseur {
  id: number;
  numero: string;
  commande_achat_id?: number;
  fournisseur_id: number;
  fournisseur?: Fournisseur;
  date_emission: string;
  date_echeance?: string;
  montant_total: number;
  statut: StatutFactureFournisseur;
  created_at?: string;
  updated_at?: string;
}

export enum StatutFactureFournisseur {
  EN_ATTENTE = 'EnAttente',
  PAYEE = 'Payée',
  PARTIELLEMENT_PAYEE = 'PartiellementPayée',
  IMPAYEE = 'Impayée'
}

export interface Paiement {
  id: number;
  facture_id?: number;
  facture_fournisseur_id?: number;
  client_id?: number;
  fournisseur_id?: number;
  montant: number;
  date_paiement: string;
  mode_paiement: ModePaiement;
  created_at?: string;
  updated_at?: string;
}

export enum ModePaiement {
  ESPECES = 'Espèces',
  CHEQUE = 'Chèque',
  VIREMENT = 'Virement',
  MOBILE_MONEY = 'MobileMoney',
  CARTE_BANCAIRE = 'CarteBancaire'
}
