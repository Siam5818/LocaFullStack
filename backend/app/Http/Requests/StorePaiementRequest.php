<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use OpenApi\Attributes as OA;

#[OA\Schema(
    schema: "StorePaiementRequest",
    required: ["montant", "date_echeance"],
    properties: [
        new OA\Property(property: "montant", type: "number", format: "float", minimum: 0, example: 500.00),
        new OA\Property(property: "date_echeance", type: "string", format: "date", example: "2026-07-05")
    ]
)]
class StorePaiementRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'montant'       => ['required', 'numeric', 'min:0'],
            'date_echeance' => ['required', 'date'],
        ];
    }
}
