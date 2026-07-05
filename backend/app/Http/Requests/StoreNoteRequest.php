<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use OpenApi\Attributes as OA;

#[OA\Schema(
    schema: "StoreNoteRequest",
    required: ["note"],
    properties: [
        new OA\Property(property: "note", type: "integer", minimum: 1, maximum: 5, example: 5),
        new OA\Property(property: "commentaire", type: "string", maxLength: 1000, nullable: true, example: "Excellent séjour, logement impeccable !")
    ]
)]
class StoreNoteRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'note'        => ['required', 'integer', 'min:1', 'max:5'],
            'commentaire' => ['nullable', 'string', 'max:1000'],
        ];
    }
}
