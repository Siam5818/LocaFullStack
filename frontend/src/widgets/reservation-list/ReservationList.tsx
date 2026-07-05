/* cspell:disable */

import { Link } from "react-router-dom";
import { Card, CardContent } from "@/components/ui/card";
import { Button } from "@/components/ui/button";
import { StatusBadge } from "@/shared/ui/StatusBadge";
import { EmptyState } from "@/shared/ui/EmptyState";
import { formatDate } from "@/shared/lib/format";
import type { Reservation } from "@/entities/reservation/types";

interface Props {
  reservations: Reservation[];
  onAnnuler?: (id: number) => void;
  isPending?: boolean;
  limit?: number;
  showLink?: boolean;
}

export function ReservationList({
  reservations,
  onAnnuler,
  isPending = false,
  limit,
  showLink = false,
}: Readonly<Props>) {
  const items = limit ? reservations.slice(0, limit) : reservations;

  if (items.length === 0) {
    return (
      <EmptyState message="Aucune réservation pour l'instant." icon="🏠" />
    );
  }

  return (
    <div className="space-y-3">
      {items.map((r) => (
        <Card key={r.id}>
          <CardContent className="p-4 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
            <div>
              <p className="font-semibold">
                {r.propriete?.nom ?? `Propriété #${r.propriete_id}`}
              </p>
              <p className="text-sm text-gray-500">
                Soumise le {formatDate(r.date_soumission)}
              </p>
            </div>
            <div className="flex items-center gap-3 shrink-0">
              <StatusBadge type="reservation" statut={r.statut} />
              {r.statut === "en_attente" && onAnnuler && (
                <Button
                  size="sm"
                  variant="destructive"
                  disabled={isPending}
                  onClick={() => onAnnuler(r.id)}
                >
                  Annuler
                </Button>
              )}
              {showLink && (
                <Link to="/espace-client/reservations">
                  <Button size="sm" variant="ghost">
                    Tout voir
                  </Button>
                </Link>
              )}
            </div>
          </CardContent>
        </Card>
      ))}
    </div>
  );
}
