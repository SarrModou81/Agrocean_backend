// core/models/commande-achat.model.ts

export interface CommandeAchat {
  id: number;
  numero: string;
  fournisseur_id: number;
  user_id: number;
  date_commande: string;
  date_livraison_prevue: string;
  statut: 'Brouillon' | 'Validée' | 'Reçue' | 'Annulée';
  montant_total: number;
  motif_annulation?: string;
  date_annulation?: string;
  annule_par?: number;
  date_reception?: string;
  created_at?: string;
  updated_at?: string;
  fournisseur?: any;
  user?: any;
  annule_par_user?: any;
  detail_commande_achats?: DetailCommandeAchat[];
}

export interface DetailCommandeAchat {
  id: number;
  commande_achat_id: number;
  produit_id: number;
  quantite: number;
  prix_unitaire: number;
  sous_total: number;
  created_at?: string;
  updated_at?: string;
  produit?: any;
}
