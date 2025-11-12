// core/models/client.model.ts

export interface Client {
  id: number;
  nom: string;
  email?: string;
  telephone: string;
  adresse?: string;
  type: 'Particulier' | 'Entreprise';
  credit_max?: number;
  solde?: number;
  created_at?: string;
  updated_at?: string;
}
