/* cspell:disable */

import { useState } from "react";
import { useMutation, useQueryClient } from "@tanstack/react-query";
import { Card, CardContent } from "@/components/ui/card";
import { Badge } from "@/components/ui/badge";
import { Button } from "@/components/ui/button";
import { formatFCFA } from "@/shared/lib/format";
import { toast } from "sonner";
import apiClient from "@/shared/api/client";
import type { Propriete } from "@/entities/propriete/types";
import { ImageUpload } from "./ImageUpload";

export function ProprieteAdminCard({
  propriete: p,
}: Readonly<{ propriete: Propriete }>) {
  const queryClient = useQueryClient();

  const { mutate: supprimer } = useMutation({
    mutationFn: () => apiClient.delete(`/admin/proprietes/${p.id}`),
    onSuccess: () => {
      toast.success("Propriété supprimée.");
      queryClient.invalidateQueries({ queryKey: ["admin-proprietes"] });
      queryClient.invalidateQueries({ queryKey: ["proprietes"] });
    },
    onError: () => toast.error("Erreur lors de la suppression."),
  });

  const [showUpload, setShowUpload] = useState(false);

  return (
    <Card>
      <CardContent className="p-4 space-y-1">
        <div className="flex justify-between items-start">
          <p className="font-semibold">{p.nom}</p>
          <Badge variant="outline">{p.service.nom}</Badge>
        </div>
        <p className="text-sm text-gray-500">
          {p.quartier}, {p.ville}
        </p>
        <p className="font-bold">{formatFCFA(p.cout)}</p>
        <p className="text-xs text-gray-400">
          {p.nombre_piece} pièce(s) · {p.dimension} m²
        </p>
        <Button
          size="sm"
          variant="destructive"
          className="mt-2"
          onClick={() => {
            if (confirm("Supprimer cette propriété ?")) supprimer();
          }}
        >
          Supprimer
        </Button>

        <Button
          size="sm"
          variant="outline"
          className="mt-1"
          onClick={() => setShowUpload((v) => !v)}
        >
          {showUpload ? "Fermer" : "Ajouter des images"}
        </Button>

        {showUpload && (
          <div className="mt-3 pt-3 border-t">
            <ImageUpload proprieteId={p.id} />
          </div>
        )}
      </CardContent>
    </Card>
  );
}
