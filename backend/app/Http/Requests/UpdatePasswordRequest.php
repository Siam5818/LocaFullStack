<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Password;
use OpenApi\Attributes as OA;

#[OA\Schema(
    schema: "UpdatePasswordRequest",
    required: ["current_password", "password", "password_confirmation"],
    properties: [
        new OA\Property(property: "current_password", type: "string", format: "password", example: "AncienM0tDeP@sse1"),
        new OA\Property(property: "password", type: "string", format: "password", minLength: 8, example: "NouveauM0tDeP@sse2"),
        new OA\Property(property: "password_confirmation", type: "string", format: "password", example: "NouveauM0tDeP@sse2")
    ]
)]
class UpdatePasswordRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }


    public function rules(): array
    {
        return [
            'current_password' => ['required', 'string'],
            'password'          => ['required', 'confirmed', Password::min(8)->mixedCase()->numbers()],
        ];
    }
}
