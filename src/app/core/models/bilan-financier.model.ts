// core/models/bilan-financier.model.ts

export interface BilanFinancier {
  id: number;
  periode_debut: string;
  periode_fin: string;
  chiffre_affaires: number;
  cout_achats: number;
  charges_exploitation: number;
  resultat_exploitation: number;
  resultat_net: number;
  tresorerie_debut: number;
  tresorerie_fin: number;
  created_at?: string;
  updated_at?: string;
}

export interface EtatTresorerie {
  tresorerie_actuelle: number;
  encaissements_prevus: number;
  decaissements_prevus: number;
  tresorerie_previsionnelle: number;
  factures_impayees: number;
  factures_fournisseurs_impayees: number;
}

export interface CompteResultat {
  periode_debut: string;
  periode_fin: string;
  chiffre_affaires: number;
  cout_achats: number;
  marge_brute: number;
  charges_exploitation: number;
  resultat_exploitation: number;
  resultat_net: number;
}

export interface DashboardFinancier {
  chiffre_affaires_mois: number;
  evolution_ca: number;
  depenses_mois: number;
  evolution_depenses: number;
  marge_brute: number;
  evolution_marge: number;
  tresorerie: number;
  evolution_tresorerie: number;
  factures_impayees: number;
  factures_echues: number;
  top_clients: any[];
  evolution_mensuelle: any[];
}
