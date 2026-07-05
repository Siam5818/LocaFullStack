<?php
/* cspell:disable */

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\DemandeController;
use App\Http\Controllers\Api\EquipementController;
use App\Http\Controllers\Api\ProfilController;
use App\Http\Controllers\Api\ProprieteController;
use App\Http\Controllers\Api\ServiceController;
use App\Http\Controllers\Api\TypeProprieteController;
use App\Http\Controllers\Api\Client\ContratController as ClientContratController;
use App\Http\Controllers\Api\Client\FavorieController;
use App\Http\Controllers\Api\Client\NoteController as ClientNoteController;
use App\Http\Controllers\Api\Client\ReservationController as ClientReservationController;
use App\Http\Controllers\Api\Bailleur\ContratController as BailleurContratController;
use App\Http\Controllers\Api\Bailleur\ProprieteController as BailleurProprieteController;
use App\Http\Controllers\Api\Bailleur\RevenuController;
use App\Http\Controllers\Api\Admin\BailleurController as AdminBailleurController;
use App\Http\Controllers\Api\Admin\ClientController as AdminClientController;
use App\Http\Controllers\Api\Admin\ContratController as AdminContratController;
use App\Http\Controllers\Api\Admin\DashboardController;
use App\Http\Controllers\Api\Admin\DemandeController as AdminDemandeController;
use App\Http\Controllers\Api\Admin\EquipementController as AdminEquipementController;
use App\Http\Controllers\Api\Admin\ProprieteController as AdminProprieteController;
use App\Http\Controllers\Api\Admin\TypeContratController as AdminTypeContratController;
use App\Http\Controllers\Api\Admin\TypeProprieteController as AdminTypeProprieteController;
use App\Http\Controllers\Api\Admin\PaiementController as AdminPaiementController;
use App\Http\Controllers\Api\Admin\ReservationController as AdminReservationController;
use App\Http\Controllers\Api\Admin\ImageProprieteController;
use App\Http\Controllers\Api\Admin\ServiceController as AdminServiceController;
use App\Http\Controllers\Api\PasswordResetController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| PUBLIC — Authentification
|--------------------------------------------------------------------------
*/

Route::post('/register', [AuthController::class, 'register'])->middleware('throttle:sensitive');
Route::post('/login', [AuthController::class, 'login']);
Route::post('/email/resend', [AuthController::class, 'resendVerification'])->middleware('throttle:sensitive');
Route::post('/forgot-password', [PasswordResetController::class, 'forgotPassword'])
    ->middleware('throttle:sensitive');

Route::post('/reset-password', [PasswordResetController::class, 'resetPassword'])
    ->middleware('throttle:sensitive');

Route::get('/email/verify/{id}/{hash}', [AuthController::class, 'verifyEmail'])
    ->middleware('signed')->name('verification.verify');

/*
|--------------------------------------------------------------------------
| PUBLIC — Propriétés
|--------------------------------------------------------------------------
*/

// Regroupement des routes pour éviter la duplication des URL "/proprietes"
Route::prefix('proprietes')->group(function () {
    Route::get('/', [ProprieteController::class, 'index']);
    Route::get('{propriete}', [ProprieteController::class, 'show']);
});

Route::get('/typeproprietes', [TypeProprieteController::class, 'index']);
Route::get('/services', [ServiceController::class, 'index']);
Route::get('/equipements', [EquipementController::class, 'index']);
Route::get('/proprietes/{propriete}/notes', [ClientNoteController::class, 'index']);

Route::post('/demandes', [DemandeController::class, 'store'])->middleware('throttle:sensitive');

/*
|--------------------------------------------------------------------------
| AUTHENTIFIÉ — tous rôles confondus
|--------------------------------------------------------------------------
*/

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);

    Route::controller(ProfilController::class)->group(function () {
        Route::get('/me', [AuthController::class, 'me']);
        Route::put('/me', 'update');
        Route::put('/me/password', 'updatePassword');
    });

    /*
    |----------------------------------------------------------------------
    | CLIENT (role:client)
    |----------------------------------------------------------------------
    */
    Route::middleware('role:client')->group(function () {
        Route::prefix('mes-reservations')->controller(ClientReservationController::class)->group(function () {
            Route::post('/', 'store');
            Route::get('/', 'index');
            Route::get('{reservation}', 'show');
            Route::delete('{reservation}', 'destroy');
        });

        Route::prefix('mes-contrats')->controller(ClientContratController::class)->group(function () {
            Route::get('/', 'index');

            Route::prefix('{contrat}')->group(function () {
                Route::get('/', 'show');
                Route::get('paiements', 'paiements');
                Route::get('telecharger', 'telecharger');
                Route::get('paiements/{paiement}/recu', 'recuPaiement');
            });
        });

        Route::controller(FavorieController::class)->group(function () {
            Route::post('/favoris', 'store');
            Route::get('/mes-favoris', 'index');
            Route::delete('/favoris/{favorie}', 'destroy');
        });

        Route::controller(ClientNoteController::class)->group(function () {
            Route::post('/proprietes/{propriete}/notes', 'store');
            Route::put('/notes/{note}', 'update');
            Route::delete('/notes/{note}', 'destroy');
        });
    });

    /*
    |----------------------------------------------------------------------
    | BAILLEUR (role:bailleur)
    |----------------------------------------------------------------------
    */
    Route::middleware('role:bailleur')->prefix('bailleur')->group(function () {
        Route::prefix('proprietes')->controller(BailleurProprieteController::class)->group(function () {
            Route::get('/', 'index');
            Route::get('{propriete}', 'show');
        });

        Route::prefix('contrats')->controller(BailleurContratController::class)->group(function () {
            Route::get('/', 'index');

            Route::prefix('{contrat}')->group(function () {
                Route::get('/', 'show');
                Route::get('paiements', 'paiements');
                Route::get('telecharger', 'telecharger');
                Route::get('paiements/{paiement}/recu', 'recuPaiement');
            });
        });

        Route::get('/revenus', [RevenuController::class, 'index']);
    });

    /*
    |----------------------------------------------------------------------
    | ADMIN (role:admin)
    |----------------------------------------------------------------------
    */
    Route::middleware('role:admin')->prefix('admin')->group(function () {

        Route::prefix('bailleurs')->controller(AdminBailleurController::class)->group(function () {
            Route::post('/', 'store');
            Route::get('/', 'index');
            Route::get('{bailleur}', 'show');
            Route::put('{bailleur}', 'update');
            Route::patch('{bailleur}/desactiver', 'desactiver');
        });

        Route::prefix('clients')->controller(AdminClientController::class)->group(function () {
            Route::get('/', 'index');
            Route::get('{client}', 'show');
            Route::patch('{client}/desactiver', 'desactiver');
            Route::patch('{client}/activer', 'activer');
        });

        Route::prefix('proprietes')->controller(AdminProprieteController::class)->group(function () {
            Route::post('/', 'store');

            Route::prefix('{propriete}')->group(function () {
                Route::put('/', 'update');
                Route::delete('/', 'destroy');
                Route::put('equipements', 'syncEquipements');
            });
        });

        Route::controller(ImageProprieteController::class)->group(function () {
            Route::post('/proprietes/{propriete}/images', 'store');
            Route::delete('/images/{image}', 'destroy');
            Route::patch('/images/{image}/principale', 'definirPrincipale');
        });

        Route::prefix('reservations')->controller(AdminReservationController::class)->group(function () {
            Route::get('/', 'index');
            Route::prefix('{reservation}')->group(function () {
                Route::patch('confirmer', 'confirmer');
                Route::patch('annuler', 'annuler');
                Route::post('contrat', 'creerContrat');
            });
        });

        Route::prefix('contrats')->controller(AdminContratController::class)->group(function () {
            Route::get('/', 'index');
            Route::prefix('{contrat}')->group(function () {
                Route::get('/', 'show');
                Route::get('telecharger', 'telecharger');
                Route::patch('resilier', 'resilier');
            });
        });

        Route::controller(AdminPaiementController::class)->group(function () {
            Route::post('/contrats/{contrat}/paiements', 'store');
            Route::patch('/paiements/{paiement}/statut', 'updateStatut');
            Route::get('/paiements/{paiement}/recu', 'recu');
        });

        Route::prefix('typeproprietes')->controller(AdminTypeProprieteController::class)->group(function () {
            Route::post('/', 'store');
            Route::put('{typepropriete}', 'update');
            Route::delete('{typepropriete}', 'destroy');
        });

        Route::prefix('services')->controller(AdminServiceController::class)->group(function () {
            Route::post('/', 'store');
            Route::put('{service}', 'update');
        });

        Route::prefix('typecontrats')->controller(AdminTypeContratController::class)->group(function () {
            Route::post('/', 'store');
            Route::put('{typecontrat}', 'update');
        });

        Route::prefix('equipements')->controller(AdminEquipementController::class)->group(function () {
            Route::post('/', 'store');
            Route::put('{equipement}', 'update');
        });

        Route::prefix('demandes')->controller(AdminDemandeController::class)->group(function () {
            Route::get('/', 'index');
            Route::prefix('{demande}')->group(function () {
                Route::patch('traiter', 'traiter');
                Route::patch('fermer', 'fermer');
            });
        });

        Route::get('/dashboard/stats', [DashboardController::class, 'stats']);
    });
});
