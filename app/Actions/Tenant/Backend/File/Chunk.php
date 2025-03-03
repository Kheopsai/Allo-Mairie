<?php

namespace App\Actions\Tenant\Backend\File;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Lorisleiva\Actions\Concerns\AsAction;

class Chunk
{
    use AsAction;

    public function handle(Request $request): JsonResponse
    {
        $request->validate([
            'file' => 'required|file',
            'uploadId' => 'required|string',
            'chunkNumber' => 'required|numeric',
            'totalChunks' => 'required|numeric',
            'fileName' => 'required|string',
            'inputName' => 'required|string',
        ]);

        $chunk = $request->file('file');
        $chunkNumber = $request->input('chunkNumber');
        $uploadId = $request->input('uploadId');
        $fileName = Str::ascii($request->input('fileName'));

        $path = Storage::disk('local')->path("chunks/{$uploadId}");
        if (! file_exists($path)) {
            mkdir($path, 0755, true);
        }

        $chunk->move($path, "{$chunkNumber}.part");

        return response()->json(['success' => true]);
    }
}
