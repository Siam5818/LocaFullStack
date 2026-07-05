/* cspell:disable */

import { useMutation, useQueryClient } from "@tanstack/react-query";
import { toast } from "sonner";
import { creerReservation } from "@/entities/reservation/api";

export function useCreerReservation() {
  const queryClient = useQueryClient();

  return useMutation({
    mutationFn: (proprieteId: number) => creerReservation(proprieteId),
    onSuccess: () => {
      toast.success("Réservation envoyée avec succès.");
      queryClient.invalidateQueries({ queryKey: ["mes-reservations"] });
    },
    onError: (err: any) => {
      const message =
        err?.response?.data?.message ?? "Impossible de créer la réservation.";
      toast.error(message);
    },
  });
}
