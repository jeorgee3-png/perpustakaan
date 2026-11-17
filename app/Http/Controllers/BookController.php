<?php

namespace App\Http\Controllers;

use App\Models\Book;
use App\Models\Loan;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class BookController extends Controller
{
    public function index(Request $request): View
    {
        $search = trim((string) $request->query('q', ''));
        $availability = (string) $request->query('availability', 'all');
        $sort = (string) $request->query('sort', 'latest');

        $query = Book::query();

        if ($search !== '') {
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                    ->orWhere('author', 'like', "%{$search}%")
                    ->orWhere('publisher', 'like', "%{$search}%");
            });
        }

        if ($availability === 'available') {
            $query->where('stock', '>', 0);
        } elseif ($availability === 'empty') {
            $query->where('stock', '<=', 0);
        }

        match ($sort) {
            'title_asc' => $query->orderBy('title'),
            'year_desc' => $query->orderByDesc('year')->orderBy('title'),
            'stock_desc' => $query->orderByDesc('stock')->orderBy('title'),
            default => $query->latest(),
        };

        $books = $query->paginate(9)->withQueryString();

        $stats = [
            'totalTitles' => Book::count(),
            'totalStock' => (int) Book::sum('stock'),
            'totalMembers' => User::where('role', 'siswa')->count(),
            'activeLoans' => Loan::where('status', 'dipinjam')->count(),
        ];

        $userActiveBookIds = [];
        $userActiveCount = 0;

        if (Auth::check()) {
            $userActiveBookIds = Loan::where('user_id', Auth::id())
                ->where('status', 'dipinjam')
                ->pluck('book_id')
                ->all();
            $userActiveCount = count($userActiveBookIds);
        }

        return view('books.index', compact(
            'books',
            'stats',
            'search',
            'availability',
            'sort',
            'userActiveBookIds',
            'userActiveCount'
        ));
    }

    public function show(Book $book): View
    {
        $userActiveLoan = null;
        $userActiveCount = 0;

        if (Auth::check()) {
            $userActiveLoans = Loan::where('user_id', Auth::id())
                ->where('status', 'dipinjam')
                ->get();
            $userActiveCount = $userActiveLoans->count();
            $userActiveLoan = $userActiveLoans->firstWhere('book_id', $book->id);
        }

        $activeLoansCount = $book->activeLoans()->count();
        $recentLoans = $book->loans()
            ->with('user')
            ->latest('loan_date')
            ->limit(5)
            ->get();

        return view('books.show', compact(
            'book',
            'userActiveLoan',
            'userActiveCount',
            'activeLoansCount',
            'recentLoans'
        ));
    }
}
