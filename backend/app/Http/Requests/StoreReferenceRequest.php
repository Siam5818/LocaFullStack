<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use OpenApi\Attributes as OA;

#[OA\Schema(
    schema: "StoreReferenceRequest",
    required: ["nom"],
    properties: [
        new OA\Property(property: "nom", type: "string", maxLength: 150, example: "Climatisation"),
        new OA\Property(property: "description", type: "string", maxLength: 255, nullable: true, example: "Système de refroidissement d'air"),
        new OA\Property(property: "icone", type: "string", maxLength: 50, nullable: true, example: "fa-snowflake")
    ]
)]
class StoreReferenceRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'nom'         => ['required', 'string', 'max:150'],
            'description' => ['nullable', 'string', 'max:255'],
            'icone'       => ['nullable', 'string', 'max:50'],
        ];
    }
}
