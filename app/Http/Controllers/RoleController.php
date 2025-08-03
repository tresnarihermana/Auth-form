<?php

namespace App\Http\Controllers;

use Inertia\Inertia;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RoleController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $perPage = $request->input('per_page', 10);
        $search = $request->input('search');
        $permission = $request->input('permission');
        $roles = Role::with('permissions')
            ->when($search, function ($query, $search) {
                $query->where(function ($q) use ($search) {
                    $q->where('name', 'like', "%$search%");
                });
            })->when($permission, function ($query, $permission) {
                $query->whereHas('permissions', function ($q) use ($permission) {
                    $q->where('name', '=', $permission);
                });
            })
            ->paginate($perPage)
            ->withQueryString();
        return Inertia::render("Roles/Index", [
            "roles" => $roles,
            "permissions" => Permission::all(),
            'filters' => $request->only(['search','role']),
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return Inertia::render("Roles/Create", [
            "permissions" => Permission::all(),
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'permissions' => 'nullable|array',
        ]);

        $role = Role::create([
            'name' => $request->name,
        ]);
        $role->syncPermissions($request->permissions);

        return to_route("roles.index")->with("message", "Success Create Role");
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        return Inertia::render("Roles/Index");
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $role = Role::findOrFail($id);
        return Inertia::render("Roles/Edit", [
            "role" => $role,
            "rolePermissions" => $role->permissions->pluck("name")->all(),
            "permissions" => Permission::all(),
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        // dd($request->all());
        $request->validate([
            'name' => 'required',
            'permissions' => 'nullable',
        ]);

        $role = Role::findOrFail($id);
        $role->name = $request->name;
        $role->save();
        $role->syncPermissions($request->permissions);

        return to_route("roles.index")->with("message", "Success Create Role");
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $role = Role::findOrFail($id);
        if ($role->name === "Super Admin") {
            abort(403, 'You are not allowed to delete Super Admin Role.');
        };


        Role::destroy($id);
        return to_route("roles.index")->with("message", "Success Delete Role");
    }
}
