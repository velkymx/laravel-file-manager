<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Hash;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Group;

class AdminController extends Controller
{
    // add a constructor that checks if the user is authenticated and if is_admin is set to 1
    public function __construct()
    {
        // Add a closure-based middleware to check admin privileges
        $this->middleware(function ($request, $next) {
            $user = auth()->user();

            // Abort if the user is not authenticated or not an admin
            if (!$user || $user->is_admin != 1) {
                abort(403, 'Access denied. Admins only.');
            }

            return $next($request);
        });
    }

    /**
     * Display a list of all users.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        // Retrieve all users with their associated groups
        $users = User::with('group')->get();

        return view('admin.users.index', compact('users'));
    }

    /**
     * Show the form for editing a user's group.
     *
     * @param \App\Models\User $user
     * @return \Illuminate\View\View
     */
    public function edit(User $user)
    {
        // Retrieve all available groups
        $groups = Group::all();

        return view('admin.users.edit', compact('user', 'groups'));
    }

    /**
     * Update a user's group.
     *
     * @param \Illuminate\Http\Request $request
     * @param \App\Models\User $user
     * @return \Illuminate\Http\RedirectResponse
     */
    public function updateGroup(Request $request, User $user)
    {
        // Validate the group_id input
        $request->validate([
            'group_id' => 'required|exists:groups,id',
        ]);

        // Update the user's group
        $user->group_id = $request->group_id;
        $user->save();

        return redirect()->route('admin.users.index')->with('success', 'User group updated successfully.');
    }



    public function update(Request $request, User $user)
    {
        // Validate the incoming request
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email,' . $user->id,
            'password' => 'nullable|string|min:8|confirmed',
            'group_id' => 'required|exists:groups,id',
            'is_admin' => 'nullable|boolean',
        ]);

        // Update the user's basic info
        $user->name = $validated['name'];
        $user->email = $validated['email'];
        $user->group_id = $validated['group_id'];
        $user->is_admin = $request->has('is_admin') ? 1 : 0;

        // Update password only if provided
        if (!empty($validated['password'])) {
            $user->password = Hash::make($validated['password']);
        }

        // Save changes
        $user->save();

        return redirect()->route('admin.users.index')->with('success', 'User updated successfully.');
    }
}
