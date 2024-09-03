<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Log;
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
        $this->updateOnSIB();
    }

    public function removeSilver()
    {
        $this->groups()->detach(4);
        $this->updateOnSIB();
    }

        /**
     * @param $ending_at
     * @param NULL $plan
     * @param NULL $sub_id
     */
    public function updateSilver($ending_at = null, $plan = null, $sub_id = null)
    {
        $this->groups()->updateExistingPivot(4, ['ending_at' => $ending_at, 'plan' => $plan, 'sub_id' => $sub_id]);
        $this->updateOnSIB();
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
        $this->updateOnSIB();
    }

    public function removeGold()
    {
        $this->groups()->detach(5);
        $this->updateOnSIB();
    }

    /**
     * @param $ending_at
     * @param NULL $plan
     * @param NULL $sub_id
     */
    public function updateGold($ending_at = null, $plan = null, $sub_id = null)
    {
        $this->groups()->updateExistingPivot(5, ['ending_at' => $ending_at, 'plan' => $plan, 'sub_id' => $sub_id]);
        $this->updateOnSIB();
    }

    public function updateOnSIB()
    {
      
        $updateContact['email'] = $this->email;

        $statut = $this->getStatus();
        $data =
            [
            'USERID' => $this->id,
            'PSEUDO' => $this->username,
            'PREMIUM' => $this->isPremium(),
            'LEVEL' => $statut->level,
            'LAST_SEEN' => substr($this->last_activity, 0, 10),
            'REGISTERED_AT' => substr($this->created_at, 0, 10),
        ];
        if ($statut->level != "membre") {

            if ($statut->ending_at != null) {
                $data['ENDING_AT'] = substr($statut->ending_at, 0, 10);
            } else {
                $data['ENDING_AT'] = "2099-01-01";
            }

            if (substr($statut->plan, 0, 4) == "plan") {
                $data['MENSUEL'] = true;
            } else {
                $data['MENSUEL'] = false;
            }

            $data['PERIOD'] = substr($statut->ending_at, 0, 10);
        }

        $updateContact['attributes'] = $data;

        $updateContact['emailBlacklisted'] = false;
        $membre = $this->membre;

        $curl = new \anlutro\cURL\cURL;
        $endpoint = config("pokac.endpoint_n8n"); // prod

        $url = "25a184bc-6090-411a-ad83-3e3bf6e8f7ca";

        try
        {
            $response = $curl->post($endpoint.$url, $updateContact);
            if( $response->statusCode == 400 ) $this->createOnSIB();
        }
        catch ( \Exception $e )
        {
            Log::info( 'Exception when calling updateonsib: ' . $e->getMessage() );
            $this->createOnSIB();
        }


        $contactEmails['emails'] = [$this->email];
            $contactEmails['nl'] = 4;
            $url = "7832f38f-d7ed-49c0-838a-64ad3b586a1f";
    
            try
            {
                $response = $curl->post($endpoint.$url, $contactEmails);

            }
            catch ( \Exception $e )
            {
                Log::info( 'Exception when calling updateonsib add to nl 20: ' . $e->getMessage() );
            }

        $levels = ['silver' => 10, 'gold' => 11];
        if ($statut->level != "membre") {

        $contactEmails['emails'] = [$this->email];
        $contactEmails['nl'] = $levels[$statut->level];
        $url = "7832f38f-d7ed-49c0-838a-64ad3b586a1f";

        try
        {
            $response = $curl->post($endpoint.$url, $contactEmails);

        }
        catch ( \Exception $e )
        {
            Log::info( 'Exception when calling updateonsib add to nl 20: ' . $e->getMessage() );
        }
        }

        if ($membre and $membre->news == 0) {
            //$updateContact['emailBlacklisted'] = true;
            $news = false;
        } else {
            $news = true;
        }
// on ajoute à NEWSLETTER

        if ($news) {
            $contactEmails['emails'] = [$this->email];
            $contactEmails['nl'] = 20;
            $url = "7832f38f-d7ed-49c0-838a-64ad3b586a1f";
    
            try
            {
                $response = $curl->post($endpoint.$url, $contactEmails);
    
            }
            catch ( \Exception $e )
            {
                Log::info( 'Exception when calling updateonsib add to nl 20: ' . $e->getMessage() );
            }
        } else {
            $contactEmails['emails'] = [$this->email];
            $contactEmails['nl'] = 20;
            $url = "1a2b473f-c4e2-4a0f-9c11-aa1d27fa7fc3";

            try {
                $response = $curl->post($endpoint.$url, $contactEmails);
            } catch (\Exception$e) {
                Log::info('Exception when removefromList: ' . $e->getMessage());

            }
        }

        $listId = $this->getSIBLists();
        //\Log::info($listId);
        // betclic 12
        // pmu 13
        // unibet 5
        // party 3

        $SIBList = [3 => 8, 12 => 5, 5 => 7, 13 => 6];

        foreach ($listId as $lid) {
           
            $contactEmails['emails'] = [$this->email];
            $contactEmails['nl'] = $SIBList[$lid];
            $url = "7832f38f-d7ed-49c0-838a-64ad3b586a1f";
    
            try
            {
                $response = $curl->post($endpoint.$url, $contactEmails);
    
            }
            catch ( \Exception $e )
            {
                Log::info( 'Exception when calling updateonsib add to nl 20: ' . $e->getMessage() );
            }
        }

    }

    public function createOnSIB()
    {
        $createContact['email'] = $this->email;
        $createContact['attributes'] = ['USERID' => $this->id, 'PSEUDO' => $this->username, 'PREMIUM' => false, 'REGISTERED_AT' => substr($this->created_at, 0, 10)];

        $createContact['emailBlacklisted'] = false;
        $membre = $this->membre;
        // if($membre and $membre->news == 0 ) $createContact['emailBlacklisted'] = true;

        $curl = new \anlutro\cURL\cURL;
        $endpoint = config("pokac.endpoint_n8n"); // prod

        $url = "8b11e8af-2539-4a1b-aa7c-36fb8253c8a8";

        try
        {
            $response = $curl->post($endpoint.$url, $createContact);
            if( $response->statusCode == 400 ) $this->updateOnSIB();
        }
        catch ( \Exception $e )
        {
            Log::info( 'Exception when calling createonsib: ' . $e->getMessage() );
            $this->updateOnSIB();
        }

    
        $contactEmails['emails'] = [$this->email];
        $contactEmails['nl'] = 4;
        $url = "7832f38f-d7ed-49c0-838a-64ad3b586a1f";

        try
        {
            $response = $curl->post($endpoint.$url, $contactEmails);

        }
        catch ( \Exception $e )
        {
            Log::info( 'Exception when calling updateonsib add to nl 4: ' . $e->getMessage() );
        }

        if ($membre and $membre->news == 0) {
            //$updateContact['emailBlacklisted'] = true;
            $news = false;
        } else {
            $news = true;
        }
// on ajoute à NEWSLETTER

        if ($news) {
            $contactEmails['emails'] = [$this->email];
            $contactEmails['nl'] = 20;
            $url = "7832f38f-d7ed-49c0-838a-64ad3b586a1f";
    
            try
            {
                $response = $curl->post($endpoint.$url, $contactEmails);

            }
            catch ( \Exception $e )
            {
                \Log::info( 'Exception when calling updateonsib add to nl 20: ' . $e->getMessage() );
            }
    
        } else {
           

            $contactEmails['emails'] = [$this->email];
            $contactEmails['nl'] = 20;
            $url = "1a2b473f-c4e2-4a0f-9c11-aa1d27fa7fc3";

            try {
                $response = $curl->post($endpoint.$url, $contactEmails);
            } catch (\Exception$e) {
                \Log::info('Exception when removefromList: ' . $e->getMessage());

            }
            
        }

    }
}
