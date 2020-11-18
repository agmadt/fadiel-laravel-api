<?php

namespace App\Api\V1\Controllers;

use App\Models\Media;
use Illuminate\Support\Str;
use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Storage;
use App\Api\V1\Requests\StoreMediaRequest;

class MediaController extends Controller
{
    public function store(StoreMediaRequest $request): JsonResponse
    {
        $file = $request->file('media');
        $fullName = Str::uuid() . '.' . $file->extension();

        Storage::put($fullName, $file->get(), 'public');

        $url = Storage::url($fullName);

        $media = Media::create([
            'filename' => $fullName,
            'location' => $url,
            'type' => $file->getClientMimeType()
        ]);

        return response()->json([
            'id' => $media->id,
            'location' => $media->location
        ]);
    }
}
