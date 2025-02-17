<?php

namespace App\Traits;

use App\Models\File;
use App\Services\FileSystem\FileAdder;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Relations\MorphOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use InvalidArgumentException;
use RuntimeException;
use Symfony\Component\HttpFoundation\File\File as SymfonyFile;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Throwable;

trait HasFiles
{
    public static function bootHasFiles(): void
    {
        static::deleting(function (Model $model) {
            if (in_array(SoftDeletes::class, class_uses_recursive($model))) {
                return;
            }

            foreach ($model->files as $file) {
                $file->delete();
            }
        });
    }

    /**
     * Define a polymorphic one-to-many relationship with File model.
     */
    public function files(): MorphMany
    {
        return $this->morphMany(config('fileable.file_model', File::class), 'fileable');
    }

    /**
     * Define a polymorphic one-to-one relationship with File model.
     */
    public function file(): MorphOne
    {
        return $this->morphOne(config('fileable.file_model', File::class), 'fileable');
    }

    /**
     * Add a new file to the model.
     */
    public function addFile(SymfonyFile|string|UploadedFile $file): FileAdder
    {
        return app(FileAdder::class)
            ->add($file)
            ->to($this);
    }

    /**
     * Add a file from an HTTP request.
     */
    public function addFileFromRequest(Request $request, string $key): FileAdder
    {
        $uploadedFile = $request->file($key);
        if (! $uploadedFile) {
            throw new InvalidArgumentException("No file found in the request for key: {$key}");
        }

        return $this->addFile($uploadedFile);
    }

    /**
     * Add a file from the storage disk.
     */
    public function addFileFromDisk(string $path, ?string $disk = null): FileAdder
    {
        $disk = $disk ?? config('fileable.disk', 'default');
        $stream = Storage::disk($disk)->readStream($path);
        if (! $stream) {
            throw new RuntimeException("Unable to read file from disk: {$path}");
        }

        return $this->addFile($stream)
            ->as(basename($path))
            ->on($disk);
    }

    /**
     * Add a file from a remote URL.
     */
    public function addFileFromUrl(string $url): FileAdder
    {
        $stream = @fopen($url, 'r');
        if (! $stream) {
            throw new RuntimeException("Unable to open URL: {$url}");
        }

        $filename = urldecode(basename(parse_url($url, PHP_URL_PATH)));
        $filename = $filename ?: 'downloaded_file_'.Str::random(10);

        return $this->addFile($stream)
            ->as($filename)
            ->on(config('fileable.disk', 'default'));
    }

    /**
     * Retrieve all files associated with the model.
     */
    public function getAllFiles(): Collection
    {
        return $this->files;
    }

    /**
     * Retrieve a specific file by its ID.
     */
    public function getFileById(int $fileId): ?File
    {
        return $this->files()->find($fileId);
    }

    /**
     * Retrieve the first file associated with the model.
     */
    public function getFirstFile(): ?File
    {
        return $this->files()->first();
    }

    /**
     * Retrieve the latest file associated with the model.
     */
    public function getLatestFile(): ?File
    {
        return $this->files()->latest()->first();
    }

    /**
     * Update an existing file.
     *
     * @throws Throwable
     */
    public function updateFile(int $fileId, SymfonyFile|string|UploadedFile $newFile): File
    {
        $file = $this->getFileById($fileId);
        if (! $file) {
            throw new RuntimeException("File with ID {$fileId} not found.");
        }

        $file->delete();

        return $this->addFile($newFile)
            ->save();
    }

    /**
     * Remove a specific file by its ID.
     */
    public function removeFile(int $fileId): bool
    {
        $file = $this->getFileById($fileId);
        if (! $file) {
            throw new RuntimeException("File with ID {$fileId} not found.");
        }

        return $file->delete();
    }

    /**
     * Remove all files associated with the model.
     */
    public function removeAllFiles(): bool
    {
        $success = true;
        foreach ($this->files as $file) {
            if (! $file->deleteFile()) {
                $success = false;
            }
        }

        return $success;
    }

    /**
     * Check if a directory is empty.
     */
    protected function isDirectoryEmpty(string $disk, string $directory): bool
    {
        return empty(Storage::disk($disk)->files($directory)) &&
            empty(Storage::disk($disk)->directories($directory));
    }

    /**
     * Paginate the associated files.
     */
    public function paginateFiles(int $perPage = 15, array|string $columns = ['*'], string $pageName = 'page', ?int $page = null): LengthAwarePaginator
    {
        return $this->files()->paginate($perPage, $columns, $pageName, $page);
    }

    /**
     * Check if the model has any files.
     */
    public function hasFiles(): bool
    {
        return $this->files()->exists();
    }

    /**
     * Get the count of associated files.
     */
    public function filesCount(): int
    {
        return $this->files()->count();
    }

    /**
     * Attach multiple files to the model.
     */
    public function attachFiles(iterable $files): void
    {
        foreach ($files as $file) {
            $this->addFile($file)->save();
        }
    }

    /**
     * Detach all files from the model without deleting them.
     */
    public function detachAllFiles(): void
    {
        $this->files()->update(['fileable_id' => null, 'fileable_type' => null]);
    }

    /**
     * Replace existing files with new files.
     */
    public function replaceFiles(iterable $newFiles): void
    {
        $this->removeAllFiles();
        $this->attachFiles($newFiles);
    }

    /**
     * Sync files with the model, adding new ones and removing missing ones.
     */
    public function syncFiles(iterable $files): void
    {
        $existingFileIds = $this->files()->pluck('id')->toArray();
        $newFileIds = [];

        foreach ($files as $file) {
            if ($file instanceof File) {
                $newFileIds[] = $file->id;
            } else {
                $addedFile = $this->addFile($file)->save();
                $newFileIds[] = $addedFile->id;
            }
        }

        $filesToRemove = array_diff($existingFileIds, $newFileIds);
        if (! empty($filesToRemove)) {
            $this->files()->whereIn('id', $filesToRemove)->delete();
        }
    }

    /**
     * Retrieve files with specific conditions.
     */
    public function getFilteredFiles(?callable $callback = null): Collection
    {
        $query = $this->files();

        if ($callback) {
            $callback($query);
        }

        return $query->get();
    }

    /**
     * Check if a file with a specific filename exists.
     */
    public function fileExists(string $filename): bool
    {
        return $this->files()->where('filename', $filename)->exists();
    }

    /**
     * Retrieve files by MIME type.
     */
    public function getFilesByMimeType(string $mimeType): Collection
    {
        return $this->files()->where('mimetype', $mimeType)->get();
    }

    /**
     * Retrieve files within a specific size range.
     */
    public function getFilesBySizeRange(int $minSize, int $maxSize): Collection
    {
        return $this->files()
            ->whereBetween('size', [$minSize, $maxSize])
            ->get();
    }

    /**
     * Retrieve files uploaded within a specific date range.
     */
    public function getFilesByDateRange(string $startDate, string $endDate): Collection
    {
        return $this->files()
            ->whereBetween('created_at', [$startDate, $endDate])
            ->get();
    }

    /**
     * Get the primary file (e.g., featured image).
     */
    public function getPrimaryFile(): ?File
    {
        return $this->file()->first();
    }

    /**
     * Set a primary file for the model.
     *
     * @throws Throwable
     */
    public function setPrimaryFile(SymfonyFile|string|UploadedFile $file): File
    {
        $existingPrimary = $this->getPrimaryFile();
        $existingPrimary?->delete();

        return $this->addFile($file)->save();
    }

    /**
     * Replace the primary file with a new file.
     *
     * @throws Throwable
     */
    public function replacePrimaryFile(SymfonyFile|string|UploadedFile $file): File
    {
        return $this->setPrimaryFile($file);
    }

    public function downloadFile(int $fileId): StreamedResponse
    {
        $file = $this->getFileById($fileId);
        if (! $file) {
            abort(404, 'File not found.');
        }

        return Storage::disk($file->disk)->download($file->filepath, $file->filename);
    }

    public function streamFile(int $fileId): StreamedResponse
    {
        $file = $this->getFileById($fileId);
        if (! $file) {
            abort(404, 'File not found.');
        }

        return Storage::disk($file->disk)->response($file->filepath);
    }

    /**
     * Generate a temporary URL for a file.
     */
    public function temporaryUrl(int $fileId, \DateTimeInterface|\DateInterval|int $expiration): string
    {
        $file = $this->getFileById($fileId);
        if (! $file) {
            throw new RuntimeException('File not found.');
        }

        return Storage::disk($file->disk)->temporaryUrl($file->filepath, $expiration);
    }

    /**
     * Get the total size of all associated files.
     */
    public function getTotalFilesSize(): int
    {
        return $this->files()->sum('size');
    }

    /**
     * Get the average size of associated files.
     */
    public function getAverageFileSize(): float
    {
        return $this->files()->avg('size') ?? 0.0;
    }

    /**
     * Get the largest file associated with the model.
     */
    public function getLargestFile(): ?File
    {
        return $this->files()->orderByDesc('size')->first();
    }

    /**
     * Get the smallest file associated with the model.
     */
    public function getSmallestFile(): ?File
    {
        return $this->files()->orderBy('size')->first();
    }

    /**
     * Get the most recently uploaded file.
     */
    public function getMostRecentFile(): ?File
    {
        return $this->files()->latest()->first();
    }

    /**
     * Get the oldest uploaded file.
     */
    public function getOldestFile(): ?File
    {
        return $this->files()->oldest()->first();
    }

    /**
     * Get files grouped by MIME type.
     */
    public function getFilesGroupedByMimeType(): array
    {
        return $this->files()->get()->groupBy('mimetype')->toArray();
    }

    /**
     * Get files with pagination.
     */
    public function getFilesPaginated(int $perPage = 15): LengthAwarePaginator
    {
        return $this->files()->paginate($perPage);
    }

    /**
     * Get files sorted by a specific attribute.
     */
    public function getFilesSortedBy(string $attribute, string $direction = 'asc'): Collection
    {
        return $this->files()->orderBy($attribute, $direction)->get();
    }

    /**
     * Check if the model has a file with the given filename.
     */
    public function hasFile(string $filename): bool
    {
        return $this->files()->where('filename', $filename)->exists();
    }

    /**
     * Retrieve files matching a specific pattern.
     */
    public function getFilesMatching(string $pattern): Collection
    {
        return $this->files()->where('filename', 'like', $pattern)->get();
    }
}
