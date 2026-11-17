<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Book;
use App\Models\Loan;
use App\Models\User;
use Illuminate\View\View;

class AdminDashboardController extends Controller
{
    public function index(): View
    {
        $stats = [
            'totalBooks' => Book::count(),
            'totalStock' => (int) Book::sum('stock'),
            'totalStudents' => User::where('role', 'siswa')->count(),
            'activeLoans' => Loan::where('status', 'dipinjam')->count(),
            'overdueLoans' => Loan::where('status', 'dipinjam')
                ->whereDate('due_date', '<', now()->toDateString())
                ->count(),
            'collectedFines' => (int) Loan::where('status', 'dikembalikan')->sum('fine'),
        ];

        $recentLoans = Loan::with(['user', 'book'])
            ->latest('loan_date')
            ->latest('id')
            ->limit(8)
            ->get();

        $lowStockBooks = Book::where('stock', '<=', 2)
            ->orderBy('stock')
            ->orderBy('title')
            ->limit(6)
            ->get();

        return view('admin.dashboard', compact('stats', 'recentLoans', 'lowStockBooks'));
    }
}
