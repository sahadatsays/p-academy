<?php namespace App\Models\Forum;
use Illuminate\Database\Eloquent\Model;

class Topic extends Model {

    protected $connection='forum';

	public $timestamps = false;
	protected $table = 'topics';
	protected $guarded = array('id');


   public function getPostsCount()
   {
       return $this->posts_count;
   }


    public function posts()
    {
        return $this->hasMany('\App\Models\Forum\Post','topic_id');
    }


}
