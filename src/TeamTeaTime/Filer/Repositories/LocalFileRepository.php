<?php namespace TeamTeaTime\Filer\Repositories;

use TeamTeaTime\Models\LocalFile;

class LocalFileRepository extends BaseRepository
{

    public function __construct()
    {
        $this->model = new LocalFile;
        $this->perPage = 20;
    }

}
