<?php

namespace App\Http\Controllers\Api;

use App\Helpers\QueryHelper;
use App\Http\Resources\MemberResource;
use App\Models\Membre;
use App\Models\Operator;
use App\Models\TransfertPpa;
use App\Models\User;
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
            return new MemberResource($member);
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
}
