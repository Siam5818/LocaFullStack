/* cspell:disable */

import { Route } from 'react-router-dom';
import type { ComponentType, ReactNode } from 'react';
import MesProprietesPage from '@/pages/bailleur/MesProprietesPage';
import RevenuesPage from '@/pages/bailleur/RevenuesPage';
import MesContratsPage from '@/pages/bailleur/MesContratsPage';
import BailleurPaiementsPage from '@/pages/bailleur/PaiementsContratPage';

type Guard = ComponentType<{ allowed: ('client' | 'bailleur' | 'admin')[]; children: ReactNode }>;

export const bailleurRoutes = (RoleGuard: Guard) => (
  <>
    <Route path="/bailleur/proprietes" element={<RoleGuard allowed={['bailleur']}><MesProprietesPage /></RoleGuard>} />
    <Route
      path="/bailleur/contrats"
      element={<RoleGuard allowed={['bailleur']}><MesContratsPage /></RoleGuard>}
    />
    <Route
      path="/bailleur/contrats/:contratId/paiements"
      element={<RoleGuard allowed={['bailleur']}><BailleurPaiementsPage /></RoleGuard>}
    />
    <Route path="/bailleur/revenus" element={<RoleGuard allowed={['bailleur']}><RevenuesPage /></RoleGuard>} />
  </>
);