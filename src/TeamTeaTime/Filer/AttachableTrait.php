<?php namespace TeamTeaTime\Filer;

use Symfony\Component\HttpFoundation\File\File;
use TeamTeaTime\Filer\Attachment;
use TeamTeaTime\Filer\LocalFile;
use TeamTeaTime\Filer\URL;

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
        $type = $this->checkType($item);

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
                    $item = new File($item);
                }

                $itemToAttach = LocalFile::firstOrNew([
                    'user_id'   => $user->id,
                    'filename'  => $item->getFilename(),
                    'path'      => str_replace(config('filer.path.absolute'), '', $item->getPath())
                ]);

                $itemToAttach->fill([
                    'mimetype'  => $item->getMimeType(),
                    'size'      => $item->getSize()
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

    /**
     * Attempts to determine what the given item is between a local filepath, a
     * local file object or a URL.
     *
     * @param   $item           The item to check.
     *          mixed
     *
     * @return  string if check successful. An exception is thrown if it fails.
     */
    private function checkType($item)
    {
        if (is_string($item))
        {
            // Item is a string; check to see if it's a URL
            if (filter_var($item, FILTER_VALIDATE_URL, FILTER_FLAG_PATH_REQUIRED))
            {
                // Item is a URL
                return 'URL';
            }
            elseif (is_file($item))
            {
                // Item is a filepath
                return 'LocalFile';
            }
        }
        elseif (is_a($item, 'SplFileInfo'))
        {
            // Item is a file object
            return 'LocalFile';
        }

        // Throw an exception if item doesn't match any known types
        throw new Exception('Unknown item type');
    }

}
