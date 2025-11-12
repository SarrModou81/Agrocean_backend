// core/models/user.model.ts

export interface User {
  id: number;
  nom: string;
  prenom: string;
  email: string;
  telephone?: string;
  role: UserRole;
  is_active: boolean;
  created_at?: string;
  updated_at?: string;
}

export enum UserRole {
  ADMINISTRATEUR = 'Administrateur',
  COMMERCIAL = 'Commercial',
  GESTIONNAIRE_STOCK = 'GestionnaireStock',
  COMPTABLE = 'Comptable',
  AGENT_APPROVISIONNEMENT = 'AgentApprovisionnement'
}

export interface LoginRequest {
  email: string;
  password: string;
}

export interface LoginResponse {
  token: string;
  user: User;
  expires_in: number;
}

export interface RegisterRequest {
  nom: string;
  prenom: string;
  email: string;
  password: string;
  telephone?: string;
  role: UserRole;
}

export interface AuthUser {
  user: User;
  permissions: any;
  role: string;
}
