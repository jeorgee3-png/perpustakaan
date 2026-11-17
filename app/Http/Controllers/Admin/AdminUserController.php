<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AdminUserController extends Controller
{
    public function index(Request $request): View
    {
        $search = trim((string) $request->query('q', ''));
        $role = (string) $request->query('role', 'all');

        $query = User::withCount([
            'loans as active_loans_count' => fn ($q) => $q->where('status', 'dipinjam'),
            'loans as total_loans_count',
        ]);

        if ($search !== '') {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('kelas', 'like', "%{$search}%");
            });
        }

        if (in_array($role, ['admin', 'siswa'], true)) {
            $query->where('role', $role);
        }

        $users = $query->orderBy('role')->orderBy('name')->paginate(12)->withQueryString();

        return view('admin.users.index', compact('users', 'search', 'role'));
    }
}
