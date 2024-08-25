<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Casts\AsCollection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Passport\HasApiTokens;
use stdClass;

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

    /**
     * @return mixed
     */
    public function getStatus()
    {
        $status = new stdClass;
        $status->level = "membre";
        $status->acces = 302;
        $status->ending_at = null;
        $status->plan = null;
        $status->sub_id = null;

        $premium = $this->groups()->where('group_id', '=', 3)->first();
        if ($premium) {
            $status->level = "premium";
            $status->ending_at = $premium->pivot->ending_at;
            $status->plan = $premium->pivot->plan;
            $status->sub_id = $premium->pivot->sub_id;
            $status->acces = 303;
        }

        $silver = $this->groups()->where('group_id', '=', 4)->first();
        if ($silver) {
            $status->level = "silver";
            $status->ending_at = $silver->pivot->ending_at != null ? Carbon::parse($silver->pivot->ending_at)->format('d M Y') : '';
            $status->plan = $silver->pivot->plan;
            $status->sub_id = $silver->pivot->sub_id;
            $status->acces = 807;
        }

        $gold = $this->groups()->where('group_id', '=', 5)->first();
        if ($gold) {
            $status->level = "gold";
            $status->ending_at = $gold->pivot->ending_at != null ? Carbon::parse($gold->pivot->ending_at)->format('d F Y') : 'at life';
            $status->plan = $gold->pivot->plan;
            $status->sub_id = $gold->pivot->sub_id;
            $status->acces = 808;
        }

        if ($status->plan != null) {
            $status->plan = substr($status->plan,0,4) == "plan" ? 'mensuel' : 'annuel';
        }

        return $status;

    }

        /**
     * @return mixed
     */
    public function isSilver()
    {
        return $this->groups()->where('group_id', '=', 4)->get()->count() > 0;
    }

        /**
     * @param $ending_at
     * @param NULL $plan
     * @param NULL $sub_id
     */
    public function addSilver($ending_at = null, $plan = null, $sub_id = null)
    {
        $this->groups()->attach(4, ['ending_at' => $ending_at, 'plan' => $plan, 'sub_id' => $sub_id]);
        // $this->updateOnSIB();
    }

    public function removeSilver()
    {
        $this->groups()->detach(4);
        // $this->updateOnSIB();
    }

        /**
     * @param $ending_at
     * @param NULL $plan
     * @param NULL $sub_id
     */
    public function updateSilver($ending_at = null, $plan = null, $sub_id = null)
    {
        $this->groups()->updateExistingPivot(4, ['ending_at' => $ending_at, 'plan' => $plan, 'sub_id' => $sub_id]);
        // $this->updateOnSIB();
    }

    /**
     * @param $ending_at
     */
    public function addPremium($ending_at = null)
    {
        $this->groups()->attach(3, ['ending_at' => $ending_at]);
        $this->updateOnSIB();
    }

    public function removePremium()
    {
        $this->groups()->detach(3);
        $this->updateOnSIB();
    }

    /**
     * @param $ending_at
     */
    public function updatePremium($ending_at = null)
    {
        $this->groups()->updateExistingPivot(3, ['ending_at' => $ending_at]);
        $this->updateOnSIB();
    }

    /**
     * @return mixed
     */
    public function isGold()
    {
        return $this->groups()->where('group_id', '=', 5)->get()->count() > 0;
    }

    /**
     * @param $ending_at
     * @param NULL $plan
     * @param NULL $sub_id
     */
    public function addGold($ending_at = null, $plan = null, $sub_id = null)
    {
        $this->groups()->attach(5, ['ending_at' => $ending_at, 'plan' => $plan, 'sub_id' => $sub_id]);
        // $this->updateOnSIB();
    }

    public function removeGold()
    {
        $this->groups()->detach(5);
        // $this->updateOnSIB();
    }

    /**
     * @param $ending_at
     * @param NULL $plan
     * @param NULL $sub_id
     */
    public function updateGold($ending_at = null, $plan = null, $sub_id = null)
    {
        $this->groups()->updateExistingPivot(5, ['ending_at' => $ending_at, 'plan' => $plan, 'sub_id' => $sub_id]);
        // $this->updateOnSIB();
    }
}
