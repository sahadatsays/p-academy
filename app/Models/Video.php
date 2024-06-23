<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Video extends Model
{
    public $timestamps = true;
    protected $table = 'pa_videos';
    protected $guarded = array('id');

    /**
     * Get the user that owns the Video
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function article()
    {
        return $this->belongsTo('\Kelio\Zetatori\Article', 'itemid');
    }

    public function getSerie()
    {
        return $this->belongsTo('App\Models\VideoSerie', 'serie');
    }
}
