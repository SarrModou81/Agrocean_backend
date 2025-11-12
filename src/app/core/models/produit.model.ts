// core/models/produit.model.ts

export interface Produit {
  id: number;
  code: string;
  reference: string;
  nom: string;
  description?: string;
  categorie_id: number;
  categorie?: Categorie;
  prix_achat: number;
  prix_vente: number;
  seuil_minimum: number;
  peremption: boolean;
  created_at?: string;
  updated_at?: string;
}

export interface Categorie {
  id: number;
  nom: string;
  description?: string;
  code_prefix: string;
  type_stockage: TypeStockage;
  created_at?: string;
  updated_at?: string;
}

export enum TypeStockage {
  SEC = 'Sec',
  FRAIS = 'Frais',
  CONGELE = 'Congelé',
  AMBIANT = 'Ambiant'
}

export interface Stock {
  id: number;
  produit_id: number;
  produit?: Produit;
  entrepot_id: number;
  entrepot?: Entrepot;
  quantite: number;
  emplacement?: string;
  date_entree: string;
  numero_lot?: string;
  date_peremption?: string;
  statut: StatutStock;
  created_at?: string;
  updated_at?: string;
}

export interface Entrepot {
  id: number;
  nom: string;
  adresse?: string;
  capacite?: number;
  type_froid?: boolean;
  created_at?: string;
  updated_at?: string;
}

export enum StatutStock {
  DISPONIBLE = 'Disponible',
  RESERVE = 'Réservé',
  PERIME = 'Périmé',
  ENDOMMAGE = 'Endommagé'
}

export interface MouvementStock {
  id: number;
  type: TypeMouvement;
  stock_id?: number;
  produit_id: number;
  produit?: Produit;
  entrepot_id: number;
  entrepot?: Entrepot;
  quantite: number;
  numero_lot?: string;
  motif?: string;
  reference_type?: string;
  reference_id?: number;
  user_id?: number;
  date: string;
  created_at?: string;
}

export enum TypeMouvement {
  ENTREE = 'Entrée',
  SORTIE = 'Sortie',
  AJUSTEMENT = 'Ajustement'
}
