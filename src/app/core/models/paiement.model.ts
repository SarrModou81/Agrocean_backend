// core/models/paiement.model.ts

export interface Paiement {
  id: number;
  type: 'Client' | 'Fournisseur';
  reference_type?: string;
  reference_id?: number;
  client_id?: number;
  fournisseur_id?: number;
  montant: number;
  mode_paiement: 'Espèces' | 'Chèque' | 'Virement' | 'Carte' | 'Mobile Money';
  date_paiement: string;
  notes?: string;
  created_at?: string;
  updated_at?: string;
  client?: any;
  fournisseur?: any;
}
