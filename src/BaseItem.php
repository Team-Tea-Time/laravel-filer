<?php namespace TeamTeaTime\Filer;

use Illuminate\Database\Eloquent\Model;

abstract class BaseItem extends Model
{
    /**
     * Relationship: attachment
     *
     * @return \Illuminate\Database\Eloquent\Relations\MorphMany
     */
    public function attachment()
    {
        return $this->morphMany(Attachment::class, 'item');
    }

    /**
     * Get the item's URL.
     *
     * @return string
     */
    abstract public function getUrl();

    /**
     * Get the item's download URL.
     *
     * @return string
     */
    abstract public function getDownloadUrl();
}
