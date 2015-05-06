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
     * @param   $options        Array of optional settings.
     *
     * @return  TeamTeaTime\Filer\Models\Attachment
     */
    public function attach($item, $options = array())
    {
        // Merge in default options
        $options += [
            'key'           => '',
            'title'         => '',
            'description'   => '',
            'user_id'       => 'callback'
        ];

        if ($options['user_id'] == 'callback') {
            $userIDCallback = config('filer.user.id');
            $userID = $userIDCallback();
        } else {
            $userID = $options['user_id'];
        }

        // Determine the type
        $type = Utils::checkType($item);

        // Create the appropriate model for the item if it doesn't already exist
        $itemToAttach = NULL;
        switch ($type)
        {
            case 'URL':
                $itemToAttach = URL::firstOrCreate([
                    'user_id'   => $userID,
                    'url'       => $item
                ]);

                break;
            case 'LocalFile':
                if (is_file($item))
                {
                    $file = new File($item);
                }

                $itemToAttach = LocalFile::firstOrNew([
                    'user_id'  => $userID,
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

        // Create/update and save the attachment
        $attach = Attachment::firstOrNew([
            'user_id'   => $userID,
            'model_key' => $options['key']
        ]);

        if (!is_null($options['title'])) {
            $attach->title = $options['title'];
        }

        if (!is_null($options['description'])) {
            $attach->description = $options['description'];
        }

        $attach->save();

        // Save the current model to the attachment
        $this->attachments()->save($attach);
        // Save the item to the attachment
        $itemToAttach->attachment()->save($attach);

        return $attach;
    }

}
