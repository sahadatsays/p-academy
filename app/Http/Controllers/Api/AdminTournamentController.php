<?php

namespace App\Http\Controllers\Api;

use App\Helpers\QueryHelper;
use App\Http\Requests\TournamentRequest;
use App\Http\Resources\TournamentResource;
use App\Models\Tournament;
use Illuminate\Http\Request;

class AdminTournamentController extends ApiController
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $perPage = $request->input('itemsPerPage', 15);

        $query = Tournament::query();

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
            $columns = ['titre', 'id'];
            $query = QueryHelper::searchAll($query, $search, $columns);
        }

        // Pagination
        $tournaments = $query->paginate($perPage);

        return TournamentResource::collection($tournaments);
    }

    private function sortBy($query, $key, $order)
    {
        if ($key == 'id') {
            return $query->orderBy('pa_tournois.id', $order);
        }

        if ($key == 'titre') {
            return $query->orderBy('titre', $order);
        }
        
        if ($key == 'operator.name') {
            return $query->withAggregate('operator', 'nom')->orderBy('operator_nom', $order);
        }

        if ($key == 'since') {
            return $query->orderBy('buyin', $order);
        }

        if ($key == 'type_tournament') {
            return $query->orderBy('typetournoi', $order);
        }
        if ($key == 'password') {
            return $query->orderBy('password', $order);
        }

        if ($key == 'added_op') {
            return $query->orderBy('added_op', $order);
        }

        if ($key == 'start_date') {
            return $query->orderBy('date_debut', $order);
        }

        if ($key == 'end_date') {
            return $query->orderBy('date_fin', $order);
        }

        if ($key == 'article_id') {
            return $query->orderBy('article_id', $order);
        }

        return $query->orderBy($key, $order);
    }

    private function filters($query, $request)
    {
        if ($request->get('id')) {
            $query->where('id', $request->id);
        }

        if ($request->get('title')) {
            $query->where('titre', 'LIKE', '%' . $request->title . '%');
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
    public function store(TournamentRequest $request)
    {
        $data = $request->validated();
        unset($data['heure_fin_submit']);
        unset($data['heure_debut_submit']);

        $tournament = Tournament::create($data);
        return $this->sendResponse(new TournamentResource($tournament), 'Le tournoi a été ajouté!');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
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
    public function update(TournamentRequest $request, string $id)
    {
        try {
            $tournament = Tournament::findOrFail($id);
            $tournament->update($request->validated());
            return $this->sendResponse(new TournamentResource($tournament), 'Le tournoi a été modifié!');
        } catch (\Throwable $th) {
            return $this->sendError($th->getMessage());
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
