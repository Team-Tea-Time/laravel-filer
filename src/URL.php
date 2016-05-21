<?php namespace TeamTeaTime\Filer;

class Url extends BaseItem
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'filer_urls';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['url'];

    /**
     * Get the item's URL.
     *
     * @return string
     */
    public function getUrl()
    {
        return $this->url;
    }

    /**
     * Get the item's download URL.
     *
     * @return string
     */
    public function getDownloadUrl()
    {
        return $this->url;
    }
}
