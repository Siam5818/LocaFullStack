/* cspell:disable */

import { useState } from "react";
import { useMutation, useQueryClient } from "@tanstack/react-query";
import { Card, CardContent } from "@/components/ui/card";
import { Badge } from "@/components/ui/badge";
import { Button } from "@/components/ui/button";
import { toast } from "sonner";
import { formatDate } from "@/shared/lib/format";
import { ContratForm } from "./ContratForm";
import apiClient from "@/shared/api/client";
import type { Contrat } from "@/entities/contrat/types";
import { Link } from "react-router-dom";

export interface ReservationAdminData {
  id: number;
  statut: "en_attente" | "confirmee" | "annulee";
  date_soumission: string;
  client: { personne: { nom: string; prenom: string; email: string } };
  propriete: { nom: string; ville: string };
  contrat?: Contrat | null;
}

const STATUT_VARIANT: Record<
  string,
  "default" | "secondary" | "destructive" | "outline"
> = {
  en_attente: "secondary",
  confirmee: "default",
  annulee: "destructive",
};

interface Props {
  reservation: ReservationAdminData;
}

export function ReservationCard({ reservation: r }: Readonly<Props>) {
  const queryClient = useQueryClient();
  const [showContratForm, setShowContratForm] = useState(false);

  const { mutate: confirmer } = useMutation({
    mutationFn: () => apiClient.patch(`/admin/reservations/${r.id}/confirmer`),
    onSuccess: () => {
      toast.success("Réservation confirmée.");
      queryClient.invalidateQueries({ queryKey: ["admin-reservations"] });
    },
    onError: () => toast.error("Erreur lors de la confirmation."),
  });

  const { mutate: annuler } = useMutation({
    mutationFn: () => apiClient.patch(`/admin/reservations/${r.id}/annuler`),
    onSuccess: () => {
      toast.success("Réservation annulée.");
      queryClient.invalidateQueries({ queryKey: ["admin-reservations"] });
    },
    onError: () => toast.error("Erreur lors de l'annulation."),
  });

  return (
    <Card>
      <CardContent className="p-5 space-y-3">
        <div className="flex flex-wrap items-start justify-between gap-2">
          <div>
            <p className="font-semibold">{r.propriete?.nom}</p>
            <p className="text-sm text-gray-500">
              {r.client?.personne.prenom} {r.client?.personne.nom}
              {" · "}
              {formatDate(r.date_soumission)}
            </p>
          </div>
          <Badge variant={STATUT_VARIANT[r.statut] ?? "outline"}>
            {r.statut}
          </Badge>
        </div>

        {r.statut === "en_attente" && (
          <div className="flex gap-2">
            <Button size="sm" onClick={() => confirmer()}>
              Confirmer
            </Button>
            <Button size="sm" variant="destructive" onClick={() => annuler()}>
              Annuler
            </Button>
          </div>
        )}

        {r.statut === "confirmee" && !r.contrat && (
          <>
            {showContratForm ? (
              <ContratForm
                reservationId={r.id}
                onCancel={() => setShowContratForm(false)}
              />
            ) : (
              <Button
                size="sm"
                variant="outline"
                onClick={() => setShowContratForm(true)}
              >
                Créer le contrat
              </Button>
            )}
          </>
        )}

        {r.contrat && (
          <Link to={`/admin/contrats/${r.contrat.id}/paiements`}>
            <Button size="sm" variant="outline" className="text-green-600">
              ✓ Contrat #{r.contrat.id} — Gérer les paiements
            </Button>
          </Link>
        )}
      </CardContent>
    </Card>
  );
}
