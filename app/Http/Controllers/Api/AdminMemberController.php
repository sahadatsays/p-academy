<?php

namespace App\Http\Controllers\Api;

use App\Http\Resources\MemberResource;
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
        $perPage = $request->input('per_page', 15);
        $sortField = $request->input('sort_by', 'created_at');
        $sortOrder = $request->input('sort_order', 'desc');

        $query = User::leftJoin('zt_v_groups_by_userid', 'zt_users.id', '=', 'zt_v_groups_by_userid.user_id')
            ->leftJoin('pa_membres', 'zt_users.id', '=', 'pa_membres.id')
            ->leftJoin('zt_users_metadata', 'zt_users.id', '=', 'zt_users_metadata.user_id')
            ->select(array(
                'zt_users.id', 'zt_users.token', 'zt_users.username', 'zt_users.first_name', 'zt_users.last_name', DB::Raw('concat(zt_users.first_name," ",zt_users.last_name) as name'),
                'zt_users.email', 'pa_membres.ppa', 'zt_v_groups_by_userid.lesgroupes', 'zt_users.activated', 'zt_users.created_at', 'zt_users.last_activity'
            ));
        // sorting query
        $query = $query->orderBy($sortField, $sortOrder);

        // Pagination
        $members = $query->paginate($perPage);

        return MemberResource::collection($members);
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
            $user = User::leftJoin('zt_v_groups_by_userid', 'zt_users.id', '=', 'zt_v_groups_by_userid.user_id')
                ->leftJoin('pa_membres', 'zt_users.id', '=', 'pa_membres.id')
                ->leftJoin('zt_users_metadata', 'zt_users.id', '=', 'zt_users_metadata.user_id')
                ->select(array(
                    'zt_users.id', 'zt_users.token', 'zt_users.first_name', 'zt_users.last_name', 'zt_users.username', DB::Raw('concat(zt_users.first_name," ",zt_users.last_name) as name'),
                    'zt_users.email', 'pa_membres.ppa', 'zt_v_groups_by_userid.lesgroupes', 'zt_users.activated', 'zt_users.created_at', 'zt_users.last_activity'
                ))
                ->where('zt_users.id', $id)
                ->firstOrFail();
            return new MemberResource($user);
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
