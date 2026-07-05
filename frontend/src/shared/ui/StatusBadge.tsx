/* cspell:disable */
import { Badge } from '@/components/ui/badge';
import {
  STATUT_RESERVATION_LABELS,
  STATUT_CONTRAT_LABELS,
  STATUT_PAIEMENT_LABELS,
} from '@/shared/lib/constants';

type StatusType = 'reservation' | 'contrat' | 'paiement';

const MAP: Record<StatusType, Record<string, { label: string; variant: 'default' | 'secondary' | 'destructive' | 'outline' }>> = {
  reservation: STATUT_RESERVATION_LABELS,
  contrat:     STATUT_CONTRAT_LABELS,
  paiement:    STATUT_PAIEMENT_LABELS,
};

interface StatusBadgeProps {
  type: StatusType;
  statut: string;
}

export function StatusBadge({ type, statut }: Readonly<StatusBadgeProps>) {
  const config = MAP[type][statut] ?? { label: statut, variant: 'outline' as const };
  return <Badge variant={config.variant}>{config.label}</Badge>;
}
