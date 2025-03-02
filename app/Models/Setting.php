<?php

namespace App\Models;

use App\Traits\HasImage;
use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Stancl\Tenancy\Database\Concerns\CentralConnection;
use Stancl\VirtualColumn\VirtualColumn;

class Setting extends Model implements HasMedia
{
    use CentralConnection;
    // use HasImage;
    use InteractsWithMedia;
    use VirtualColumn;

    protected $guarded = [];

    public static function getCustomColumns(): array
    {
        return [
            'id',
            'tenant_id',
            'created_at',
            'updated_at',
        ];
    }


    public function setConnection($name): Setting
    {
        return parent::setConnection($this->getConnectionName());
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
                'path' => $media->getPath(),
                'temporaryUrl' => $isPreviewable ? $media->original_url : null,
                'size' => $media->size,
            ];
        });

        return $dropzoneMedia->toArray();
    }
}
