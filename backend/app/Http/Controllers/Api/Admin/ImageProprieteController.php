<?php
/* cspell:disable */

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\ImagePropriete;
use App\Models\Propriete;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ImageProprieteController extends Controller
{
    public function store(Request $request, Propriete $propriete): JsonResponse
    {
        $request->validate([
            'images'   => ['required', 'array', 'max:10'],
            'images.*' => ['image', 'mimes:jpeg,png,jpg,gif,webp', 'max:5120'], // 5 Mo max par image
        ]);

        $images = [];
        $existingCount = $propriete->images()->count();

        foreach ($request->file('images') as $index => $file) {
            $chemin = $file->store("proprietes/{$propriete->id}", 'public');

            $images[] = ImagePropriete::create([
                'propriete_id'  => $propriete->id,
                'chemin'        => $chemin,
                'is_principale' => $existingCount === 0 && $index === 0,
                'ordre'         => $existingCount + $index,
            ]);
        }

        return response()->json([
            'message' => count($images) . ' image(s) ajoutée(s).',
            'images'  => $images,
        ], 201);
    }

    public function destroy(ImagePropriete $image): JsonResponse
    {
        Storage::disk('public')->delete($image->chemin);
        $image->delete();
        return response()->json(['message' => 'Image supprimée.']);
    }

    public function definirPrincipale(ImagePropriete $image): JsonResponse
    {
        ImagePropriete::where('propriete_id', $image->propriete_id)
            ->update(['is_principale' => false]);
        $image->update(['is_principale' => true]);

        return response()->json(['message' => 'Image principale mise à jour.', 'image' => $image]);
    }
}
