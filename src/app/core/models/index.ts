// core/models/index.ts

export * from './user.model';
export * from './produit.model';
export * from './vente.model';
export * from './achat.model';
export * from './client.model';
export * from './fournisseur.model';
export * from './commande-achat.model';
export * from './livraison.model';
export * from './facture.model';
export * from './paiement.model';
export * from './stock.model';
export * from './alerte.model';
export * from './bilan-financier.model';

// Interfaces communes
export interface ApiResponse<T> {
  success: boolean;
  data?: T;
  message?: string;
  errors?: any;
}

export interface PaginatedResponse<T> {
  current_page: number;
  data: T[];
  first_page_url: string;
  from: number;
  last_page: number;
  last_page_url: string;
  next_page_url: string | null;
  path: string;
  per_page: number;
  prev_page_url: string | null;
  to: number;
  total: number;
}
