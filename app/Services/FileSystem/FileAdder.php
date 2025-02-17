<?php

namespace App\Services\FileSystem;

use App\Interface\FileableInterface as FileableContract;
use App\Interface\FileInterface as FileContract;
use App\Models\File;
use Closure;
use Exception;
use finfo;
use Illuminate\Contracts\Filesystem\Factory as FilesystemFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Str;
use OutOfBoundsException;
use RuntimeException;
use Symfony\Component\HttpFoundation\File\File as SymfonyFile;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Mime\MimeTypes;
use Throwable;

class FileAdder
{
    protected FilesystemFactory $filesystem;

    protected FileContract|Model $file;

    protected FileableContract|Model $fileable;

    protected string|UploadedFile|SymfonyFile $originalFile;

    protected ?Closure $tap = null;

    protected bool $preserveOriginal = false;

    protected ?string $directory = null;

    public function __construct(FilesystemFactory $filesystem)
    {
        $this->filesystem = $filesystem;
        $this->file = app(config('fileable.file_model', File::class));
    }

    /**
     * Invoke the save method.
     *
     * @throws Throwable
     */
    public function __invoke(): FileContract
    {
        return $this->save();
    }

    /**
     * Associate the file with a fileable entity.
     */
    public function to(FileableContract|Model $fileable): self
    {
        $this->fileable = $fileable;

        return $this;
    }

    /**
     * Specify the disk to store the file.
     */
    public function on(string $disk): self
    {
        $this->file->disk = $disk;

        return $this;
    }

    /**
     * Specify the directory to store the file.
     */
    public function in(string $directory): self
    {
        $this->directory = rtrim($directory, '/');

        return $this;
    }

    /**
     * Add the original file to be stored.
     */
    public function add(SymfonyFile|string|UploadedFile $originalFile): self
    {
        $this->originalFile = $originalFile;

        return $this;
    }

    /**
     * Assign a specific filename to the stored file.
     */
    public function as(string $filename): self
    {
        $this->file->filename = $filename;

        return $this;
    }

    /**
     * Assign a display name to the file.
     */
    public function named(string $name): self
    {
        $this->file->display_name = $name;

        return $this;
    }

    /**
     * Attach metadata to the file.
     */
    public function with(array $meta): self
    {
        $this->file->meta = $meta;

        return $this;
    }

    /**
     * Provide a callback to modify the file before saving.
     */
    public function tap(Closure $callback): self
    {
        $this->tap = $callback;

        return $this;
    }

    /**
     * Determine whether to preserve the original file after storing.
     */
    public function preserveOriginal(bool $preserve = true): self
    {
        $this->preserveOriginal = $preserve;

        return $this;
    }

    /**
     * Fill additional data into the file model.
     */
    public function fillData(string $attribute, mixed $data): self
    {
        $this->file->$attribute = $data;

        return $this;
    }

    /**
     * Save the file to the filesystem and associate it with the fileable entity.
     *
     * @throws Throwable
     */
    public function save(): FileContract
    {
        $this->ensureFileableIsSet();
        $this->fillFile();
        $this->configureFileAttributes();
        $this->applyTapCallback();

        $this->storeFile();

        $this->ensureFileableExists();

        $this->fileable->files()->save($this->file);

        return $this->file;
    }

    /**
     * Ensure that the fileable entity is set.
     *
     * @throws RuntimeException
     */
    protected function ensureFileableIsSet(): void
    {
        if (! isset($this->fileable)) {
            throw new RuntimeException('Fileable entity is not set. Use the to() method to set it.');
        }
    }

    /**
     * Configure file attributes such as disk, UUID, and filepath.
     */
    protected function configureFileAttributes(): void
    {
        $this->file->disk ??= config('fileable.disk');
        $this->file->uuid = (string) Str::uuid();

        $extension = pathinfo($this->file->filename, PATHINFO_EXTENSION);

        $filename = "{$this->file->uuid}.{$extension}";
        $this->file->filepath = ltrim(implode('/', array_filter([
            $this->directory,
            $filename,
        ])), '/');
    }

    /**
     * Apply the tap callback if set.
     */
    protected function applyTapCallback(): void
    {
        if ($this->tap instanceof Closure) {
            ($this->tap)($this->file, $this->fileable, $this->originalFile);
        }
    }

    /**
     * Store the file in the filesystem.
     *
     * @throws RuntimeException|Throwable
     */
    protected function storeFile(): void
    {
        if (is_resource($this->originalFile)) {
            $meta_data = stream_get_meta_data($this->originalFile);
            $filename = $meta_data['uri'];
            $handle = fopen($filename, 'r');
        } else {
            $handle = fopen(
                is_string($this->originalFile)
                    ? $this->originalFile
                    : $this->originalFile->getPathname(),
                'r'
            );
        }
        throw_unless($this->file->store($handle), new RuntimeException);

        if (is_resource($handle)) {
            fclose($handle);
        }

        if (! $this->preserveOriginal) {
            throw_unless($this->deleteOriginal(), new RuntimeException('Failed to delete the original file.'));
        }
    }

    /**
     * Get the file stream from the original file.
     *
     * @throws RuntimeException
     */
    protected function getFileStream()
    {
        if (is_resource($this->originalFile)) {
            $metaData = stream_get_meta_data($this->originalFile);
            $filename = $metaData['uri'] ?? null;
            if (! $filename || ! file_exists($filename)) {
                throw new RuntimeException('Invalid file resource provided.');
            }

            return fopen($filename, 'r');
        }

        if (is_string($this->originalFile)) {
            if (! file_exists($this->originalFile) || ! is_readable($this->originalFile)) {
                throw new RuntimeException("File does not exist or is not readable at path: {$this->originalFile}");
            }

            return fopen($this->originalFile, 'r');
        }

        if ($this->originalFile instanceof SymfonyFile) {
            return fopen($this->originalFile->getPathname(), 'r');
        }

        throw new RuntimeException('Unsupported type for original file.');
    }

    /**
     * Delete the original file from the filesystem.
     *
     * @throws RuntimeException
     */
    protected function deleteOriginal(): bool
    {
        if (is_string($this->originalFile)) {
            return unlink($this->originalFile);
        }
        if (is_resource($this->originalFile)) {
            $meta_data = stream_get_meta_data($this->originalFile);
            $filename = $meta_data['uri'];

            return unlink($filename);
        }

        return unlink($this->originalFile->getPathname());
    }

    /**
     * Get the path of the original file.
     *
     * @throws RuntimeException
     */
    protected function getOriginalFilePath(): string
    {
        if (is_string($this->originalFile)) {
            return $this->originalFile;
        }

        if (is_resource($this->originalFile)) {
            $metaData = stream_get_meta_data($this->originalFile);
            $filename = $metaData['uri'] ?? null;
            if (! $filename) {
                throw new RuntimeException('Invalid file resource provided.');
            }

            return $filename;
        }

        if ($this->originalFile instanceof SymfonyFile) {
            return $this->originalFile->getPathname();
        }

        throw new RuntimeException('Unsupported type for original file.');
    }

    /**
     * Ensure that the fileable entity exists in the database.
     *
     * @throws ModelNotFoundException
     */
    protected function ensureFileableExists(): void
    {
        if (! $this->fileable->exists) {
            throw (new ModelNotFoundException)->setModel(get_class($this->fileable));
        }
    }

    /**
     * Fill the file model with data from the original file.
     *
     * @throws Exception
     */
    protected function fillFile(): void
    {
        if (is_resource($this->originalFile)) {
            $this->fillFileFromStream();
        } elseif (is_string($this->originalFile)) {
            $this->fillFileFromPath();
        } elseif ($this->originalFile instanceof UploadedFile) {
            $this->fillFileFromUploadedFile();
        } elseif ($this->originalFile instanceof SymfonyFile) {
            $this->fillFileFromSymfonyFile();
        } else {
            throw new OutOfBoundsException('Unsupported original file type provided.');
        }
    }

    /**
     * Fill file data from a file path.
     *
     * @throws Exception
     */
    protected function fillFileFromPath(): void
    {
        $path = $this->originalFile;

        if (! file_exists($path)) {
            throw new Exception("File does not exist at path: {$path}");
        }

        if (! is_readable($path)) {
            throw new Exception("File is not readable at path: {$path}");
        }

        $this->file->filename ??= basename($path);
        $this->file->size = filesize($path) ?: throw new Exception('Unable to determine file size.');
        $this->file->mimetype = mime_content_type($path) ?: throw new Exception('Unable to determine MIME type.');
    }

    /**
     * Fill file data from an UploadedFile instance.
     */
    protected function fillFileFromUploadedFile(): void
    {
        /** @var UploadedFile $file */
        $file = $this->originalFile;

        $this->file->filename ??= $file->getClientOriginalName();
        $this->file->size = $file->getSize();
        $this->file->mimetype = $file->getClientMimeType() ?: $file->getMimeType();
    }

    /**
     * Fill file data from a SymfonyFile instance.
     */
    protected function fillFileFromSymfonyFile(): void
    {
        /** @var SymfonyFile $file */
        $file = $this->originalFile;

        $this->file->filename ??= $file->getFilename();
        $this->file->size = $file->getSize();
        $this->file->mimetype = $file->getMimeType();
    }

    /**
     * Fill file data from a file stream.
     *
     * @throws Exception
     */
    protected function fillFileFromStream(): void
    {
        $content = stream_get_contents($this->originalFile);
        if ($content === false) {
            throw new Exception('Unable to read from the file stream.');
        }

        $finfo = new finfo(FILEINFO_MIME_TYPE);
        $mimeType = $finfo->buffer($content);
        if (! $mimeType) {
            throw new Exception('Unable to determine MIME type from stream.');
        }

        $this->file->size = strlen($content);
        $this->file->mimetype = $mimeType;

        $extensions = MimeTypes::getDefault()->getExtensions($mimeType);
        $extension = $extensions[0] ?? 'bin';
        $this->file->filename ??= sprintf(
            '%s-%s.%s',
            date('Ymd_His'),
            Str::random(10),
            $extension
        );
    }
}
