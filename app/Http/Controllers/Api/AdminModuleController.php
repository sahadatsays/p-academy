<?php

namespace App\Http\Controllers\Api;

use App\Helpers\QueryHelper;
use App\Http\Controllers\Api\ApiController;
use App\Http\Controllers\Controller;
use App\Http\Resources\ModuleResource;
use App\Models\Module;
use Illuminate\Http\Request;

class AdminModuleController extends ApiController
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $perPage = $request->input('itemsPerPage', 15);

        $query = Module::query();

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
        $modules = $query->paginate($perPage);

        return ModuleResource::collection($modules);
    }

    private function sortBy($query, $key, $order)
    {
        if ($key == 'createdAt') {
            return $query->orderBy('created_at', $order);
        }

        if ($key == 'updatedAt') {
            return $query->orderBy('updated_at', $order);
        }

        if ($key == 'status') {
            return $query->orderBy('state', $order);
        }

        return $query->orderBy($key, $order);
    }

    private function filters($query, $request)
    {

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
            'title' => 'required|string',
            'name' => 'required|string',
            'position' => 'nullable|string',
            'status' => 'nullable',
            'show_title' => 'boolean',
            'order' => 'nullable|numeric',
            'lang' => 'required|string',
            'content' => 'nullable|string'
        ]);

        // new module
        $module = Module::create([
            'name' => $data['name'], 
            'position' => $data['position'] ?? '',
            'state' => $data['status'],
            'show_title' => $data['show_title'] ?? 0,
            'order' => $data['order'],
            'created_by' => 5,
            'rules' => ''
        ]);

        // Module Translation
        $moduleTranslation = $module->translations()->create([
            'lang' => $data['lang'],
            'title' => $data['title'],
            'content'   => $data['content'],
        ]);

        // module Rules
        $moduleRules = $module->modulerules()->create([
            'show' => 1,
            'type' => 'all'
        ]);

        return $this->sendResponse($module, 'New Module has been created!');
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
        $module = Module::find($id);
        if ($module == null) {
            return $this->sendError('Module not found!');
        }

        $data['module'] = new ModuleResource($module);
        $data['moduleTranslation'] = $module->translations()->first();

        return $this->sendResponse($data, 'Module found!');
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $module = Module::find($id);
        if ($module == null) {
            return $this->sendError('Module not found');
        }

        $data = $request->validate([
            'title' => 'required|string',
            'name' => 'required|string',
            'position' => 'nullable|string',
            'status' => 'nullable',
            'show_title' => 'boolean',
            'order' => 'nullable|numeric',
            'lang' => 'required|string',
            'content' => 'nullable|string'
        ]);

        // new module
        $module->update([
            'name' => $data['name'], 
            'position' => $data['position'] ?? '',
            'state' => $data['status'],
            'show_title' => $data['show_title'] ?? 0,
            'order' => $data['order'],
            'created_by' => 5,
            'rules' => ''
        ]);

        // Module Translation
        $module->translations()->first()->update([
            'lang' => $data['lang'],
            'title' => $data['title'],
            'content'   => $data['content'],
        ]);

        // module Rules
        // $moduleRules = $module->modulerules()->create([
        //     'show' => 1,
        //     'type' => 'all'
        // ]);

        return $this->sendResponse($module, 'New Module has been updated!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $module = Module::find($id);
        if ($module == null) {
            return $this->sendError('Module not found!');
        }

        $module->translations()->delete();
        $module->modulerules()->delete();
        $module->delete();
        return $this->sendResponse(null, 'Module has been deleted!');
    }

    public function publish (Module $module)
    {
        if ( $module->state == 0 ) {
            $module->state = 1;
            $message = 'Votre module a été publié';
        }
        elseif ( $module->state == 1 ) {
            $module->state = 0;
            $module->save();
            $message = 'Votre module a été dépublié';
        } elseif ( $module->state == 2 ) {
            $module->state = 1;
            $message = 'Votre module a été publié';
        }
        $module->save();

        return $this->sendResponse($module, $message);
    }
}
