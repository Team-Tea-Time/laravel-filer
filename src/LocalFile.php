<?php namespace TeamTeaTime\Filer;

use Symfony\Component\HttpFoundation\File\File;
use Illuminate\Database\Eloquent\ModelNotFoundException;

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
        static::creating(function($model) {
            $model->hash = $model->makeHash();
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
     * Get the current identifier used for the routes.
     *
     * @return integer|string
     */
    public function getIdentifier()
    {
        if (config('filer.hash_routes')) {
            return $this->hash;
        }

        return $this->id;
    }

    /**
     * Get model by the current used identifier for the routes.
     *
     * @param  integer $id
     * @return LocalFIle
     */
    public static function getByIdentifier($id)
    {
        if (config('filer.hash_routes')) {
            $file = self::whereHash($id)->first();
        } else {
            $file = self::whereId($id)->first();
        }

        if (!$file) {
            throw (new ModelNotFoundException)->setModel(LocalFile::class);
        }

        return $file;
    }

    /**
     * Makes as hash for the file.
     *
     * @return string
     */
    public function makeHash()
    {
        return str_random(config('filer.hash_length', 40));
    }

}
