<?php namespace TeamTeaTime\Filer;

use Symfony\Component\HttpFoundation\File\File;

class LocalFile extends BaseItem
{
    protected $table      = 'filer_local_files';
    public    $timestamps = true;
    protected $fillable   = ['user_id', 'filename', 'path', 'mimetype', 'size'];

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
        return route('filer.file.view', $this->id);
    }

    /**
     * Get the item's download URL.
     *
     * @return string
     */
    public function getDownloadUrl()
    {
        return route('filer.file.download', $this->id);
    }

    /**
     * Construct a filepath.
     *
     * @param  string  $path
     * @return string
     */
    private function getPath($path)
    {
        return $path . "{$this->path}/{$this->filename}";
    }
}
