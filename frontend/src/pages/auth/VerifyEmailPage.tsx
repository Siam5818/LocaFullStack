/* cspell:disable */

import { Link } from 'react-router-dom';
import { Button } from '@/components/ui/button';
import { Header } from '@/widgets/header/Header';

export default function VerifyEmailPage() {
  return (
    <div className="min-h-screen flex flex-col">
      <Header />
      <main className="flex-1 flex items-center justify-center px-4">
        <div className="max-w-sm w-full text-center space-y-4">
          <div className="text-5xl">📧</div>
          <h1 className="text-xl font-bold">Vérifiez votre e-mail</h1>
          <p className="text-sm text-gray-500">
            Nous vous avons envoyé un e-mail de confirmation. Cliquez sur le lien
            dans l'e-mail pour activer votre compte.
          </p>
          <p className="text-xs text-gray-400">
            Vérifiez également vos spams si vous ne trouvez pas l'e-mail.
          </p>
          <Link to="/connexion">
            <Button variant="outline" className="w-full">Retour à la connexion</Button>
          </Link>
        </div>
      </main>
    </div>
  );
}