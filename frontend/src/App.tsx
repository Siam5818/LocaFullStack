/* cspell:disable */
import { QueryProvider } from './app/providers/QueryProvider';
import { AppRouter } from './app/router';
import { Toaster } from '@/components/ui/sonner';

export function App() {
  return (
    <QueryProvider>
      <AppRouter />
      <Toaster position="top-right" richColors />
    </QueryProvider>
  );
}

export default App;