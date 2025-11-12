// core/models/facture.model.ts

export interface Facture {
  id: number;
  vente_id: number;
  numero_facture: string;
  date_emission: string;
  date_echeance: string;
  montant_ht: number;
  montant_ttc: number;
  statut: 'Impayée' | 'Payée partiellement' | 'Payée' | 'Échue';
  created_at?: string;
  updated_at?: string;
  vente?: any; // À typer avec le modèle Vente
}

export interface FactureFournisseur {
  id: number;
  commande_achat_id: number;
  fournisseur_id: number;
  numero_facture: string;
  date_emission: string;
  date_echeance: string;
  montant_ht: number;
  montant_ttc: number;
  statut: 'Impayée' | 'Payée partiellement' | 'Payée' | 'Échue';
  created_at?: string;
  updated_at?: string;
  commande_achat?: any;
  fournisseur?: any;
}
