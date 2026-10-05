<?php

namespace App\Http\Controllers;

// Import the User model.
// This allows the controller to work with the users table.
use App\Models\User;

// Request contains the data submitted from forms.
use Illuminate\Http\Request;

// Hash is used to securely encrypt/hash passwords
// before saving them to the database.
use Illuminate\Support\Facades\Hash;

// Rule allows us to handle the unique email rule
// when updating an existing user.
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    // ============================================================
    // DISPLAY ALL USERS
    // ============================================================
    //
    // Business purpose:
    // Show the administrator all users/staff accounts.
    //
    public function index(Request $request)
    {
        $query = User::orderBy('id', 'asc');

        // 1. Search by ID, Name, or Email
        if ($request->filled('search')) {
            $search = $request->input('search');

            $query->where(function ($q) use ($search) {
                $q->where('id', 'like', "%{$search}%")
                    ->orWhere('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%");
            });
        }

        // 2. Filter by Role ('admin' or 'staff')
        if ($request->filled('role') && $request->role !== 'all') {
            $query->where('role', $request->role);
        }

        // 3. Filter by Account Status ('active' or 'disabled')
        if ($request->filled('status') && $request->status !== 'all') {
            $query->where('status', $request->status);
        }
        
        $users = $query->paginate(10)->withQueryString();

        return view('users.users-index', [
            'users' => $users
        ]);
    }


    // ============================================================
    // SHOW CREATE USER FORM
    // ============================================================
    //
    // Business purpose:
    // Display the form where an administrator can
    // create a new staff/user account.
    //
    public function create()
    {
        // Display the create user page.
        return view('users.add_user');
    }


    // ============================================================
    // SAVE NEW USER
    // ============================================================
    //
    // Business process:
    //
    // Admin enters user information
    //          ↓
    // Validate information
    //          ↓
    // Hash password
    //          ↓
    // Create user account
    //
    public function store(Request $request)
    {
        // Step 1: Validate the information submitted by the admin.
        $request->validate([
            // User's name is required.
            'name' => 'required|string|max:100',

            // Email is required and must be unique.
            'email' => 'required|email|max:255|unique:users,email',

            // Password is required when creating a new account.
            'password'  => 'required|string|min:6',

            'role'      => 'required|in:manager,secretary,sales-clerk,technician,admin,staff',

            'status' => 'required|in:active,disabled',
        ]);

        $dbRole = ($request->input('role') === 'manager') ? 'admin' : 'staff';

        $user = new User();
        $user->name = $request->input('name');
        $user->email = $request->input('email');
        $user->password = Hash::make($request->input('password'));
        $user->role = $dbRole;
        $user->status = $request->input('status');
        $user->save();

        return redirect('/user-management')
            ->with('success', 'User created successfully.');
    }


    public function show($id)
    {
        $user = User::findOrFail($id);

        // No dedicated user detail page: the users list is canonical.
        return redirect()->route('users.index');
    }


    public function edit($id)
    {
        $user = User::findOrFail($id);

        // Uses edit.blade.php from resources/views/users/edit.blade.php
        return view('users.edit', [
            'user' => $user
        ]);
    }


    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id);

        $request->validate([
            'name' => 'required|string|max:100',

            'email' => [
                'required',
                'email',
                'max:255',
                Rule::unique('users', 'email')->ignore($user->id),
            ],

            'password' => 'nullable|string|min:8|confirmed',

            // Accept both select options and underlying DB roles
            'role' => 'required|in:manager,secretary,sales-clerk,technician,admin,staff',

            // Only values the users.status enum accepts.
            'status' => 'required|in:active,disabled',
        ]);

        $roleInput = $request->input('role');
        if ($roleInput === 'manager') {
            $dbRole = 'admin';
        } elseif (in_array($roleInput, ['secretary', 'sales-clerk', 'technician'])) {
            $dbRole = 'staff';
        } else {
            $dbRole = $roleInput; // Preserves admin or staff if already set
        }

        $user->name   = $request->input('name');
        $user->email  = $request->input('email');
        $user->role   = $dbRole;
        $user->status = $request->input('status');

        if ($request->filled('password')) {
            $user->password = Hash::make($request->input('password'));
        }

        $user->save();

        return redirect('/user-management')
            ->with('success', 'User updated successfully.');
    }


    public function destroy($id)
    {
        $user = User::findOrFail($id);

        $user->delete();

        return redirect('/user-management')
            ->with('success', 'User deleted successfully.');
    }
}