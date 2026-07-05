/* cspell:disable */

import { Link } from "react-router-dom";
import { useAuth } from "@/features/auth/useAuth";
import { Button } from "@/components/ui/button";

export function Header() {
  const { user, logout } = useAuth();

  return (
    <header className="border-b sticky top-0 bg-white z-50">
      <div className="max-w-6xl mx-auto px-4 h-16 flex items-center justify-between">
        <Link to="/" className="font-bold text-lg">
          Immo Location
        </Link>

        <nav className="flex items-center gap-4">
          <Link
            to="/proprietes"
            className="text-sm text-gray-600 hover:text-gray-900"
          >
            Propriétés
          </Link>
          <Link
            to="/contact"
            className="text-sm text-gray-600 hover:text-gray-900"
          >
            Contact
          </Link>

          {!user && (
            <>
              <Link to="/connexion">
                <Button variant="ghost" size="sm">
                  Connexion
                </Button>
              </Link>
              <Link to="/inscription">
                <Button size="sm">Inscription</Button>
              </Link>
            </>
          )}

          {user?.role === "client" && (
            <>
              <Link
                to="/espace-client"
                className="text-sm text-gray-600 hover:text-gray-900"
              >
                Mon espace
              </Link>
              <Link
                to="/espace-client/contrats"
                className="text-sm text-gray-600 hover:text-gray-900"
              >
                Mes contrats
              </Link>
              <Link
                to="/espace-client/profil"
                className="text-sm text-gray-600 hover:text-gray-900"
              >
                Mon profil
              </Link>
              <Button variant="outline" size="sm" onClick={() => logout()}>
                Déconnexion
              </Button>
            </>
          )}

          {user?.role === "bailleur" && (
            <>
              <Link
                to="/bailleur/proprietes"
                className="text-sm text-gray-600 hover:text-gray-900"
              >
                Mes biens
              </Link>
              <Link
                to="/bailleur/contrats"
                className="text-sm text-gray-600 hover:text-gray-900"
              >
                Mes contrats
              </Link>
              <Link
                to="/bailleur/revenus"
                className="text-sm text-gray-600 hover:text-gray-900"
              >
                Revenus
              </Link>
              <Button variant="outline" size="sm" onClick={() => logout()}>
                Déconnexion
              </Button>
            </>
          )}

          {user?.role === "admin" && (
            <>
              <Link
                to="/admin"
                className="text-sm text-gray-600 hover:text-gray-900"
              >
                Dashboard
              </Link>
              <Button variant="outline" size="sm" onClick={() => logout()}>
                Déconnexion
              </Button>
            </>
          )}
        </nav>
      </div>
    </header>
  );
}
