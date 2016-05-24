<?php namespace TeamTeaTime\Filer;

use Illuminate\Database\Eloquent\Model;
use Storage;

class Attachment extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'filer_attachments';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['user_id', 'key'];

    /**
     * The relations to eager load on every query.
     *
     * @var array
     */
    protected $with = ['model', 'item'];

    /**
     * The "booting" method of the model.
     *
     * @return void
     */
    protected static function boot() {
        parent::boot();

        static::deleting(function ($attachment) {
            $attachment->item->delete();
        });
    }

    /**
     * Relationship: user
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user()
    {
        return $this->belongsTo(config('filer.user.model'), 'user_id');
    }

    /**
     * Relationship: model
     *
     * @return \Illuminate\Database\Eloquent\Relations\MorphTo
     */
    public function model()
    {
        return $this->morphTo();
    }

    /**
     * Relationship: item
     *
     * @return \Illuminate\Database\Eloquent\Relations\MorphTo
     */
    public function item()
    {
        return $this->morphTo();
    }

    /**
     * Scope: key
     *
     * @param  \Illuminate\Database\Query\Builder  $query
     * @param  string  $key
     * @return \Illuminate\Database\Query\Builder
     */
    public function scopeKey($query, $key)
    {
        return $query->where('key', $key);
    }

    /**
     * Get the attachment's URL.
     *
     * @return string
     */
    public function getUrl()
    {
        if (config('filer.append_query_string')) {
            return "{$this->item->getUrl()}?v={$this->updated_at->timestamp}";
        }

        return $this->item->getUrl();
    }

    /**
     * Get the attachment's download URL.
     *
     * @return string
     */
    public function getDownloadUrl()
    {
        return $this->item->getDownloadUrl();
    }
}
