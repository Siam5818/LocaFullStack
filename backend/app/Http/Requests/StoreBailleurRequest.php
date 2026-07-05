<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Password;
use OpenApi\Attributes as OA;

#[OA\Schema(
    schema: "StoreBailleurRequest",
    required: ["nom", "prenom", "email", "telephone", "password"],
    properties: [
        new OA\Property(property: "nom", type: "string", maxLength: 100, example: "Baillet"),
        new OA\Property(property: "prenom", type: "string", maxLength: 100, example: "Pierre"),
        new OA\Property(property: "email", type: "string", format: "email", maxLength: 255, example: "pierre.baillet@example.com"),
        new OA\Property(property: "telephone", type: "string", maxLength: 30, example: "+33699887766"),
        new OA\Property(property: "password", type: "string", format: "password", minLength: 8, example: "Pierre1234"),
        new OA\Property(property: "iban", type: "string", maxLength: 50, nullable: true, example: "FR763000..."),
        new OA\Property(property: "nom_banque", type: "string", maxLength: 100, nullable: true, example: "Crédit Agricole")
    ]
)]
class StoreBailleurRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'nom'        => ['required', 'string', 'max:100'],
            'prenom'     => ['required', 'string', 'max:100'],
            'email'      => ['required', 'email', 'max:255', 'unique:personnes,email'],
            'telephone'  => ['required', 'string', 'max:30'],
            'password'   => ['required', Password::min(8)->mixedCase()->numbers()],
            'iban'       => ['nullable', 'string', 'max:50'],
            'nom_banque' => ['nullable', 'string', 'max:100'],
        ];
    }
}
