<?php

namespace TeamTeaTime\Filer;

class URL extends BaseItem
{
    // Eloquent properties
    protected $table      = 'filer_urls';
    public    $timestamps = true;
    protected $fillable   = ['user_id', 'url'];
}
