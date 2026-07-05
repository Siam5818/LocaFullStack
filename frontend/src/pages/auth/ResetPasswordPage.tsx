/* cspell:disable */

import { useState } from 'react';
import { useNavigate, useSearchParams } from 'react-router-dom';
import { resetPassword } from '@/features/auth/api';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { toast } from 'sonner';
import { Header } from '@/widgets/header/Header';

export default function ResetPasswordPage() {
  const navigate = useNavigate();
  const [params] = useSearchParams();
  const token = params.get('token') ?? '';
  const email = params.get('email') ?? '';

  const [form, setForm] = useState({ password: '', password_confirmation: '' });
  const [loading, setLoading] = useState(false);

  const set = (key: keyof typeof form) =>
    (e: React.ChangeEvent<HTMLInputElement>) =>
      setForm((f) => ({ ...f, [key]: e.target.value }));

  async function handleSubmit(e: any) {
    e.preventDefault();
    if (form.password !== form.password_confirmation) {
      toast.error('Les mots de passe ne correspondent pas.');
      return;
    }
    setLoading(true);
    try {
      const res = await resetPassword({ token, email, ...form });
      toast.success(res.message);
      navigate('/connexion');
    } catch (err: any) {
      toast.error(err?.response?.data?.message ?? 'Lien invalide ou expiré.');
    } finally {
      setLoading(false);
    }
  }

  if (!token || !email) {
    return (
      <div className="min-h-screen flex flex-col">
        <Header />
        <main className="flex-1 flex items-center justify-center">
          <p className="text-red-500">Lien de réinitialisation invalide.</p>
        </main>
      </div>
    );
  }

  return (
    <div className="min-h-screen flex flex-col">
      <Header />
      <main className="flex-1 flex items-center justify-center px-4">
        <Card className="w-full max-w-sm">
          <CardHeader>
            <CardTitle>Nouveau mot de passe</CardTitle>
          </CardHeader>
          <CardContent>
            <form onSubmit={handleSubmit} className="space-y-4">
              <p className="text-sm text-gray-500">
                Réinitialisation pour : <strong>{email}</strong>
              </p>
              <div>
                <Label>Nouveau mot de passe</Label>
                <Input
                  type="password"
                  required
                  value={form.password}
                  onChange={set('password')}
                />
                <p className="text-xs text-gray-400 mt-1">8 caractères min., une majuscule, un chiffre.</p>
              </div>
              <div>
                <Label>Confirmer le mot de passe</Label>
                <Input
                  type="password"
                  required
                  value={form.password_confirmation}
                  onChange={set('password_confirmation')}
                />
              </div>
              <Button type="submit" className="w-full" disabled={loading}>
                {loading ? 'Réinitialisation...' : 'Réinitialiser mon mot de passe'}
              </Button>
            </form>
          </CardContent>
        </Card>
      </main>
    </div>
  );
}