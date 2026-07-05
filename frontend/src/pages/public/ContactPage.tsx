/* cspell:disable */

import { useState } from 'react';
import { useMutation } from '@tanstack/react-query';
import { Header } from '@/widgets/header/Header';
import { Footer } from '@/widgets/footer/Footer';
import { Card, CardContent } from '@/components/ui/card';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { toast } from 'sonner';
import apiClient from '@/shared/api/client';

const empty = { nom: '', email: '', telephone: '', objet: '', description: '' };

export default function ContactPage() {
  const [form, setForm] = useState(empty);
  const [sent, setSent] = useState(false);

  const set = (key: keyof typeof form) =>
    (e: React.ChangeEvent<HTMLInputElement | HTMLTextAreaElement>) =>
      setForm((f) => ({ ...f, [key]: e.target.value }));

  const { mutate, isPending } = useMutation({
    mutationFn: () => apiClient.post('/demandes', form),
    onSuccess: () => {
      toast.success('Votre demande a bien été envoyée.');
      setSent(true);
    },
    onError: (err: any) => {
      const errors = err?.response?.data?.errors;
      if (errors) Object.values(errors).flat().forEach((m) => toast.error(m as string));
      else toast.error('Erreur lors de l\'envoi.');
    },
  });

  return (
    <div className="min-h-screen flex flex-col">
      <Header />
      <main className="flex-1 max-w-2xl mx-auto px-4 py-12 w-full">
        <h1 className="text-2xl font-bold mb-2">Contactez-nous</h1>
        <p className="text-gray-500 text-sm mb-8">
          Une question sur un bien, une demande de visite ou une information ?
          Remplissez ce formulaire et nous vous répondrons rapidement.
        </p>

        {sent ? (
          <Card>
            <CardContent className="p-8 text-center space-y-3">
              <p className="text-4xl">✉️</p>
              <p className="font-semibold">Demande envoyée avec succès !</p>
              <p className="text-sm text-gray-500">
                Notre équipe vous contactera dans les plus brefs délais.
              </p>
              <Button onClick={() => { setForm(empty); setSent(false); }} variant="outline">
                Envoyer une autre demande
              </Button>
            </CardContent>
          </Card>
        ) : (
          <Card>
            <CardContent className="p-6">
              <form
                onSubmit={(e) => { e.preventDefault(); mutate(); }}
                className="space-y-4"
              >
                <div className="grid grid-cols-2 gap-4">
                  <div>
                    <Label>Nom complet</Label>
                    <Input required value={form.nom} onChange={set('nom')} placeholder="Votre nom" />
                  </div>
                  <div>
                    <Label>Téléphone</Label>
                    <Input required value={form.telephone} onChange={set('telephone')} placeholder="771234567" />
                  </div>
                </div>
                <div>
                  <Label>Adresse e-mail</Label>
                  <Input type="email" required value={form.email} onChange={set('email')} placeholder="vous@example.com" />
                </div>
                <div>
                  <Label>Objet</Label>
                  <Input required value={form.objet} onChange={set('objet')} placeholder="Ex: Demande de visite — Villa Almadies" />
                </div>
                <div>
                  <Label>Message</Label>
                  <textarea
                    required
                    className="border rounded-md px-3 py-2 text-sm w-full min-h-32 resize-none focus:outline-none focus:ring-1 focus:ring-gray-300"
                    value={form.description}
                    onChange={set('description')}
                    placeholder="Décrivez votre demande..."
                  />
                </div>
                <Button type="submit" className="w-full" disabled={isPending}>
                  {isPending ? 'Envoi en cours...' : 'Envoyer ma demande'}
                </Button>
              </form>
            </CardContent>
          </Card>
        )}
      </main>
      <Footer />
    </div>
  );
}