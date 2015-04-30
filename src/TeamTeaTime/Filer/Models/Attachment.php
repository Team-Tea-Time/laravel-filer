<?php namespace TeamTeaTime\Filer\Models;

use Eloquent;

class Attachment extends Eloquent
{

    // Eloquent properties
    protected $table      = 'filer_attachments';
    public    $timestamps = true;
    protected $fillable   = ['user_id', 'attachment_type', 'attachment_id', 'attachable_key'];

    /*
    |--------------------------------------------------------------------------
    | Relationships
    |--------------------------------------------------------------------------
    */

    public function attachable() {
        return $this->morphTo();
    }

}
