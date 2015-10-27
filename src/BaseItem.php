<?php

namespace TeamTeaTime\Filer;

use Eloquent;

class BaseItem extends Eloquent
{
    /*
    |--------------------------------------------------------------------------
    | Relationships
    |--------------------------------------------------------------------------
    */

    public function attachment()
    {
        return $this->morphMany('TeamTeaTime\Filer\Attachment', 'attachment');
    }
}
