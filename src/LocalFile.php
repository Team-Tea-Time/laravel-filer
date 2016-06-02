<?php namespace TeamTeaTime\Filer;

use Illuminate\Database\Eloquent\ModelNotFoundException;
use Symfony\Component\HttpFoundation\File\File;

class LocalFile extends BaseItem
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'filer_local_files';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['filename', 'path', 'mimetype', 'size'];

    /**
     * The "booting" method of the model.
     *
     * @return void
     */
    protected static function boot() {
        parent::boot();

        if (config('filer.cleanup_on_delete')) {
            static::deleting(function ($file) {
                $path = config('filer.path.absolute') . "{$file->path}/{$file->filename}";

                if (is_file($path)) {
                    unlink($path);
                }
            });
        }

        // Whenever a new model is created in the database, we add a hash
        static::creating(function ($model) {
            $model->hash = $model->generateHash();
        });
    }

    /**
     * Return a Symfony File representation of the file.
     *
     * @return File
     */
    public function getFile()
    {
        return new File($this->getAbsolutePath());
    }

    /**
     * Get the file's absolute path.
     *
     * @return string
     */
    public function getAbsolutePath()
    {
        return $this->getPath(config('filer.path.absolute'));
    }

    /**
     * Get the file's relative path.
     *
     * @return string
     */
    public function getRelativePath()
    {
        return $this->getPath(config('filer.path.relative'));
    }

    /**
     * Get the file's contents.
     *
     * @return string
     */
    public function getContents()
    {
        return file_get_contents($this->getAbsolutePath());
    }

    /**
     * Get the item's URL.
     *
     * @return string
     */
    public function getUrl()
    {
        return route('filer.file.view', $this->getIdentifier());
    }

    /**
     * Get the item's download URL.
     *
     * @return string
     */
    public function getDownloadUrl()
    {
        return route('filer.file.download', $this->getIdentifier());
    }

    /**
     * Construct a filepath.
     *
     * @param  string  $path
     * @return string
     */
    private function getPath($path)
    {
        return "{$path}{$this->path}/{$this->filename}";
    }

    /**
     * Get the unique identifier according to filer.hash_routes.
     *
     * @return integer|string
     */
    public function getIdentifier()
    {
        return config('filer.hash_routes') ? $this->hash : $this->id;
    }

    /**
     * Get a model instance using a unique identifier according to filer.hash_routes.
     *
     * @param  integer  $id
     * @return LocalFIle
     */
    public static function getByIdentifier($id)
    {
        $file = config('filer.hash_routes') ? static::whereHash($id)->first() : static::find($id);

        if (!$file) {
            throw (new ModelNotFoundException)->setModel(static::class);
        }

        return $file;
    }

    /**
     * Generate a unique hash for the file.
     *
     * @return string
     */
    public function generateHash()
    {
        do {
            $hash = str_random(config('filer.hash_length', 40));
        } while (static::whereHash($hash)->first() instanceof LocalFile);

        return $hash;
    }
}
