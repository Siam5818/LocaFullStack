<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use OpenApi\Attributes as OA;

#[OA\Schema(
    schema: "FilterProprieteRequest",
    properties: [
        new OA\Property(property: "ville", type: "string", maxLength: 100, example: "Paris"),
        new OA\Property(property: "prix_min", type: "number", minimum: 0, example: 300),
        new OA\Property(property: "prix_max", type: "number", minimum: 0, example: 1500),
        new OA\Property(property: "type_id", type: "integer", example: 2),
        new OA\Property(property: "service_id", type: "integer", example: 1),
        new OA\Property(property: "piece_min", type: "integer", minimum: 0, example: 2),
        new OA\Property(property: "tri", type: "string", enum: ["recent", "prix_asc", "prix_desc"], example: "recent"),
        new OA\Property(property: "cursor", type: "string", nullable: true)
    ]
)]
class FilterProprieteRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // Autoriser toutes les requêtes pour ce formulaire
    }

    public function rules(): array
    {
        return [
            'ville'      => ['nullable', 'string', 'max:100'],
            'prix_min'   => ['nullable', 'numeric', 'min:0'],
            'prix_max'   => ['nullable', 'numeric', 'min:0'],
            'type_id'    => ['nullable', 'integer', 'exists:typeproprietes,id'],
            'service_id' => ['nullable', 'integer', 'exists:services,id'],
            'piece_min'  => ['nullable', 'integer', 'min:0'],
            'tri'        => ['nullable', 'in:recent,prix_asc,prix_desc'],
            'cursor'     => ['nullable', 'string'],
        ];
    }
}
