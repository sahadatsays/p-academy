<?php

namespace App\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;

class UserGroup extends Model {

    protected $table = 'zt_users_groups';
   protected $guarded = array('id');

    public function user()
    {
       return $this->belongsTo(User::class);
    }
    public function group()
    {
       return $this->belongsTo(Group::class);
    }

}
?>
