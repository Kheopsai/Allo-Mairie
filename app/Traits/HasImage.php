<?php

namespace App\Traits;

use Exception;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Intervention\Image\ImageManagerStatic as Image;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

trait HasImage
{
    public bool $updatedMedia = false;

    public function media()
    {
        return $this->morphMany(Media::class, 'model');
    }

    /**
     * @throws Exception
     */
    public function addImage($images, $collection = 'images'): void
    {
        foreach ($images as $image) {

            $path = $image['path'];
            if (! File::exists($path)) {
                throw new Exception("File does not exist: {$path}");
            }

            $fileSize = File::size($path);
            if ($fileSize > config('media.max_file_size', 10 * 1024 * 1024)) {
                throw new Exception("File is too big: {$path}");
            }

            $fileName = $this->generateFileName($path);
            $disk = config('filesystems.default');

            Storage::disk($disk)->putFileAs($collection, $path, $fileName);

            $thumbnail = Image::make($path)->rotate(90)->fit(100, 100);
            $thumbnailName = 'thumb_'.$fileName;
            Storage::disk($disk)->put($collection.'/thumbs/'.$thumbnailName, $thumbnail->stream());

            $this->media()->create([
                'collection_name' => $collection,
                'name' => $fileName,
                'file_name' => $fileName,
                'mime_type' => File::mimeType($path),
                'disk' => $disk,
                'size' => $fileSize,
                'manipulations' => ['thumb' => $thumbnailName],
                'custom_properties' => [],
                'responsive_images' => [],
                'uuid' => (string) Str::uuid(),
                'conversions_disk' => $disk,
                'generated_conversions' => ['thumb' => true],
                'order_column' => null,
            ]);
        }
    }

    /**
     * @throws Exception
     */
    public function syncImage($images, $collection = 'images'): void
    {
        $this->clearMediaCollection($collection);
        foreach ($images as $image) {
            $this->addImage([$image], $collection);
        }
    }

    public function clearMediaCollection($collection = 'images'): void
    {
        $media = $this->media()->where('collection_name', $collection)->get();
        foreach ($media as $item) {
            Storage::disk($item->disk)->delete($collection.'/'.$item->file_name);
            if (isset($item->manipulations['thumb'])) {
                Storage::disk($item->disk)->delete($collection.'/thumbs/'.$item->manipulations['thumb']);
            }
            $item->delete();
        }
    }

    public function generateFileName(string $path): string
    {
        $extension = File::extension($path);
        $uid = Str::uuid()->toString();

        return "{$uid}.{$extension}";
    }

    public function hasMedia(string $collection = 'images'): bool
    {
        return $this->media()->where('collection_name', $collection)->exists();
    }

    public function getFirstMediaUrl(string $collection = 'images', ?string $conversion = null): ?string
    {
        $media = $this->media()->where('collection_name', $collection)->first();
        if ($media && Storage::disk($media->disk)->exists($media->file_name)) {
            return Storage::disk($media->disk)->url($media->file_name);
        }

        return null;
    }

    public function bindToDropzone($collection = 'images'): array
    {
        $mediaItems = $this->media()->where('collection_name', $collection)->get();

        $dropzoneMedia = $mediaItems->map(function ($media) {
            $isPreviewable = in_array($media->mime_type, ['image/jpeg', 'image/png', 'image/gif', 'video/mp4']);

            return [
                'tmpFilename' => $media->file_name,
                'name' => $media->name,
                'extension' => pathinfo($media->file_name, PATHINFO_EXTENSION),
                'path' => Storage::disk($media->disk)->path($media->file_name),
                'temporaryUrl' => $isPreviewable ? Storage::disk($media->disk)->url($media->file_name) : null,
                'size' => $media->size,
            ];
        });

        return $dropzoneMedia->toArray();
    }

    public function updatedMedia(): void
    {
        $this->updatedMedia = true;
    }

    /**
     * @throws Exception
     */
    public function sync(Model $model, string $collection): void
    {
        if ($this->updatedMedia && $this->media()->count() > 0) {
            if (method_exists($model, 'addImage')) {
                $mediaData = $this->media()->get()->map(function ($media) {
                    return [
                        'path' => Storage::disk($media->disk)->path($media->file_name),
                    ];
                })->toArray();
                $model->addImage($mediaData, $collection);
            } else {
                throw new Exception('Model does not have the addImage method.');
            }
        } else {
            if (method_exists($model, 'clearMediaCollection')) {
                $model->clearMediaCollection($collection);
            } else {
                throw new Exception('Model does not have the clearMediaCollection method.');
            }
        }
    }

    public function image($collection = 'images')
    {
        return $this->media()->where('collection_name', $collection)->first();
    }

    public function images($collection = 'images'): Collection
    {
        return $this->media()->where('collection_name', $collection)->get();
    }

    public function hasImage(): bool
    {
        return $this->image() !== null;
    }

    public function hasImages(): bool
    {
        return $this->images()->count() > 0;
    }
}
