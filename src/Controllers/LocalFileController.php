<?php

namespace TeamTeaTime\Filer\Controllers;

use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use TeamTeaTime\Filer\LocalFile;

class LocalFileController extends Controller
{
    /**
     * Attempt to render the specified file.
     *
     * @param  int  $id
     * @return Response
     */
    public function view($id)
    {
        $file = LocalFile::findOrFail($id);
        $response = new Response($file->getContents(), 200);
        $response->header('Content-Type', $file->getFile()->getMimeType());
        return $response;
    }

    /**
     * Return a download response for the specified file.
     *
     * @param  int  $id
     * @return Response
     */
    public function download($id)
    {
        $file = LocalFile::findOrFail($id);
        return response()->download($file->getAbsolutePath());
    }
}
