/* cspell:disable */
import { Route } from 'react-router-dom';
import type { ComponentType, ReactNode } from 'react';
import DashboardPage from '@/pages/client/DashboardPage';
import MesReservationsPage from '@/pages/client/MesReservationsPage';
import MesFavorisPage from '@/pages/client/MesFavorisPage';
import ProfilPage from '@/pages/client/ProfilPage';
import MesContratsPage from '@/pages/bailleur/MesContratsPage';

type Guard = ComponentType<{ allowed: ('client' | 'bailleur' | 'admin')[]; children: ReactNode }>;

export const clientRoutes = (RoleGuard: Guard) => (
  <>
    <Route path="/espace-client" element={<RoleGuard allowed={['client']}><DashboardPage /></RoleGuard>} />
    <Route path="/espace-client/reservations" element={<RoleGuard allowed={['client']}><MesReservationsPage /></RoleGuard>} />
    <Route path="/espace-client/contrats" element={<RoleGuard allowed={['client']}><MesContratsPage /></RoleGuard>} />
    <Route path="/espace-client/favoris" element={<RoleGuard allowed={['client']}><MesFavorisPage /></RoleGuard>} />
    <Route path="/espace-client/profil" element={<RoleGuard allowed={['client']}><ProfilPage /></RoleGuard>} />
  </>
);