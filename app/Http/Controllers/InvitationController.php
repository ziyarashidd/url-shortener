<?php

// app/Http/Controllers/InvitationController.php
namespace App\Http\Controllers;

use App\Models\Invitation;
use App\Models\User;
use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class InvitationController extends Controller
{
    public function create()
    {
        if (!Auth::user()->canInviteUsers()) {
            abort(403);
        }

        $roles = Role::whereNotIn('name', ['SuperAdmin'])->get();

        return view('invitations.create', compact('roles'));
    }

    public function store(Request $request)
    {
        $user = Auth::user();

        if (!$user->canInviteUsers()) {
            abort(403);
        }

        $validated = $request->validate([
            'email' => ['required', 'email', 'unique:users'],
            'role_id' => ['required', 'exists:roles,id'],
        ]);

        $role = Role::find($validated['role_id']);

        // Validation: Admin can't invite Admin or Member
        if ($user->isAdmin() && in_array($role->name, ['Admin', 'Member'])) {
            return back()->withErrors([
                'role_id' => 'Admin can only invite Sales or Manager.',
            ]);
        }

        // Validation: SuperAdmin can't invite Admin to new company
        if ($user->isSuperAdmin() && $role->name === 'Admin') {
            return back()->withErrors([
                'role_id' => 'SuperAdmin cannot invite Admin.',
            ]);
        }

        $invitation = Invitation::create([
            'company_id' => $user->company_id,
            'email' => $validated['email'],
            'role_id' => $role->id,
            'token' => Invitation::generateToken(),
            'expires_at' => now()->addDays(7),
        ]);

        return redirect()->back()->with('success', 'Invitation sent!');
    }

    public function accept($token)
    {
        $invitation = Invitation::where('token', $token)
                               ->whereNull('accepted_at')
                               ->firstOrFail();

        if ($invitation->isExpired()) {
            abort(403, 'Invitation expired.');
        }

        return view('invitations.accept', compact('invitation'));
    }

    public function acceptStore(Request $request, $token)
    {
        $invitation = Invitation::where('token', $token)
                               ->whereNull('accepted_at')
                               ->firstOrFail();

        if ($invitation->isExpired()) {
            abort(403, 'Invitation expired.');
        }

        $validated = $request->validate([
            'name' => ['required', 'string'],
            'password' => ['required', 'min:8', 'confirmed'],
        ]);

        if (User::where('email', $invitation->email)->exists()) {
            return back()->withErrors(['email' => 'User already exists.']);
        }

        $user = User::create([
            'company_id' => $invitation->company_id,
            'role_id' => $invitation->role_id,
            'name' => $validated['name'],
            'email' => $invitation->email,
            'password' => bcrypt($validated['password']),
        ]);

        $invitation->accept();

        Auth::login($user);

        return redirect()->route('dashboard')->with('success', 'Account created!');
    }
}