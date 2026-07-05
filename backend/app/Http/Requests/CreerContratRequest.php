<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use OpenApi\Attributes as OA;

#[OA\Schema(
    schema: "CreerContratRequest",
    required: ["typecontrat_id", "date_debut", "montant_total"],
    properties: [
        new OA\Property(property: "typecontrat_id", type: "integer", example: 1),
        new OA\Property(property: "date_debut", type: "string", format: "date", example: "2026-07-01"),
        new OA\Property(property: "date_fin", type: "string", format: "date", nullable: true, example: "2027-07-01"),
        new OA\Property(property: "montant_total", type: "number", format: "float", example: 1200.50),
        new OA\Property(property: "mode_paiement_vente", type: "string", enum: ["unique", "echelonne"], nullable: true, example: "unique")
    ]
)]
class CreerContratRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'typecontrat_id'       => ['required', 'integer', 'exists:typecontrats,id'],
            'date_debut'           => ['required', 'date'],
            'date_fin'             => ['nullable', 'date', 'after:date_debut'],
            'montant_total'        => ['required', 'numeric', 'min:0'],
            'mode_paiement_vente'  => ['nullable', 'in:unique,echelonne'],
        ];
    }
}
