/* cspell:disable */

import { useQuery } from "@tanstack/react-query";
import { Link } from "react-router-dom";
import { Button } from "@/components/ui/button";
import { Header } from "@/widgets/header/Header";
import { Footer } from "@/widgets/footer/Footer";
import { getProprietes } from "@/entities/propriete/api";
import { Skeleton } from "@/components/ui/skeleton";
import { ProprieteCard } from "@/widgets/propriete-card/ProprieteCard";
import { Propriete } from "@/entities/propriete/types";

export default function AccueilPage() {
  const { data, isLoading } = useQuery({
    queryKey: ["proprietes", "vedettes"],
    queryFn: () => getProprietes({ tri: "recent" }),
  });

  const responseData = data as any;
  const listeProprietes: Propriete[] =
    responseData?.data ?? (Array.isArray(data) ? data : []);

  return (
    <div className="min-h-screen flex flex-col">
      <Header />
      <main className="flex-1">
        <section className="bg-gray-50 py-16 text-center">
          <h1 className="text-3xl font-bold mb-3">
            Trouvez votre prochain logement à Dakar
          </h1>
          <p className="text-gray-600 mb-6">
            Location et vente de biens immobiliers, en toute confiance.
          </p>
          <Link to="/proprietes">
            <Button size="lg">Voir les propriétés</Button>
          </Link>
        </section>

        <section className="max-w-6xl mx-auto px-4 py-12">
          <h2 className="text-xl font-semibold mb-6">Dernières propriétés</h2>
          <div className="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
            {isLoading &&
              Array.from({ length: 6 }).map((_, i) => (
                <Skeleton key={`skeleton-${i}`} className="h-72 rounded-lg" />
              ))}

            {listeProprietes.map((propriete: any) => {
              console.log("Propriété actuelle :", propriete);
              return <ProprieteCard key={propriete.id} propriete={propriete} />;
            })}
          </div>
        </section>
      </main>
      <Footer />
    </div>
  );
}
