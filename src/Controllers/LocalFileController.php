<?php namespace TeamTeaTime\Filer\Controllers;

use Carbon\Carbon;
use Illuminate\Routing\Controller;
use TeamTeaTime\Filer\LocalFile;

class LocalFileController extends Controller
{
    /**
     * Attempt to render the specified file.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function view($id)
    {
        $file = LocalFile::getByIdentifier($id);

        $response = response($file->getContents(), 200)->withHeaders([
            'Content-Type'  => $file->getFile()->getMimeType(),
            'Cache-Control' => 'max-age=86400, public',
            'Expires'       => Carbon::now()->addSeconds(86400)->format('D, d M Y H:i:s \G\M\T')
        ]);
        return $response;
    }

    /**
     * Return a download response for the specified file.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function download($id)
    {
        $file = LocalFile::getByIdentifier($id);
        return response()->download($file->getAbsolutePath());
    }
}
