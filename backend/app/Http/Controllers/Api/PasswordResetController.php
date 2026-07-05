<?php
/* cspell:disable */

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\ForgotPasswordRequest;
use App\Http\Requests\ResetPasswordRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Hash;

class PasswordResetController extends Controller
{
    /**
     * Envoie le lien de réinitialisation par email.
     */
    public function forgotPassword(ForgotPasswordRequest $request): JsonResponse
    {
        $status = Password::broker('personnes')->sendResetLink(
            $request->only('email')
        );

        if ($status === Password::RESET_LINK_SENT) {
            return response()->json([
                'message' => 'Un lien de réinitialisation a été envoyé à votre adresse e-mail.',
            ]);
        }

        return response()->json([
            'message' => 'Impossible d\'envoyer le lien. Vérifiez votre adresse e-mail.',
        ], 422);
    }

    /**
     * Réinitialise le mot de passe avec le token reçu par email.
     */
    public function resetPassword(ResetPasswordRequest $request): JsonResponse
    {
        $status = Password::broker('personnes')->reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function ($personne, $password) {
                $personne->forceFill([
                    'password' => Hash::make($password),
                ])->save();

                // Révoque tous les tokens Sanctum existants pour forcer la reconnexion.
                $personne->tokens()->delete();
            }
        );

        if ($status === Password::PASSWORD_RESET) {
            return response()->json([
                'message' => 'Mot de passe réinitialisé avec succès. Vous pouvez vous connecter.',
            ]);
        }

        return response()->json([
            'message' => 'Le lien est invalide ou expiré. Demandez un nouveau lien.',
        ], 422);
    }
}
