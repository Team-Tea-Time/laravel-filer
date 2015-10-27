<?php

namespace TeamTeaTime\Filer;

class LocalFile extends BaseItem
{
    // Eloquent properties
    protected $table      = 'filer_local_files';
    public    $timestamps = true;
    protected $fillable   = ['user_id', 'filename', 'path', 'mimetype', 'size'];

    /*
    |--------------------------------------------------------------------------
    | Attributes
    |--------------------------------------------------------------------------
    */

    public function getURLAttribute()
    {
        return config('filer.path.relative') . "{$this->path}/{$this->filename}";
    }

    public function getDownloadRouteAttribute()
    {
        return route('filer.file.download', $this->id);
    }
}
