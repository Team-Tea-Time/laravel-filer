<?php namespace TeamTeaTime\Filer;

use Illuminate\Database\Eloquent\Model;

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
    protected $fillable = ['user_id', 'model_key'];

    /**
     * The relations to eager load on every query.
     *
     * @var array
     */
    protected $with = ['model', 'attachment'];

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
     * Relationship: attachment
     *
     * @return \Illuminate\Database\Eloquent\Relations\MorphTo
     */
    public function attachment()
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
        return $query->where('model_key', $key);
    }

    /**
     * Get the attachment's URL.
     *
     * @return string
     */
    public function getUrl()
    {
        if (config('filer.append_query_string')) {
            return "{$this->attachment->getUrl()}?v={$this->updated_at->timestamp}";
        }

        return $this->attachment->getUrl();
    }

    /**
     * Get the attachment's download URL.
     *
     * @return string
     */
    public function getDownloadUrl()
    {
        return $this->attachment->getDownloadUrl();
    }
}
