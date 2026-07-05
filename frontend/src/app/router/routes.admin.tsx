/* cspell:disable */
import AdminBailleursPage from '@/pages/admin/bailleurs/BailleursPage';
import AdminClientsPage from '@/pages/admin/clients/ClientsPage';
import AdminDashboardPage from '@/pages/admin/DashboardPage';
import AdminProprietesPage from '@/pages/admin/proprietes/ProprietesPage';
import AdminReservationsPage from '@/pages/admin/reservations/ReservationsPage';
import AdminPaiementsContratPage from '@/pages/admin/paiements/PaiementsContratPage';
import type { ComponentType, ReactNode } from 'react';
import { Route } from 'react-router-dom';

type Guard = ComponentType<{ allowed: ('client' | 'bailleur' | 'admin')[]; children: ReactNode }>

export const adminRoutes = (RoleGuard: Guard) => (
  <>
    <Route path="/admin" element={<RoleGuard allowed={['admin']}><AdminDashboardPage /></RoleGuard>} />
    <Route path="/admin/reservations" element={<RoleGuard allowed={['admin']}><AdminReservationsPage /></RoleGuard>} />
    <Route path="/admin/clients" element={<RoleGuard allowed={['admin']}><AdminClientsPage /></RoleGuard>} />
    <Route path="/admin/bailleurs" element={<RoleGuard allowed={['admin']}><AdminBailleursPage /></RoleGuard>} />
    <Route path="/admin/proprietes" element={<RoleGuard allowed={['admin']}><AdminProprietesPage /></RoleGuard>} />
    <Route path="/admin/contrats/:contratId/paiements" element={<RoleGuard allowed={['admin']}><AdminPaiementsContratPage /></RoleGuard>}/>
  </>
);