<?php namespace TeamTeaTime\Filer;

use Auth;
use Symfony\Component\HttpFoundation\File\File;
use TeamTeaTime\Filer\Attachment;
use TeamTeaTime\Filer\Filer;
use TeamTeaTime\Filer\LocalFile;
use TeamTeaTime\Filer\Url;

trait AttachableTrait
{
    /**
     * Relationship: attachments
     *
     * @return \Illuminate\Database\Eloquent\Relations\MorphMany
     */
    public function attachments()
    {
        return $this->morphMany(Attachment::class, 'model');
    }

    /**
     * Attaches a file/link. $item can be a local file path (relative to config('filer.path.absolute')),
     * Symfony\Component\HttpFoundation\File\File or SplFileInfo instance or a file URL.
     *
     * @param  string  $item
     * @param  array  $options
     * @return Attachment|bool
     */
    public function attach($item, array $options = [])
    {
        // Merge in default options
        $options += [
            'key'           => '',
            'title'         => '',
            'description'   => '',
            'user_id'       => Auth::id()
        ];

        // Determine the type
        $type = Filer::checkType($item);

        // Create the appropriate model for the item if it doesn't already exist
        $itemToAttach = null;
        switch ($type)
        {
            case 'Url':
                $itemToAttach = Url::firstOrCreate(['url' => $item]);

                break;
            case 'LocalFile':
                $file = new File(config('filer.path.absolute') . "/{$item}");

                $itemToAttach = LocalFile::firstOrNew([
                    'filename'  => $file->getFilename(),
                    'path'      => Filer::getRelativeFilepath($file)
                ]);

                $itemToAttach->fill([
                    'mimetype'  => $file->getMimeType(),
                    'size'      => $file->getSize()
                ]);

                $itemToAttach->save();

                break;
        }

        if (is_null($itemToAttach)) {
            return false;
        }

        // Create/update and save the attachment
        $attributes = [
            'user_id' => $options['user_id'],
            'model_id' => $this->id
        ];

        if (!is_null($options['key'])) {
            $attributes['model_key'] = $options['key'];
        }

        $attach = Attachment::firstOrNew($attributes);

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
