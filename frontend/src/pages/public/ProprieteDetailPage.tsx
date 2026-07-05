/* cspell:disable */

import { useState } from 'react';
import { useParams } from 'react-router-dom';
import { useMutation, useQueryClient } from '@tanstack/react-query';
import { Header } from '@/widgets/header/Header';
import { Footer } from '@/widgets/footer/Footer';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import { Label } from '@/components/ui/label';
import { LoadingSpinner } from '@/shared/ui/LoadingSpinner';
import { EmptyState } from '@/shared/ui/EmptyState';
import { useProprieteDetail } from '@/entities/propriete/hooks';
import { useAuth } from '@/features/auth/useAuth';
import { useCreerReservation } from '@/features/reservation-creation/useCreerReservation';
import { useAjouterFavori } from '@/features/favoris-toggle/useFavoris';
import { formatFCFA, formatDate } from '@/shared/lib/format';
import { toast } from 'sonner';
import apiClient from '@/shared/api/client';

export default function ProprieteDetailPage() {
  const { id } = useParams<{ id: string }>();
  const { user } = useAuth();
  const queryClient = useQueryClient();

  const { data, isLoading } = useProprieteDetail(Number(id));
  const { mutate: reserver, isPending: reservPending } = useCreerReservation();
  const { mutate: ajouterFavori, isPending: favPending } = useAjouterFavori();

  const [noteForm, setNoteForm] = useState({ note: 5, commentaire: '' });
  const [showNoteForm, setShowNoteForm] = useState(false);

  const { mutate: soumettreNote, isPending: notePending } = useMutation({
    mutationFn: () =>
      apiClient.post(`/proprietes/${data?.propriete.id}/notes`, noteForm),
    onSuccess: () => {
      toast.success('Avis publié avec succès.');
      setShowNoteForm(false);
      setNoteForm({ note: 5, commentaire: '' });
      queryClient.invalidateQueries({ queryKey: ['propriete', Number(id)] });
    },
    onError: (err: any) =>
      toast.error(err?.response?.data?.message ?? 'Erreur lors de la publication.'),
  });

  if (isLoading) {
    return (
      <div className="min-h-screen flex flex-col">
        <Header />
        <main className="flex-1"><LoadingSpinner /></main>
        <Footer />
      </div>
    );
  }

  if (!data) {
    return (
      <div className="min-h-screen flex flex-col">
        <Header />
        <main className="flex-1">
          <EmptyState message="Propriété introuvable." icon="🔍" />
        </main>
        <Footer />
      </div>
    );
  }

  const { propriete, note_moyenne, nombre_avis } = data;

  function handleReserver() {
    if (!user) { toast.error('Connectez-vous pour réserver.'); return; }
    if (user.role !== 'client') { toast.error('Seuls les clients peuvent réserver.'); return; }
    reserver(propriete.id);
  }

  function handleFavori() {
    if (!user) { toast.error('Connectez-vous pour ajouter aux favoris.'); return; }
    if (user.role !== 'client') { toast.error('Seuls les clients peuvent ajouter des favoris.'); return; }
    ajouterFavori(propriete.id);
  }

  return (
    <div className="min-h-screen flex flex-col">
      <Header />
      <main className="flex-1 max-w-4xl mx-auto px-4 py-8 w-full space-y-6">

        {/* En-tête */}
        <div>
          <div className="flex gap-2 mb-2">
            <Badge variant="secondary">{propriete.service.nom}</Badge>
            <Badge variant="outline">{propriete.type_propriete.nom}</Badge>
          </div>
          <h1 className="text-2xl font-bold">{propriete.nom}</h1>
          <p className="text-gray-500">
            {[propriete.rue, propriete.quartier, propriete.ville]
              .filter(Boolean)
              .join(', ')}
          </p>
        </div>

        {/* Galerie */}
        <div className="aspect-video bg-gray-100 rounded-lg flex items-center justify-center text-gray-400 text-sm overflow-hidden">
          {propriete.images && propriete.images.length > 0 ? (
            <div className="flex gap-2 overflow-x-auto w-full h-full p-2">
              {propriete.images.map((img) => (
                <img
                  key={img.id}
                  src={`${import.meta.env.VITE_API_URL?.replace('/api', '')}/storage/${img.chemin}`}
                  alt={propriete.nom}
                  className="h-full object-cover rounded flex-shrink-0"
                />
              ))}
            </div>
          ) : (
            <span>Pas d'image disponible</span>
          )}
        </div>

        {/* Prix + actions */}
        <div className="flex flex-col sm:flex-row sm:items-center justify-between border-y py-4 gap-3">
          <span className="text-2xl font-bold">{formatFCFA(propriete.cout)}</span>
          <div className="flex gap-2">
            <Button
              variant="outline"
              onClick={handleFavori}
              disabled={favPending || !user || user.role !== 'client'}
            >
              ♡ Favoris
            </Button>
            <Button
              onClick={handleReserver}
              disabled={reservPending || !user || user.role !== 'client'}
            >
              {reservPending ? 'Envoi...' : 'Réserver'}
            </Button>
          </div>
        </div>

        {/* Caractéristiques */}
        <div className="grid grid-cols-3 gap-4 text-sm text-center">
          <div className="border rounded-lg p-3">
            <p className="text-gray-500 text-xs">Pièces</p>
            <p className="font-bold text-lg">{propriete.nombre_piece}</p>
          </div>
          <div className="border rounded-lg p-3">
            <p className="text-gray-500 text-xs">Surface</p>
            <p className="font-bold text-lg">{propriete.dimension} m²</p>
          </div>
          <div className="border rounded-lg p-3">
            <p className="text-gray-500 text-xs">Note moyenne</p>
            <p className="font-bold text-lg">{note_moyenne}/5</p>
            <p className="text-xs text-gray-400">{nombre_avis} avis</p>
          </div>
        </div>

        {/* Description */}
        <div>
          <h2 className="font-semibold mb-2">Description</h2>
          <p className="text-gray-700 leading-relaxed">{propriete.description}</p>
        </div>

        {/* Équipements */}
        {propriete.equipements.length > 0 && (
          <div>
            <h2 className="font-semibold mb-2">Équipements</h2>
            <div className="flex flex-wrap gap-2">
              {propriete.equipements.map((eq) => (
                <Badge key={eq.id} variant="outline">
                  {eq.nom}
                </Badge>
              ))}
            </div>
          </div>
        )}

        {/* Section avis */}
        <div>
          <h2 className="font-semibold mb-3">Avis ({nombre_avis})</h2>

          {/* Formulaire de note — visible uniquement pour les clients connectés */}
          {user?.role === 'client' && (
            <div className="mb-4">
              {showNoteForm ? (
                <div className="border rounded-lg p-4 space-y-3 bg-gray-50">
                  <p className="font-medium text-sm">Votre avis</p>

                  {/* Sélection de la note en étoiles */}
                  <div>
                    <Label className="text-xs text-gray-500">Note</Label>
                    <div className="flex gap-1 mt-1">
                      {[1, 2, 3, 4, 5].map((n) => (
                        <button
                          key={n}
                          type="button"
                          onClick={() => setNoteForm((f) => ({ ...f, note: n }))}
                          className={`text-2xl transition-all ${
                            noteForm.note >= n
                              ? 'text-yellow-400 scale-110'
                              : 'text-gray-300'
                          }`}
                        >
                          ★
                        </button>
                      ))}
                      <span className="ml-2 text-sm text-gray-500 self-center">
                        {noteForm.note}/5
                      </span>
                    </div>
                  </div>

                  {/* Commentaire */}
                  <div>
                    <Label className="text-xs text-gray-500">
                      Commentaire (optionnel)
                    </Label>
                    <textarea
                      className="border rounded-md px-3 py-2 text-sm w-full min-h-20 resize-none mt-1 focus:outline-none focus:ring-1 focus:ring-gray-300"
                      value={noteForm.commentaire}
                      onChange={(e) =>
                        setNoteForm((f) => ({ ...f, commentaire: e.target.value }))
                      }
                      placeholder="Partagez votre expérience avec cette propriété..."
                    />
                  </div>

                  <div className="flex gap-2">
                    <Button
                      size="sm"
                      onClick={() => soumettreNote()}
                      disabled={notePending}
                    >
                      {notePending ? 'Publication...' : 'Publier mon avis'}
                    </Button>
                    <Button
                      size="sm"
                      variant="outline"
                      onClick={() => {
                        setShowNoteForm(false);
                        setNoteForm({ note: 5, commentaire: '' });
                      }}
                    >
                      Annuler
                    </Button>
                  </div>
                </div>
              ) : (
                <Button
                  size="sm"
                  variant="outline"
                  className="mb-3"
                  onClick={() => setShowNoteForm(true)}
                >
                  ✏️ Laisser un avis
                </Button>
              )}
            </div>
          )}

          {/* Liste des avis */}
          {propriete.notes.length === 0 ? (
            <EmptyState message="Aucun avis pour le moment." icon="💬" />
          ) : (
            <div className="space-y-3">
              {propriete.notes.map((note) => (
                <div key={note.id} className="border rounded-lg p-4 text-sm">
                  <div className="flex items-start justify-between mb-1">
                    <div>
                      <span className="font-medium">
                        {note.client.personne.prenom} {note.client.personne.nom}
                      </span>
                      <span className="ml-2 text-yellow-400">
                        {'★'.repeat(note.note)}
                        <span className="text-gray-200">
                          {'★'.repeat(5 - note.note)}
                        </span>
                      </span>
                    </div>
                    <span className="text-gray-400 text-xs shrink-0">
                      {formatDate(note.created_at)}
                    </span>
                  </div>
                  {note.commentaire && (
                    <p className="text-gray-600 mt-1 leading-relaxed">
                      {note.commentaire}
                    </p>
                  )}
                </div>
              ))}
            </div>
          )}
        </div>

      </main>
      <Footer />
    </div>
  );
}