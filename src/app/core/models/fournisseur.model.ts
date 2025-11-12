// core/models/fournisseur.model.ts

export interface Fournisseur {
  id: number;
  nom: string;
  contact?: string;
  telephone: string;
  adresse?: string;
  evaluation?: number;
  conditions?: string;
  created_at?: string;
  updated_at?: string;
}
