<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class UserManagementController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'web.role:admin,manager']);
    }

    /**
     * Show the user management page.
     * Admins see all users; Managers see only Cashiers.
     */
    public function index()
    {
        $currentUser = auth()->user();

        $query = User::query()->where('id', '!=', $currentUser->id);

        if ($currentUser->isManager()) {
            // Managers can only see/manage cashiers
            $query->where('role', 'cashier');
        }

        $users = $query->latest()->get();

        return view('users.index', compact('users', 'currentUser'));
    }

    /**
     * Store a newly created user.
     */
    public function store(Request $request)
    {
        $currentUser = auth()->user();

        // Managers can only create cashiers
        $allowedRoles = $currentUser->isAdmin()
            ? ['admin', 'manager', 'cashier']
            : ['cashier'];

        $validated = $request->validate([
            'name'     => 'required|string|max:255',
            'email'    => 'required|email|unique:users,email',
            'password' => 'required|string|min:8|confirmed',
            'role'     => ['required', Rule::in($allowedRoles)],
            'phone'    => 'nullable|string|max:20',
        ]);

        User::create([
            'name'      => $validated['name'],
            'email'     => $validated['email'],
            'password'  => Hash::make($validated['password']),
            'role'      => $validated['role'],
            'phone'     => $validated['phone'] ?? null,
            'is_active' => true,
        ]);

        return redirect()->route('users.index')
            ->with('success', 'Staff member added successfully.');
    }

    /**
     * Delete a user. Only Admin can delete; cannot delete other Admins.
     */
    public function destroy(User $user)
    {
        $currentUser = auth()->user();

        if (!$currentUser->isAdmin()) {
            abort(403, 'Only Admins can delete users.');
        }

        if ($user->isAdmin()) {
            abort(403, 'Cannot delete an Admin account.');
        }

        if ($user->id === $currentUser->id) {
            abort(403, 'You cannot delete your own account.');
        }

        $user->delete();

        return redirect()->route('users.index')
            ->with('success', 'User deleted successfully.');
    }

    /**
     * Toggle a user's active status.
     */
    public function toggleStatus(User $user)
    {
        $currentUser = auth()->user();

        if ($user->isAdmin() && !$currentUser->isAdmin()) {
            abort(403, 'Only Admins can modify Admin accounts.');
        }

        $user->update(['is_active' => !$user->is_active]);

        return redirect()->route('users.index')
            ->with('success', 'User status updated.');
    }
}
