<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Patag extends Model
{
    use HasFactory;

    public $timestamps = true;
    protected $table = 'pa_tags';
    protected $guarded = array('id');

    public function childs()
    {
        return $this->hasMany(Patag::class, 'parent_id');
    }

    public function parent()
    {
        return $this->belongsTo(self::class, 'parent_id', 'id');
    }

    public function articles()
    {
        return $this->belongsToMany(Article::class, 'pa_tags_articles', 'patag_id', 'article_id');
    }

    public function buildTreeChilds($tree, $id)
    {
        $tag = Patag::find($id);

        foreach ($tag->childs()->get() as $c)
        {
            $tree = $this->buildTreeChilds($tree, $c->id);
        }

        $tree[] = $id;

        return $tree;

    }

   public function scopeType($query, $type)
   {
     return $query->where('type','=',$type);
   }

   public function coach()
   {

     $row = DB::table('zt_users_customdata')->where('metakey','patag_id')->where('metavalue',$this->id)->first();

        if ($row) {
           return User::find($row->user_id);
        }

   }

}
