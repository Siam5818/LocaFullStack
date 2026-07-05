/* cspell:disable */

import { useState } from 'react';
import { Link, useNavigate } from 'react-router-dom';
import { register } from '@/features/auth/api';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { toast } from 'sonner';
import { Header } from '@/widgets/header/Header';

export default function RegisterPage() {
  const navigate = useNavigate();
  const [form, setForm] = useState({
    nom: '', prenom: '', email: '', telephone: '', password: '', password_confirmation: ''
  });
  const [loading, setLoading] = useState(false);

  function set(key: keyof typeof form) {
    return (e: React.ChangeEvent<HTMLInputElement>) => setForm((f) => ({ ...f, [key]: e.target.value }));
  }

  async function handleSubmit(e: any) {
    e.preventDefault();
    if (form.password !== form.password_confirmation) {
      toast.error('Les mots de passe ne correspondent pas.'); return;
    }
    setLoading(true);
    try {
      await register(form);
      toast.success('Inscription réussie ! Vérifiez votre e-mail avant de vous connecter.');
      navigate('/verification-email');
    } catch (err: any) {
      const errors = err?.response?.data?.errors;
      if (errors) {
        Object.values(errors).flat().forEach((msg) => toast.error(msg as string));
      } else {
        toast.error(err?.response?.data?.message ?? 'Erreur lors de l\'inscription.');
      }
    } finally {
      setLoading(false);
    }
  }

  return (
    <div className="min-h-screen flex flex-col">
      <Header />
      <main className="flex-1 flex items-center justify-center px-4 py-8">
        <Card className="w-full max-w-md">
          <CardHeader>
            <CardTitle>Créer un compte client</CardTitle>
          </CardHeader>
          <CardContent>
            <form onSubmit={handleSubmit} className="space-y-3">
              <div className="grid grid-cols-2 gap-3">
                <div>
                  <Label>Nom</Label>
                  <Input required value={form.nom} onChange={set('nom')} />
                </div>
                <div>
                  <Label>Prénom</Label>
                  <Input required value={form.prenom} onChange={set('prenom')} />
                </div>
              </div>
              <div>
                <Label>E-mail</Label>
                <Input type="email" required value={form.email} onChange={set('email')} />
              </div>
              <div>
                <Label>Téléphone</Label>
                <Input required value={form.telephone} onChange={set('telephone')} placeholder="771234567" />
              </div>
              <div>
                <Label>Mot de passe</Label>
                <Input type="password" required value={form.password} onChange={set('password')} />
                <p className="text-xs text-gray-400 mt-1">8 caractères min., une majuscule, un chiffre.</p>
              </div>
              <div>
                <Label>Confirmer le mot de passe</Label>
                <Input type="password" required value={form.password_confirmation} onChange={set('password_confirmation')} />
              </div>
              <Button type="submit" className="w-full" disabled={loading}>
                {loading ? 'Inscription...' : 'Créer mon compte'}
              </Button>
              <p className="text-center text-sm text-gray-500">
                Déjà inscrit ? <Link to="/connexion" className="underline">Se connecter</Link>
              </p>
            </form>
          </CardContent>
        </Card>
      </main>
    </div>
  );
}