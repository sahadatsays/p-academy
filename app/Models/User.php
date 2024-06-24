<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;

use Illuminate\Database\Eloquent\Casts\AsCollection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Passport\HasApiTokens;

class User extends Authenticatable
{
    use HasFactory, Notifiable, HasApiTokens;

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'zt_users';

    /**
     * @var array
     */
    protected $guarded = ['id'];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed'
        ];
    }

    /**
     * User name
     */
    public function name()
    {
        return $this->last_name . ' ' . $this->first_name;
    }

    /**
     * @return mixed
     */
    public function groups()
    {
        return $this->belongsToMany(Group::class, 'zt_users_groups', 'user_id', 'group_id')->withPivot('ending_at', 'plan', 'sub_id');
    }

    /**
     * @return mixed
     */
    public function isRoot()
    {
        return $this->groups()->where('group_id', '=', 1)->get()->count() > 0;
    }

    /**
     * @return mixed
     */
    public function isAdmin()
    {
        return $this->groups()->where('group_id', '=', 2)->get()->count() > 0;
    }

    /**
     * @return mixed
     */
    public function isPremium()
    {
        return $this->groups()->where('group_id', '=', 3)->orWhere('group_id', '=', 4)->orWhere('group_id', '=', 5)->get()->count() > 0;
    }

    /**
     * @param $group
     * @return mixed
     */
    public function inGroup($group)
    {
        if (gettype($group) == 'integer') {
            return $this->groups()->where('zt_groups.id', '=', $group)->count();
        } else {
            return $this->groups()->where('lower(zt_groups.name)', '=', strtolower($group))->count();
        }
    }

    /**
     * @return mixed
     */
    public function member()
    {
        return $this->hasOne(Membre::class, 'id');
    }
}
