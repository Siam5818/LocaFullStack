<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use OpenApi\Attributes as OA;

#[OA\Schema(
    schema: "UpdateProfilRequest",
    properties: [
        new OA\Property(property: "nom", type: "string", maxLength: 100, nullable: true, example: "Gomez"),
        new OA\Property(property: "prenom", type: "string", maxLength: 100, nullable: true, example: "Carlos"),
        new OA\Property(property: "telephone", type: "string", maxLength: 30, nullable: true, example: "+33699887766")
    ]
)]
class UpdateProfilRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'nom'       => ['sometimes', 'string', 'max:100'],
            'prenom'    => ['sometimes', 'string', 'max:100'],
            'telephone' => ['sometimes', 'string', 'max:30'],
        ];
    }
}
