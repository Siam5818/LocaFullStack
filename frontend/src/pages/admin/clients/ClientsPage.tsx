import { useQuery } from '@tanstack/react-query';
import { Header } from '@/widgets/header/Header';
import { Footer } from '@/widgets/footer/Footer';
import { Skeleton } from '@/components/ui/skeleton';
import apiClient from '@/shared/api/client';
import { ClientCard, type ClientAdminData } from './ClientCard';

async function getClients() {
  const { data } = await apiClient.get<ClientAdminData[]>('/admin/clients');
  return data;
}

export default function AdminClientsPage() {
  const { data, isLoading } = useQuery({ queryKey: ['admin-clients'], queryFn: getClients });

  return (
    <div className="min-h-screen flex flex-col">
      <Header />
      <main className="flex-1 max-w-4xl mx-auto px-4 py-8 w-full">
        <h1 className="text-2xl font-bold mb-6">Clients ({data?.length ?? 0})</h1>
        <div className="space-y-3">
          {isLoading && Array.from({ length: 4 }).map((_, i) => <Skeleton key={i} className="h-20 w-full" />)}
          {data?.map((c) => <ClientCard key={c.id} client={c} />)}
          {data?.length === 0 && !isLoading && (
            <p className="text-gray-500 text-sm text-center py-12">Aucun client.</p>
          )}
        </div>
      </main>
      <Footer />
    </div>
  );
}