<?php namespace TeamTeaTime\Filer\Models;

use Eloquent;

class LocalFile extends Eloquent
{

    // Eloquent properties
    protected $table      = 'filer_local_files';
    public    $timestamps = true;
    protected $appends    = [];
    protected $fillable   = ['user_id', 'filename', 'path', 'mimetype'];

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
