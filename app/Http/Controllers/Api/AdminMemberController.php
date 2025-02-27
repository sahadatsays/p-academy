<?php

namespace App\Http\Controllers\Api;

use App\Helpers\QueryHelper;
use App\Http\Resources\AffiliationResource;
use App\Http\Resources\MemberResource;
use App\Http\Resources\PaymentResource;
use App\Http\Resources\TransfertPpaResource;
use App\Http\Resources\UserResource;
use App\Models\Membre;
use App\Models\Operator;
use App\Models\TransfertPpa;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AdminMemberController extends ApiController
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request  $request)
    {
        $perPage = $request->input('itemsPerPage', 25);

        $query = Membre::query()
            ->join('zt_users', 'pa_membres.id', '=', 'zt_users.id')
            ->select('zt_users.id as userid', 'pa_membres.*');

        // sorting query
        if ($request->get('sortBy')) {
            $sortBy = json_decode($request->get('sortBy'));
            $query = $this->sortBy($query, $sortBy->key, $sortBy->order);
        }

        // filters by columns
        $query = $this->filters($query, $request);

        // search
        if ($request->has('search')) {
            $search = $request->get('search');
            $columns = ['zt_users.id', 'zt_users.username', 'zt_users.email', 'zt_users.first_name', 'zt_users.last_name'];
            $query = QueryHelper::searchAll($query, $search, $columns);
        }

        // Pagination
        $members = $query->with('user')->paginate($perPage);

        return MemberResource::collection($members);
    }

    private function sortBy($query, $key, $order)
    {

        if ($key == 'user.username') {
            return $query->orderBy('username', $order);
        }

        if ($key == 'user.name') {
            return $query->orderBy('first_name', $order);
        }

        if ($key == 'user.email') {
            return $query->orderBy('email', $order);
        }

        if ($key == 'user.createdAt') {
            return $query->orderBy('created_at', $order);
        }

        if ($key == 'user.activatedAt') {
            return $query->orderBy('activated_at', $order);
        }
        return $query->orderBy($key, $order);
    }

    private function filters($query, $request)
    {
        // $request->only(['id', 'username', 'name', 'email', 'activated'])
        if ($request->get('id')) {
            $query->where('zt_users.id', $request->id);
        }

        if ($request->get('username')) {
            $query->where('username', 'LIKE', '%' . $request->username . '%');
        }

        if ($request->get('name')) {
            $query->where('first_name', 'LIKE', '%' . $request->name . '%')->orWhere('last_name', 'LIKE', '%' . $request->name . '%');
        }

        if ($request->get('email')) {
            $query->where('email', 'LIKE', '%' . $request->email . '%');
        }

        if ($request->get('activated') == 'Active') {
            $query->where('activated', 1);
        }

        if ($request->get('activated') == 'Inactive') {
            $query->where('activated', 0);
        }


        return $query;
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        try {
            $member = Membre::findOrFail($id);
            $commands = $member->commandes;
            $payments = $member->payments;
            $affiliations = $member->affiliations;
            $status = $member->user->getStatus();
            $allTransfertPpa = $member->allTransfertPpa()->sortByDesc('created_at');

            return [
                'member' => new MemberResource($member),
                'user' => new UserResource($member->user),
                'commands' => MemberResource::collection($commands),
                'payments' => PaymentResource::collection($payments),
                'affiliations' => AffiliationResource::collection($affiliations),
                'status' => $status,
                'allTransfertPpa' => TransfertPpaResource::collection($allTransfertPpa)
            ];
        } catch (ModelNotFoundException $th) {
            return $this->sendError($th->getMessage(), $th->getCode());
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'ppa' => 'required|numeric'
        ]);

        $ppa = $request->input('ppa', "");

        if ($ppa != "") {
            $user = User::findOrFail($id);
            $member = $user->member;
            $member->ppa = $member->ppa + $ppa;
            $member->save();

            $transfertppa = new TransfertPpa();
            $transfertppa->expediteur = 0;
            $transfertppa->destinataire = $user->id;
            $transfertppa->ppa = $ppa;
            $transfertppa->motif = $request->input('motif');
            $transfertppa->categ = $request->input('type');
            $transfertppa->onlyreport = 0;
            $transfertppa->save();


            //Data Email
            $datacom = array();
            $datacom['username'] = $user->first_name;
            $datacom['email'] = $user->email;
            $datacom['ppa'] = $member->ppa;
            $datacom['motif'] = $request->input('motif');
            $datacom['type'] = $request->input('type');


            if (substr($ppa, 0, 1) == '-') {
                $datacom['ppaactionmontant'] = substr($ppa, 1);
                $datacom['ppaaction'] = "débité";
            } else {
                $datacom['ppaactionmontant'] = $ppa;
                $datacom['ppaaction'] = "crédité";
            }
            // Send Email
            // $member->SendMailPpaAction($datacom);
            return $this->sendResponse([], 'Update confirm!');
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }

    /**
     * Status update
     */
    public function updateStatus(Request $request, User $user)
    {
        $data = $request->all();

        if (!strtotime($data['ending_at'])) {
            $data['ending_at'] = '';
        } else {
            $data['ending_at'] = Carbon::parse($data['ending_at'])->format('Y-m-d');
        }
        $status = $user->getStatus();

        if ($user->isSilver())  $user->removeSilver();
        if ($user->isGold())     $user->removeGold();

        $sub_id = null;
        $plan_id = null;

        if ($data['level'] == 'silver') {
            $user->addSilver($data['ending_at'], $plan_id, $sub_id);
        }

        if ($data['level'] == 'gold') {
            $user->addGold($data['ending_at'], $plan_id, $sub_id);
        }

        return response()->json(['success' => true, 'message' => 'Member status updated.']);
    }
}
