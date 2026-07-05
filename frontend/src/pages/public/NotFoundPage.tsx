/* cspell:disable */

import { Link } from 'react-router-dom';
import { Button } from '@/components/ui/button';
import { Header } from '@/widgets/header/Header';

export default function NotFoundPage() {
  return (
    <div className="min-h-screen flex flex-col">
      <Header />
      <main className="flex-1 flex flex-col items-center justify-center text-center px-4">
        <p className="text-6xl font-bold text-gray-200 mb-4">404</p>
        <h1 className="text-xl font-semibold mb-2">Page introuvable</h1>
        <p className="text-gray-500 text-sm mb-6">
          La page que vous cherchez n'existe pas ou a été déplacée.
        </p>
        <Link to="/"><Button>Retour à l'accueil</Button></Link>
      </main>
    </div>
  );
}