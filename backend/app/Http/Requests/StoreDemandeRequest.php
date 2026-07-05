<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use OpenApi\Attributes as OA;

#[OA\Schema(
    schema: "StoreDemandeRequest",
    required: ["nom", "email", "telephone", "objet", "description"],
    properties: [
        new OA\Property(property: "nom", type: "string", maxLength: 150, example: "Dupont"),
        new OA\Property(property: "email", type: "string", format: "email", maxLength: 255, example: "jean.dupont@example.com"),
        new OA\Property(property: "telephone", type: "string", maxLength: 30, example: "+33612345678"),
        new OA\Property(property: "objet", type: "string", maxLength: 200, example: "Demande d'information"),
        new OA\Property(property: "description", type: "string", maxLength: 2000, example: "Je souhaite avoir plus de détails sur la villa...")
    ]
)]
class StoreDemandeRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'nom'         => ['required', 'string', 'max:150'],
            'email'       => ['required', 'email', 'max:255'],
            'telephone'   => ['required', 'string', 'max:30'],
            'objet'       => ['required', 'string', 'max:200'],
            'description' => ['required', 'string', 'max:2000'],
        ];
    }
}
