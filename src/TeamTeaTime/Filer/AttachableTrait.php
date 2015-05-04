<?php namespace TeamTeaTime\Filer;

use Symfony\Component\HttpFoundation\File\File;
use TeamTeaTime\Filer\Attachment;
use TeamTeaTime\Filer\LocalFile;
use TeamTeaTime\Filer\URL;
use TeamTeaTime\Filer\Utils;

trait AttachableTrait
{

    public function attachments()
    {
        return $this->morphMany('TeamTeaTime\Filer\Attachment', 'model');
    }

    /**
     * Attaches a file/link
     *
     * @param   $file           Local file path (relative to filer.path),
     *          string          Symfony\Component\HttpFoundation\File\File
     *                          (SplFileInfo) instance or remote file URL
     *
     * @param   $key            Optional key to help identify the attachment.
     *          string
     *
     * @param   $title          Optional title.
     *          string
     *
     * @param   $description    Optional description.
     *          string
     *
     * @return  TeamTeaTime\Filer\Models\Attachment
     */
    public function attach($item, $key = '', $title = '', $description = '')
    {
        $user_callback = config('filer.current_user');
        $user = $user_callback();

        // Determine the type
        $type = Utils::checkType($item);

        // Create the appropriate model for the item if it doesn't already exist
        $itemToAttach = NULL;
        switch ($type)
        {
            case 'URL':
                $itemToAttach = URL::firstOrCreate([
                    'user_id'   => $user->id,
                    'url'       => $item
                ]);

                break;
            case 'LocalFile':
                if (is_file($item))
                {
                    $file = new File($item);
                }

                $itemToAttach = LocalFile::firstOrNew([
                    'user_id'   => $user->id,
                    'filename'  => $file->getFilename(),
                    'path'      => Utils::getRelativeFilepath($file)
                ]);

                $itemToAttach->fill([
                    'mimetype'  => $file->getMimeType(),
                    'size'      => $file->getSize()
                ]);
                $itemToAttach->save();

                break;
        }

        if (is_null($itemToAttach))
        {
            return FALSE;
        }

        // Find or create the attachment
        $attach = Attachment::firstOrCreate([
            'user_id'   => $user->id,
            'model_key' => $key
        ]);

        // Save the current model to the attachment
        $this->attachments()->save($attach);
        // Save the item to the attachment
        $itemToAttach->attachment()->save($attach);

        return $attach;
    }

}
