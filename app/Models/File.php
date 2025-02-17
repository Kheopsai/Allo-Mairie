<?php

namespace App\Models;

use App\Interface\FileableInterface;
use App\Interface\FileInterface;
use Carbon\Carbon;
use Closure;
use Illuminate\Support\Str;
use Illuminate\Contracts\Database\Eloquent\Builder;
use Illuminate\Contracts\Support\Responsable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Symfony\Component\HttpFoundation\HeaderUtils;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\StreamedResponse;

class File extends Model implements FileInterface,Responsable
{
    protected $guarded = [];

    protected $appends = ['url'];

    protected $casts = [
        'size' => 'int',
        'meta' => 'json',
    ];

    protected $observables = [
        'storing',
        'stored',
    ];

    public function __construct(array $attributes = [])
    {
        if (! isset($this->connection)) {
            $this->setConnection(config('fileable.database_connection'));
        }

        if (! isset($this->table)) {
            $this->setTable(config('fileable.table_name'));
        }

        parent::__construct($attributes);
    }

    public static function booted(): void
    {
        static::deleting(function (self $file): bool {
            if (! $file->exists()) {
                return true;
            }

            return $file->delete();
        });
    }

    /**
     * Define the polymorphic relationship.
     */
    public function fileable(): MorphTo
    {
        return $this->morphTo();
    }

    /**
     * Scope to filter files by their fileable relationship.
     */
    public function scopeWhereFileable(Builder $query, FileableInterface $fileable): Builder
    {
        return $query->where(function (Builder $q) use ($fileable) {
            $q->where('fileable_type', $fileable->getMorphClass())
                ->where('fileable_id', $fileable->getKey());
        });
    }

    /**
     * Check if the file exists on the filesystem.
     */
    public function exists(): bool
    {
        return $this->exists && file_exists($this->path());
    }

    /**
     * Get the full filesystem path to the file.
     */
    public function path(): string
    {
        return storage_path('app/'.$this->filepath);
    }

    /**
     * Accessor for the display name attribute.
     */
    public function getDisplayNameAttribute(?string $value): string
    {
        return $value ?? Str::slug(pathinfo($this->filename, PATHINFO_FILENAME));
    }

    /**
     * Accessor for the URL attribute.
     */
    public function getUrlAttribute(): ?string
    {
        // Assumes a symbolic link exists from public/storage to storage/app
        return asset('storage/'.$this->filepath);
    }

    /**
     * Accessor for the modified_at attribute.
     */
    public function getModifiedAtAttribute(): ?Carbon
    {
        if (! file_exists($this->path())) {
            return null;
        }

        $timestamp = filemtime($this->path());

        if ($timestamp === false) {
            return null;
        }

        return Carbon::createFromTimestamp($timestamp);
    }

    /**
     * Convert the model to an HTTP response.
     */
    public function toResponse($request): Response
    {
        if ($request->expectsJson()) {
            return response()->json($this);
        }

        foreach ($request->getAcceptableContentTypes() as $acceptableContentType) {
            if ($this->isOfMimeType($acceptableContentType)) {
                return $this->response();
            }
        }

        return $this->download();
    }

    /**
     * Generate a streamed response of the file.
     */
    public function response(array $headers = []): StreamedResponse
    {
        return response()->stream(function (): void {
            $stream = $this->stream();

            if ($stream) {
                fpassthru($stream);
                fclose($stream);
            }
        }, 200, array_merge([
            'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
            'Content-Type' => $this->mimetype,
            'Content-Length' => $this->size,
        ], $headers));
    }

    /**
     * Generate a download response for the file.
     */
    public function download(): StreamedResponse
    {
        return $this->response([
            'Content-Disposition' => HeaderUtils::makeDisposition(
                HeaderUtils::DISPOSITION_ATTACHMENT,
                $this->filename,
                str_replace('%', '', Str::ascii($this->filename))
            ),
        ]);
    }

    /**
     * Delete the file from the filesystem.
     */
    public function deleteFile(): bool
    {
        return unlink($this->path());
    }

    /**
     * @return resource|null
     */
    public function stream()
    {
        if (! file_exists($this->path())) {
            return null;
        }

        return fopen($this->path(), 'rb');
    }

    /**
     * Check if the file's MIME type matches a given pattern.
     */
    public function isOfMimeType(string $pattern): bool
    {
        return Str::is($pattern, $this->mimetype);
    }

    /**
     * Store contents to the file.
     *
     * @param  string|resource  $contents
     */
    public function store($contents, array $options = []): bool
    {
        $fullPath = $this->path();
        $directory = dirname($fullPath);

        if (! is_dir($directory)) {
            if (! mkdir($directory, 0775, true) && ! is_dir($directory)) {
                return false;
            }
        }

        if (is_resource($contents)) {
            $handle = fopen($fullPath, 'wb');
            if ($handle === false) {
                return false;
            }

            while (! feof($contents)) {
                $buffer = fread($contents, 8192);
                if ($buffer === false) {
                    fclose($handle);

                    return false;
                }
                fwrite($handle, $buffer);
            }

            fclose($handle);

            return true;
        }

        return file_put_contents($fullPath, $contents) !== false;
    }

    /**
     * Register a callback to be executed before storing the model.
     */
    public static function storing(Closure $callback): void
    {
        static::registerModelEvent('storing', $callback);
    }

    /**
     * Register a callback to be executed after storing the model.
     */
    public static function stored(Closure $callback): void
    {
        static::registerModelEvent('stored', $callback);
    }
}
