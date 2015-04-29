<?php namespace TeamTeaTime\Filer\Controllers;

use App;

class LocalFileController extends BaseController
{

    public function __construct()
    {
        $this->repository = App::make('TeamTeaTime\Filer\Repositories\LocalFile');
    }

    public function download($fid)
    {
        $file = $this->repository->getByID($fid);
        return response()->download($file->absolutePath);
    }

}
