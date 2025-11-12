// core/models/alerte.model.ts

export interface Alerte {
  id: number;
  type: 'Stock Minimum' | 'Péremption' | 'Facture Échue' | 'Commande en attente' | 'Autre';
  titre: string;
  message: string;
  severite: 'Faible' | 'Moyenne' | 'Élevée' | 'Critique';
  lue: boolean;
  reference_type?: string;
  reference_id?: number;
  produit_id?: number;
  created_at?: string;
  updated_at?: string;
}
