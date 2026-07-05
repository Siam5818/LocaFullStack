<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Password;
use OpenApi\Attributes as OA;

#[OA\Schema(
    schema: "RegisterRequest",
    required: ["nom", "prenom", "email", "telephone", "password", "password_confirmation"],
    properties: [
        new OA\Property(property: "nom", type: "string", maxLength: 100, example: "Dupont"),
        new OA\Property(property: "prenom", type: "string", maxLength: 100, example: "Jean"),
        new OA\Property(property: "email", type: "string", format: "email", maxLength: 255, example: "jean.dupont@example.com"),
        new OA\Property(property: "telephone", type: "string", maxLength: 30, example: "+33612345678"),
        new OA\Property(property: "password", type: "string", format: "password", minLength: 8, description: "Doit contenir des lettres majuscules/minuscules et des chiffres", example: "Jean1234"),
        new OA\Property(property: "password_confirmation", type: "string", format: "password", example: "Jean1234")
    ]
)]
class RegisterRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'nom'       => ['required', 'string', 'max:100'],
            'prenom'    => ['required', 'string', 'max:100'],
            'email'     => ['required', 'email', 'max:255', 'unique:personnes,email'],
            'telephone' => ['required', 'string', 'max:30'],
            'password'  => ['required', 'confirmed', Password::min(8)->mixedCase()->numbers()],
        ];
    }

    public function messages(): array
    {
        return [
            'email.unique'       => 'Cette adresse e-mail est déjà utilisée.',
            'password.confirmed' => 'La confirmation du mot de passe ne correspond pas.',
        ];
    }
}
