/* cspell:disable */

import { Link } from "react-router-dom";
import { Card, CardContent } from "@/components/ui/card";
import { Badge } from "@/components/ui/badge";
import { formatFCFA } from "@/shared/lib/format";
import type { Propriete } from "@/entities/propriete/types";

export function ProprieteCard({
  propriete,
}: Readonly<{ propriete: Propriete }>) {
  const image = propriete.image_principale?.[0]?.chemin;
  const imageUrl = image
    ? `${import.meta.env.VITE_API_URL?.replace("/api", "")}/storage/${image}`
    : null;

  return (
    <Link to={`/proprietes/${propriete.id}`}>
      <Card className="overflow-hidden hover:shadow-md transition-shadow h-full">
        <div className="aspect-video bg-gray-100 flex items-center justify-center">
          {imageUrl ? (
            <img
              src={imageUrl || "https://unsplash.com"}
              alt={propriete.nom}
              className="w-full h-full object-cover"
            />
          ) : (
            <span className="text-gray-400 text-sm">Pas d'image</span>
          )}
        </div>

        <CardContent className="p-4 space-y-2">
          <div className="flex items-center justify-between">
            <Badge variant="secondary">
              {propriete.service?.nom ||
                (propriete.service.id === 1 ? "Location" : "Vente")}
            </Badge>
            <Badge variant="outline">
              {propriete.type_propriete?.nom || "Bien immobilier"}
            </Badge>
          </div>
          <h3 className="font-semibold line-clamp-1">{propriete.nom}</h3>
          <p className="text-sm text-gray-500">
            {propriete.quartier}, {propriete.ville}
          </p>
          <div className="flex items-center justify-between text-sm">
            <span>
              {propriete.nombre_piece} pièce(s) · {propriete.dimension} m²
            </span>
          </div>
          <p className="font-bold text-lg">{formatFCFA(propriete.cout)}</p>
        </CardContent>
      </Card>
    </Link>
  );
}
