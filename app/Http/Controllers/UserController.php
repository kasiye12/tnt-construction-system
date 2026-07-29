<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Site;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $users = User::with(['site'])
            ->when($request->search, fn($q) => 
                $q->where('full_name', 'like', '%' . $request->search . '%')
                  ->orWhere('email', 'like', '%' . $request->search . '%')
                  ->orWhere('phone_number', 'like', '%' . $request->search . '%')
            )
            ->when($request->status, fn($q) => $q->where('status', $request->status))
            ->latest()
            ->paginate(15);

        return view('users.index', compact('users'));
    }

    public function create()
    {
        $sites = Site::where('status', 'active')->get();
        return view('users.create', compact('sites'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'full_name' => 'required|string|max:255',
            'email' => 'required|email|unique:users',
            'phone_number' => 'required|string|unique:users',
            'password' => 'required|string|min:8',
            'site_id' => 'nullable|exists:sites,id',
            'employee_id' => 'nullable|string|unique:users',
            'department' => 'nullable|string',
            'position' => 'nullable|string',
            'status' => 'required|in:active,inactive,suspended',
        ]);

        $validated['uuid'] = Str::uuid();
        $validated['password'] = Hash::make($validated['password']);
        $validated['email_verified_at'] = now();
        
        User::create($validated);

        return redirect()->route('users.index')
            ->with('success', 'User created successfully!');
    }

    public function show(User $user)
    {
        $user->load(['site', 'checkins' => function($q) {
            $q->latest()->take(10);
        }]);
        
        return view('users.show', compact('user'));
    }

    public function edit(User $user)
    {
        $sites = Site::all();
        return view('users.edit', compact('user', 'sites'));
    }

    public function update(Request $request, User $user)
    {
        $validated = $request->validate([
            'full_name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'phone_number' => 'required|string|unique:users,phone_number,' . $user->id,
            'password' => 'nullable|string|min:8',
            'site_id' => 'nullable|exists:sites,id',
            'employee_id' => 'nullable|string|unique:users,employee_id,' . $user->id,
            'department' => 'nullable|string',
            'position' => 'nullable|string',
            'status' => 'required|in:active,inactive,suspended',
        ]);

        if (!empty($validated['password'])) {
            $validated['password'] = Hash::make($validated['password']);
        } else {
            unset($validated['password']);
        }

        $user->update($validated);

        return redirect()->route('users.index')
            ->with('success', 'User updated successfully!');
    }

    public function destroy(User $user)
    {
        if ($user->id === auth()->id()) {
            return back()->with('error', 'Cannot delete yourself!');
        }
        
        $user->delete();
        return redirect()->route('users.index')
            ->with('success', 'User deleted successfully!');
    }

    public function toggleStatus(User $user)
    {
        $user->status = $user->status === 'active' ? 'inactive' : 'active';
        $user->save();
        
        return back()->with('success', 'User status updated!');
    }
}
