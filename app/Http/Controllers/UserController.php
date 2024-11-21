<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class UserController extends Controller
{
    // Display the profile edit form
    public function edit()
    {
        $user = Auth::user(); // Get the authenticated user
        $groups = \App\Models\Group::all(); // Fetch all available groups
        return view('profile.edit', compact('user', 'groups'));
    }

    // Handle profile update
    public function update(Request $request)
    {
        $user = Auth::user();
    
        // Validate the request
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email,' . $user->id,
            'password' => 'nullable|string|min:8|confirmed', // Password confirmation required if provided
            'group_id' => 'required|exists:groups,id', // Validate the group ID
        ]);
    
        // Update user details
        $user->name = $request->name;
        $user->email = $request->email;
        $user->group_id = $request->group_id; // Assign the selected group
    
        // Update password if provided
        if ($request->filled('password')) {
            $user->password = Hash::make($request->password);
        }
    
        $user->save();
    
        return redirect()->route('profile.edit')->with('success', 'Profile updated successfully.');
    }

    public function editGroup($id)
    {
        $user = User::findOrFail($id);
        $groups = Group::all();
    
        return view('users.edit-group', compact('user', 'groups'));
    }
    
    public function updateGroup(Request $request, $id)
    {
        $request->validate([
            'group_id' => 'required|exists:groups,id',
        ]);
    
        $user = User::findOrFail($id);
        $user->group_id = $request->group_id;
        $user->save();
    
        return redirect()->route('users.index')->with('success', 'User group updated successfully.');
    }
    
}
