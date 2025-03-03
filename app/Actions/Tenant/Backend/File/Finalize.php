<?php

namespace App\Actions\Tenant\Backend\File;

use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use League\Flysystem\Visibility;
use Lorisleiva\Actions\Concerns\AsAction;
use RuntimeException;

class Finalize
{
    use AsAction;

    private const string CHUNK_DISK = 'local';

    private const string FINAL_DISK = 'local';

    private const string CHUNK_PATH_PREFIX = 'chunks';

    private const string FINAL_DIRECTORY = 'uploads';

    public function handle(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'uploadId' => 'required|string',
            'fileName' => 'required|string',
            'totalChunks' => 'required|integer|min:1',
        ]);

        $uploadId = $validated['uploadId'];
        $fileName = $this->sanitizeFilename($validated['fileName']);
        $totalChunks = (int) $validated['totalChunks'];

        try {
            $chunkDirectory = $this->getChunkDirectory($uploadId);

            if (! $this->validateChunks($chunkDirectory, $totalChunks)) {
                return response()->json(['error' => 'Incomplete upload'], 400);
            }

            $finalPath = $this->generateFinalPath($fileName);

            $this->combineChunks(
                chunkDirectory: $chunkDirectory,
                totalChunks: $totalChunks,
                outputPath: $finalPath
            );

            $this->cleanupChunks($chunkDirectory);

            return response()->json([
                'path' => Storage::disk(self::FINAL_DISK)->path($finalPath),
            ]);

        } catch (Exception $e) {
            Log::error('File finalization failed', [
                'uploadId' => $uploadId,
                'error' => $e->getMessage(),
            ]);

            return response()->json([
                'error' => 'File processing failed. Please try again.',
            ], 500);
        }
    }

    private function sanitizeFilename(string $fileName): string
    {
        return Str::ascii(pathinfo($fileName, PATHINFO_FILENAME)).'.'.pathinfo($fileName, PATHINFO_EXTENSION);
    }

    private function getChunkDirectory(string $uploadId): string
    {
        return self::CHUNK_PATH_PREFIX.'/'.$uploadId;
    }

    private function validateChunks(string $chunkDirectory, int $totalChunks): bool
    {
        $chunkDisk = Storage::disk(self::CHUNK_DISK);

        if (! $chunkDisk->exists($chunkDirectory)) {
            return false;
        }

        $foundChunks = count($chunkDisk->files($chunkDirectory));

        return $foundChunks === $totalChunks;
    }

    private function generateFinalPath(string $fileName): string
    {
        return self::FINAL_DIRECTORY.'/'.Str::uuid().'_'.$fileName;
    }

    private function combineChunks(string $chunkDirectory, int $totalChunks, string $outputPath): void
    {
        $chunkDisk = Storage::disk(self::CHUNK_DISK);
        $outputDisk = Storage::disk(self::FINAL_DISK);

        $this->createDestinationDirectory(dirname($outputPath));

        $outputStream = fopen($outputDisk->path($outputPath), 'wb');

        if (! is_resource($outputStream)) {
            throw new RuntimeException("Failed to create output file: {$outputPath}");
        }

        try {
            for ($i = 0; $i < $totalChunks; $i++) {
                $chunkPath = $chunkDirectory.'/'.$i.'.part';

                if (! $chunkDisk->exists($chunkPath)) {
                    throw new RuntimeException("Missing chunk: $chunkPath");
                }

                $chunkStream = $chunkDisk->readStream($chunkPath);

                if (! is_resource($chunkStream)) {
                    throw new RuntimeException("Invalid chunk stream: $chunkPath");
                }

                stream_copy_to_stream($chunkStream, $outputStream);
                fclose($chunkStream);
            }
        } finally {
            fclose($outputStream);
        }
    }

    private function createDestinationDirectory(string $directory): void
    {
        $outputDisk = Storage::disk(self::FINAL_DISK);

        if (! $outputDisk->exists($directory)) {
            $outputDisk->makeDirectory($directory);
            $outputDisk->setVisibility($directory, Visibility::PUBLIC);
        }
    }

    private function cleanupChunks(string $chunkDirectory): void
    {
        Storage::disk(self::CHUNK_DISK)->deleteDirectory($chunkDirectory);
    }
}
