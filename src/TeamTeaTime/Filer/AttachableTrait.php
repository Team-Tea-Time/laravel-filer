<?php namespace TeamTeaTime\Filer;

use Symfony\Component\HttpFoundation\File\File;
use TeamTeaTime\Filer\Models\Attachment;
use TeamTeaTime\Filer\Models\LocalFile;
use TeamTeaTime\Filer\Models\URL;

trait AttachableTrait
{

    public function attachments()
    {
        return $this->morphMany('TeamTeaTime\Filer\Models\Attachment', 'attachable');
    }

    /**
     * Attaches a file/link
     *
     * @param   $file           Local file path (relative to filer.path),
     *          string          Symfony\Component\HttpFoundation\File\File
     *                          (SplFileInfo) instance or remote file URL
     * @param   $key            Optional key to retrieve a specific attachment.
     *          string          Useful for hasOne attachment relationships.
     * @param   $title          Optional title.
     *          string
     * @param   $description    Optional description.
     *          string
     *
     * @return TeamTeaTime\Filer\Models\Attachment
     */
    public function attach($item, $key = '', $title = '', $description = '')
    {
        $user_callback = config('filer.current_user');
        $user = $user_callback();

        if (is_string($item))
        {
            // Item is a string; check to see if it's a URL
            if (filter_var($item, FILTER_VALIDATE_URL, FILTER_FLAG_PATH_REQUIRED))
            {
                // Item is a URL
                $type = 'URL';
            }
            elseif (is_file($item))
            {
                // Item is a local file
                $type = 'LocalFile';
                $item = new File($item);
            }
        }
        elseif (is_a($item, 'SplFileInfo'))
        {
            // Item is a file object
            $type = 'LocalFile';
        }

        // Throw an exception if item doesn't match any known types
        if (!isset($type))
        {
            throw new Exception('Unknown item type');
        }

        // Create the appropriate model for the given item if it doesn't already
        // exist
        switch ($type)
        {
            case 'URL':
                $attachment = URL::firstOrCreate([
                    'user_id'   => $user->id,
                    'url'       => $item
                ]);

                break;
            case 'LocalFile':
                $attachment = LocalFile::firstOrCreate([
                    'user_id'   => $user->id,
                    'filename'  => $item->getFilename(),
                    'path'      => str_replace(config('filer.path.absolute'), '', $item->getPath()),
                    'mimetype'  => $item->getMimeType()
                ]);

                break;
        }

        print "Key: $key, type: " . get_class($attachment);

        // Find or create the attachment
        $attach = Attachment::firstOrCreate([
            'user_id'           => $user->id,
            'attachment_type'   => get_class($attachment),
            'attachment_id'     => $attachment->id,
            'attachment_key'    => $key
        ]);

        $this->attachments()->save($attach);

        return $attach;
    }

}
