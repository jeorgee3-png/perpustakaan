<?php

namespace App\Http\Controllers;

use App\Models\Book;
use App\Models\Loan;
use App\Services\LoanService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use RuntimeException;

class LoanController extends Controller
{
    public function __construct(private readonly LoanService $loanService) {}

    public function myLoans(Request $request): View
    {
        $filter = (string) $request->query('status', 'all');
        $userId = Auth::id();

        $query = Loan::with('book')
            ->where('user_id', $userId)
            ->latest('loan_date')
            ->latest('id');

        if ($filter === 'dipinjam') {
            $query->where('status', 'dipinjam');
        } elseif ($filter === 'dikembalikan') {
            $query->where('status', 'dikembalikan');
        } elseif ($filter === 'terlambat') {
            $query->where('status', 'dipinjam')
                ->whereDate('due_date', '<', now()->toDateString());
        }

        $loans = $query->paginate(10)->withQueryString();

        $allUserLoans = Loan::where('user_id', $userId)->get();
        $summary = [
            'active' => $allUserLoans->where('status', 'dipinjam')->count(),
            'overdue' => $allUserLoans->filter(fn (Loan $loan) => $loan->isOverdue())->count(),
            'returned' => $allUserLoans->where('status', 'dikembalikan')->count(),
            'totalFine' => $allUserLoans->sum(fn (Loan $loan) => $loan->estimatedFine()),
        ];

        return view('loans.my', compact('loans', 'filter', 'summary'));
    }

    public function borrow(Request $request, Book $book): RedirectResponse
    {
        try {
            $loan = $this->loanService->borrowBook($request->user(), $book);
        } catch (RuntimeException $e) {
            return back()->with('error', $e->getMessage());
        }

        return redirect()->route('loans.my')
            ->with('success', 'Buku "' . $book->title . '" berhasil dipinjam. Batas pengembalian: ' . $loan->due_date->translatedFormat('d M Y') . '.');
    }

    public function returnBook(Request $request, Loan $loan): RedirectResponse
    {
        $user = $request->user();
        if ($loan->user_id !== $user->id && ! $user->isAdmin()) {
            abort(403, 'Anda tidak berhak mengembalikan peminjaman ini.');
        }

        try {
            $returned = $this->loanService->returnLoan($loan);
        } catch (RuntimeException $e) {
            return back()->with('error', $e->getMessage());
        }

        $message = 'Buku "' . $returned->book->title . '" telah dikembalikan.';
        if ($returned->fine > 0) {
            $message .= ' Denda keterlambatan: Rp ' . number_format($returned->fine, 0, ',', '.') . '.';
        }

        return back()->with('success', $message);
    }
}
