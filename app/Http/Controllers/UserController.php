<?php

namespace App\Http\Controllers;

use App\Models\User;
use Inertia\Inertia;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;


class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $perPage = $request->input('per_page', 10);
        $search = $request->input('search');
        $role = $request->input('role');
        $users = User::with('roles')
            ->when($search, function ($query, $search) {
                $query->where(function ($q) use ($search) {
                    $q->where('name', 'like', "%$search%")
                        ->orWhere('username', 'like', "%$search%");
                });
            })->when($role, function ($query, $role) {
                $query->whereHas('roles', function ($q) use ($role) {
                    $q->where('name', '=', $role);
                });
            })
            ->paginate($perPage)
            ->withQueryString();

        return Inertia::render("Users/Index", [
            "users" => $users,
            "roles" => Role::all(),
            'filters' => $request->only(['search', 'role']),
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $roleQuery = Role::query();
        $user = auth()->user();
        if (!$user->hasRole('Super Admin')) {
            $roleQuery->whereNot('name', 'Super Admin')->WhereNot('name', 'admin');
        }

        return Inertia::render("Users/Create", [
            "roles" => $roleQuery->get(),
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // dd($request->all());

        $request->validate([
            'name' => 'required|string|max:255',
            'username' => 'required|string|max:255|regex:/^[a-zA-Z0-9_]+$/|unique:' . User::class,
            'email' => 'required|string|lowercase|email|max:255|unique:' . User::class,
            'password' => [
                'required',
                'confirmed',
                Rules\Password::defaults(),
                Password::min(8)->numbers()->symbols()->max(255)->mixedCase(),
                'regex:/^[A-Za-z0-9_\-!@#$%^&*()+=\[\]{}]+$/',
            ],
            'verified_email' => ['nullable', 'boolean'],
            'is_active' => 'boolean',

        ], [
            'name.required' => 'Name is required.',
            'username.unique' => 'Username is already taken.',
            'username.regex' => 'Username can only contain letters, numbers, and underscores.',
            'email.required' => 'Email is required.',
            'password.required' => 'Password is required.',
            'password.regex' => 'Password cant contain Space.',
            'password.confirmed' => 'Passwords do not match'
        ]);

        if ($request->boolean('verified_email') && $request->verified_email) {
            $email = now();
        } else {
            $email = null;
        }
        $user = User::create([
            'name' => $request->name,
            'username' => $request->username,
            'email' => $request->email,
            'password' =>  Hash::make($request->password),
            'email_verified_at' => $email,
            'is_active' => $request->boolean('is_active') ? true : false,
        ]);
        $user->syncRoles($request->roles);


        return to_route("users.index")->with("message", "Success Create User");
    }


    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $user = User::findOrFail($id);
        return Inertia::render("Users/Show", [
            "user" => [
                'id' => $user->id,
                'avatar' => $user->avatar,
                'name' => $user->name,
                'username' => $user->username,
                'email' => $user->email,
                'email_verified_at' => $user->email_verified_at ? $user->email_verified_at->format('Y-m-d H:i:s') : null,
                'created_at' => $user->created_at->format('Y-m-d H:i:s'),
                'updated_at' => $user->updated_at->format('Y-m-d H:i:s'),
            ],
            "roles" => Role::all(),
            "userRoles" => User::findOrFail($id)->roles->pluck("name")->all(),
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $user = User::findOrFail($id);
        if ($user->hasRole('Super Admin') && !auth()->user()->hasRole('Super Admin')) {
            abort(403, 'You are not allowed to edit a Super Admin.');
        };
        if ($user->hasRole('admin') && !auth()->user()->hasRole('Super Admin')) {
            abort(403, 'Admin does not allowed to edit another Admin.');
        }
        $roleQuery = Role::query();
        $auth = auth()->user();
        if (!$auth->hasRole('Super Admin')) {
            $roleQuery->whereNot('name', 'Super Admin')->WhereNot('name', 'admin');
        }
        return Inertia::render("Users/Edit", [
            "user" => [
                'id' => $user->id,
                'name' => $user->name,
                'username' => $user->username,
                'email' => $user->email,
                'verified_email' => $user->hasVerifiedEmail(),
            ],
            "roles" =>$roleQuery->get(),
            "userRoles" => $user->roles->pluck("name")->all(),
        ]);
        // return Inertia::render("Users/Edit", [
        //     "user" => User::find($id),
        // ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id,)
    {
        $user = User::findOrFail($id);
        if ($user->hasRole('Super Admin') && !auth()->user()->hasRole('Super Admin')) {
            abort(403, 'You are not allowed to edit a Super Admin.');
        };
        if ($user->hasRole('admin') && !auth()->user()->hasRole('Super Admin')) {
            abort(403, 'Admin does not allowed to edit another Admin.');
        }
        $validated = $request->validate([
            'name' => 'nullable|string|max:255',
            'username' => [
                'nullable',
                'string',
                'max:255',
                'regex:/^[a-zA-Z0-9_]+$/',
                Rule::unique('users')->ignore($id),
            ],
            'email' => [
                'nullable',
                'string',
                'lowercase',
                'email',
                'max:255',
                Rule::unique('users')->ignore($id),
            ],
            // 'password' => [
            //     'required',
            //     'confirmed',
            //     Rules\Password::defaults(),
            //     Password::min(8)->numbers()->symbols()->max(255)->mixedCase(),
            //     'regex:/^[A-Za-z0-9_\-!@#$%^&*()+=\[\]{}]+$/',
            // ],
            'verified_email' => ['nullable', 'boolean'],

        ], [
            'name.required' => 'Name is required.',
            'username.unique' => 'Username is already taken.',
            'username.regex' => 'Username can only contain letters, numbers, and underscores.',
            'email.required' => 'Email is required.',
            // 'password.required' => 'Password is required.',
            'password.regex' => 'Password cant contain Space.',
            'password.confirmed' => 'Passwords do not match'
        ]);
        if ($request->boolean('verified_email') && $request->verified_email) {
            $user = User::findOrFail($id);
            $user->email_verified_at = now();
            $user->save();
        } else {
            $user = User::findOrFail($id);
            $user->email_verified_at = null;
            $user->save();
        }
        if ($request->filled('password')) {
            $user = User::findOrFail($id);
            $user->password = Hash::make($request->password);
        }
        $user = User::findOrFail($id); // cari user berdasarkan ID
        $user->fill($validated);
        $user->save();
        // Cek kalau user login bukan super admin
        if (!auth()->user()->hasRole('Super Admin')) {

            // 1. Cek jika user target punya role admin/super admin
            if ($user->hasAnyRole(['admin', 'Super Admin'])) {
                abort(403, 'You are not allowed to edit another admin or super admin.');
            }

            // 2. Cek jika request ingin memberi role admin/super admin
            if (in_array('admin', (array) $request->roles) || in_array('Super Admin', (array) $request->roles)) {
                abort(403, 'You are not allowed to assign admin or super admin roles.');
            }
        }


        $user->syncRoles($request->roles);
        return to_route("users.index")->with("message", "Success Create User");
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $user = User::findOrFail($id);
        if ($user->hasRole('admin') && !auth()->user()->hasRole('Super Admin')) {
            abort(403, 'You are not allowed to delete another Admin.');
        };
        if ($user->hasRole('Super Admin') && !auth()->user()->hasRole('Super Admin')) {
            abort(403, 'You are not allowed to delete Super Admin.');
        };
        User::destroy($id);
        return to_route("users.index")->with("message", "Success Delete User");
    }

    public function toggleStatus(string $id)
    {
        $auth = auth()->user();
        $user = User::findOrFail($id);
        if(!$auth->can('users.toggleStatus')){
            abort(403, 'You are not allowed to toggle Status Users.');

        }
        $user->is_active = !$user->is_active;
        $user->save();
        return redirect()->back()->with('message', 'Status berhasil diubah.');
    }
}
