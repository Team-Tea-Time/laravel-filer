<?php

namespace TeamTeaTime\Filer\Controllers;

use Illuminate\Routing\Controller;
use TeamTeaTime\Filer\LocalFile;

class LocalFileController extends Controller
{
    public function download($fid)
    {
        $file = LocalFile::findOrFail($fid);
        return response()->download($file->URL);
    }
}
