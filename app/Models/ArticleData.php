<?php 

namespace Kelio\Zetatori;

use Illuminate\Database\Eloquent\Model;

class ArticleData extends Model {

	protected $table = "zt_articles_customdata";
    protected $fillable = ['article_id','metakey','metavalue'];

    protected $primaryKey = ['article_id','metakey'];
    public $incrementing = false;
    public $timestamps = false;

}
