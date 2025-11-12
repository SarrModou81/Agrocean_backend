// core/models/stock.model.ts

export interface Stock {
  id: number;
  produit_id: number;
  entrepot_id: number;
  quantite: number;
  emplacement?: string;
  date_entree: string;
  numero_lot?: string;
  date_peremption?: string;
  statut: 'Disponible' | 'Réservé' | 'Périmé' | 'Endommagé';
  created_at?: string;
  updated_at?: string;
  produit?: any;
  entrepot?: any;
}

export interface MouvementStock {
  id: number;
  type: 'Entrée' | 'Sortie' | 'Ajustement';
  stock_id?: number;
  produit_id: number;
  entrepot_id: number;
  quantite: number;
  numero_lot?: string;
  motif?: string;
  reference_type?: string;
  reference_id?: number;
  user_id?: number;
  date: string;
  created_at?: string;
  produit?: any;
  entrepot?: any;
  user?: any;
}
