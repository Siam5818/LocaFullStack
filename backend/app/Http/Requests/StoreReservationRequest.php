<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use OpenApi\Attributes as OA;

#[OA\Schema(
    schema: "StoreReservationRequest",
    required: ["propriete_id"],
    properties: [
        new OA\Property(property: "propriete_id", type: "integer", example: 12)
    ]
)]
class StoreReservationRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'propriete_id' => ['required', 'integer', 'exists:proprietes,id'],
        ];
    }
}
