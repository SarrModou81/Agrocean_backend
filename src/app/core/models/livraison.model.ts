// core/models/livraison.model.ts

export interface Livraison {
  id: number;
  vente_id: number;
  date_livraison: string;
  adresse_livraison: string;
  statut: 'En attente' | 'En cours' | 'Livrée' | 'Annulée';
  livreur?: string;
  notes?: string;
  created_at?: string;
  updated_at?: string;
  vente?: any; // À typer avec le modèle Vente
}
