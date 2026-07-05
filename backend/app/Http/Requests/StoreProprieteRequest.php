<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use OpenApi\Attributes as OA;

#[OA\Schema(
    schema: "StoreProprieteRequest",
    required: ["nom", "ville", "nombre_piece", "dimension", "description", "cout", "typepropriete_id", "bailleur_id", "service_id"],
    properties: [
        new OA\Property(property: "nom", type: "string", maxLength: 200, example: "Appartement F3 Lumineux"),
        new OA\Property(property: "rue", type: "string", maxLength: 255, nullable: true, example: "12 Rue des Lilas"),
        new OA\Property(property: "quartier", type: "string", maxLength: 100, nullable: true, example: "Centre-ville"),
        new OA\Property(property: "ville", type: "string", maxLength: 100, example: "Paris"),
        new OA\Property(property: "pays", type: "string", maxLength: 100, nullable: true, example: "France"),
        new OA\Property(property: "latitude", type: "number", format: "float", minimum: -90, maximum: 90, nullable: true, example: 48.8566),
        new OA\Property(property: "longitude", type: "number", format: "float", minimum: -180, maximum: 180, nullable: true, example: 2.3522),
        new OA\Property(property: "nombre_piece", type: "integer", minimum: 0, example: 3),
        new OA\Property(property: "dimension", type: "integer", minimum: 1, example: 65),
        new OA\Property(property: "description", type: "string", example: "Bel appartement refait à neuf avec balcon..."),
        new OA\Property(property: "cout", type: "number", format: "float", minimum: 0, example: 850.00),
        new OA\Property(property: "typepropriete_id", type: "integer", example: 1),
        new OA\Property(property: "bailleur_id", type: "integer", example: 4),
        new OA\Property(property: "service_id", type: "integer", example: 2),
        new OA\Property(
            property: "equipement_ids",
            type: "array",
            items: new OA\Items(type: "integer"),
            nullable: true,
            example: [1, 3, 5]
        )
    ]
)]
class StoreProprieteRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'nom'              => ['required', 'string', 'max:200'],
            'rue'              => ['nullable', 'string', 'max:255'],
            'quartier'         => ['nullable', 'string', 'max:100'],
            'ville'            => ['required', 'string', 'max:100'],
            'pays'             => ['nullable', 'string', 'max:100'],
            'latitude'         => ['nullable', 'numeric', 'between:-90,90'],
            'longitude'        => ['nullable', 'numeric', 'between:-180,180'],
            'nombre_piece'     => ['required', 'integer', 'min:0'],
            'dimension'        => ['required', 'integer', 'min:1'],
            'description'      => ['required', 'string'],
            'cout'             => ['required', 'numeric', 'min:0'],
            'typepropriete_id' => ['required', 'integer', 'exists:typeproprietes,id'],
            'bailleur_id'      => ['required', 'integer', 'exists:bailleurs,id'],
            'service_id'       => ['required', 'integer', 'exists:services,id'],
            'equipement_ids'   => ['nullable', 'array'],
            'equipement_ids.*' => ['integer', 'exists:equipements,id'],
        ];
    }
}
