/* cspell:disable */
import { BrowserRouter, Routes, Navigate } from "react-router-dom";
import { useAuth } from "@/features/auth/useAuth";
import type { Role } from "@/entities/personne/types";

import { publicRoutes } from "./routes.public";
import { clientRoutes } from "./routes.client";
import { bailleurRoutes } from "./routes.bailleur";
import { adminRoutes } from "./routes.admin";

function RoleGuard({
  allowed,
  children,
}: Readonly<{ allowed: Role[]; children: React.ReactNode }>) {
  const { user, isLoading } = useAuth();

  if (isLoading) return null;
  if (!user) return <Navigate to="/connexion" replace />;
  if (!allowed.includes(user.role)) return <Navigate to="/" replace />;

  return <>{children}</>;
}

export function AppRouter() {
  return (
    <BrowserRouter>
      <Routes>
        {publicRoutes}
        {clientRoutes(RoleGuard)}
        {bailleurRoutes(RoleGuard)}
        {adminRoutes(RoleGuard)}
      </Routes>
    </BrowserRouter>
  );
}
