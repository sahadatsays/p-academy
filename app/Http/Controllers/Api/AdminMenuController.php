<?php

namespace App\Http\Controllers\Api;

use App\Helpers\QueryHelper;
use App\Http\Controllers\Api\ApiController;
use App\Http\Controllers\Controller;
use App\Http\Resources\MenuResource;
use App\Models\Menu;
use Illuminate\Http\Request;

class AdminMenuController extends ApiController
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request  $request)
    {
        $perPage = $request->input('itemsPerPage', 25);

        $query = Menu::query();

        // sorting query
        if ($request->get('sortBy')) {
            $sortBy = json_decode($request->get('sortBy'));
            $query = $this->sortBy($query, $sortBy->key, $sortBy->order);
        } else {
            $query = $query->orderBy('id', 'desc');
        }

        // filters by columns
        $query = $this->filters($query, $request);

        // search
        if ($request->has('search')) {
            $search = $request->get('search');
            $columns = ['id', 'name'];
            $query = QueryHelper::searchAll($query, $search, $columns);
        }

        // Pagination
        $menus = $query->with('urlSite')->paginate($perPage);

        return MenuResource::collection($menus);
    }

    private function sortBy($query, $key, $order)
    {
        if ($key == 'email') {
            return $query->orderBy('email', $order);
        }

        if ($key == 'createdAt') {
            return $query->orderBy('created_at', $order);
        }

        if ($key == 'updatedAt') {
            return $query->orderBy('updated_at', $order);
        }

        if ($key == 'status') {
            return $query->orderBy('state', $order);
        }

        if ($key == 'url') {
            return $query->orderBy('url_externe', $order);
        }
        return $query->orderBy($key, $order);
    }

    private function filters($query, $request)
    {
        if ($request->get('id')) {
            $query->where('id', $request->id);
        }

        if ($request->get('name')) {
            $query->where('name', 'LIKE', '%'. $request->name .'%');
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
        $data = $request->validate([
            'name' => 'required|string|max:200',
            'title' => 'required|string',
            'lang' => 'required|string',
            'parent_id' => 'required',
            'url_externe' => 'nullable',
            'target_blank' => 'boolean',
            'obfuscate' => 'nullable',
            'urlsite_id' => 'nullable',
            'status' => 'required',
            'order' => 'required|numeric',
        ]);

        if(!$request->input('url_externe')) {
            $data['urlsite_id'] = $request->input('urlsite_id');
        } else {
            $data['urlsite_id'] = 0;
        }

        $menu = Menu::create([
            'name'          => $data['name'],
            'menu_id'       => $data['parent_id'],
            'url_externe'   => $data['url_externe'],
            'urlsite_id'    => $data['urlsite_id'],
            'order'         => $data['order'],
            'state'         => $data['status'],
            'target_blank'  => $data['target_blank'] ? 1 : 0,
            'obfuscate'     => $data['obfuscate'] ? 1 : 0,
            'rules'         => ''
        ]);

        // menu translation
        $menuTranslation = $menu->translations()->create([
            'lang' => $data['lang'],
            'title' => $data['title'],
        ]);

        return $this->sendResponse(new MenuResource($menu), 'Menu has been added!');
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
        $menu = Menu::find($id);
        if ($menu == null) {
            return $this->sendError('Menu not found');
        }

        $result['menu'] = new MenuResource($menu);
        $result['translation'] = $menu->translations()->first();

        return $this->sendResponse($result, 'Edit data');
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $menu = Menu::find($id);
        if ($menu == null) {
            return $this->sendError('Menu not found');
        }

        $data = $request->validate([
            'name' => 'required|string|max:200',
            'title' => 'required|string',
            'lang' => 'required|string',
            'parent_id' => 'required',
            'url_externe' => 'nullable',
            'target_blank' => 'boolean',
            'obfuscate' => 'nullable',
            'urlsite_id' => 'nullable',
            'status' => 'required',
            'order' => 'required|numeric',
        ]);

        if(!$request->input('url_externe')) {
            $data['urlsite_id'] = $request->input('urlsite_id');
        } else {
            $data['urlsite_id'] = 0;
        }

        $menu->update([
            'name'          => $data['name'],
            'menu_id'       => $data['parent_id'],
            'url_externe'   => $data['url_externe'],
            'urlsite_id'    => $data['urlsite_id'],
            'order'         => $data['order'],
            'state'         => $data['status'],
            'target_blank'  => $data['target_blank'] ? 1 : 0,
            'obfuscate'     => $data['obfuscate'] ? 1 : 0,
            'rules'         => ''
        ]);

        // menu translation
        $menu->translations()->first()->update([
            'lang' => $data['lang'],
            'title' => $data['title'],
        ]);

        return $this->sendResponse(new MenuResource($menu), 'Menu has been updated!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $menu = Menu::find($id);
        if ($menu == null) {
            return $this->sendError('Menu not found!');
        }

        $menu->translations()->delete();
        $menu->delete();
        return $this->sendResponse(null, 'Menu has been deleted');
    }
}
