<?php
// ============================================
// 1. USERCONTROLLER.PHP (DIPERBAIKI)
// ============================================
// app/Http/Controllers/Admin/UserController.php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Role; // TAMBAHAN: Import Model Role
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function index()
    {
        return view('admin.users.index', [
            'users' => User::with('roles')->get()
        ]);
    }

    public function edit(User $user)
    {
        return view('admin.users.edit', [
            'user' => $user,
            'roles' => Role::all()
        ]);
    }

    public function update(Request $request, User $user)
    {
        $user->roles()->sync($request->roles);
        return back()->with('success', 'Role updated successfully');
    }
}

// ============================================
// 2. ROLESEEDER.PHP (DIPERBAIKI)
// ============================================
