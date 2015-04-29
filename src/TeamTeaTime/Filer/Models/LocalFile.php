<?php namespace TeamTeaTime\Filer\Models;

use Eloquent;

class LocalFile extends Eloquent
{

    // Eloquent properties
    protected $table      = 'local_files';
    public    $timestamps = true;
    protected $appends    = [];

    /*
    |--------------------------------------------------------------------------
    | Attributes
    |--------------------------------------------------------------------------
    */

    public function getAbsolutePathAttribute()
    {
        return "{$this->path}/{$this->filename}";
    }

    public function getDownloadRouteAttribute()
    {
        return route('filer.file.download', $this->id);
    }

}
