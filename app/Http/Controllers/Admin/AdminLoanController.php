<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Book;
use App\Models\Loan;
use App\Models\User;
use App\Services\LoanService;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use RuntimeException;

class AdminLoanController extends Controller
{
    public function __construct(private readonly LoanService $loanService) {}

    public function index(Request $request): View
    {
        $search = trim((string) $request->query('q', ''));
        $status = (string) $request->query('status', 'all');

        $query = Loan::with(['user', 'book'])
            ->latest('loan_date')
            ->latest('id');

        if ($search !== '') {
            $query->where(function ($q) use ($search) {
                $q->whereHas('user', function ($uq) use ($search) {
                    $uq->where('name', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%")
                        ->orWhere('kelas', 'like', "%{$search}%");
                })->orWhereHas('book', function ($bq) use ($search) {
                    $bq->where('title', 'like', "%{$search}%")
                        ->orWhere('author', 'like', "%{$search}%");
                });
            });
        }

        if ($status === 'dipinjam') {
            $query->where('status', 'dipinjam');
        } elseif ($status === 'dikembalikan') {
            $query->where('status', 'dikembalikan');
        } elseif ($status === 'terlambat') {
            $query->where('status', 'dipinjam')
                ->whereDate('due_date', '<', now()->toDateString());
        }

        $loans = $query->paginate(12)->withQueryString();

        return view('admin.loans.index', compact('loans', 'search', 'status'));
    }

    public function create(): View
    {
        $users = User::orderBy('name')->get();
        $books = Book::where('stock', '>', 0)->orderBy('title')->get();

        return view('admin.loans.create', compact('users', 'books'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'user_id' => ['required', 'exists:users,id'],
            'book_id' => ['required', 'exists:books,id'],
            'loan_date' => ['required', 'date'],
            'due_date' => ['required', 'date', 'after_or_equal:loan_date'],
        ], [
            'user_id.required' => 'Pilih anggota peminjam.',
            'book_id.required' => 'Pilih buku yang akan dipinjam.',
            'loan_date.required' => 'Tanggal pinjam wajib diisi.',
            'due_date.required' => 'Tanggal jatuh tempo wajib diisi.',
            'due_date.after_or_equal' => 'Tanggal jatuh tempo tidak boleh lebih awal dari tanggal pinjam.',
        ]);

        $user = User::findOrFail($validated['user_id']);
        $book = Book::findOrFail($validated['book_id']);

        try {
            $this->loanService->borrowBook(
                $user,
                $book,
                Carbon::parse($validated['loan_date']),
                Carbon::parse($validated['due_date'])
            );
        } catch (RuntimeException $e) {
            return back()->withInput()->with('error', $e->getMessage());
        }

        return redirect()->route('admin.loans.index')
            ->with('success', 'Peminjaman buku "' . $book->title . '" oleh ' . $user->name . ' berhasil dicatat.');
    }

    public function returnBook(Loan $loan): RedirectResponse
    {
        try {
            $returned = $this->loanService->returnLoan($loan);
        } catch (RuntimeException $e) {
            return back()->with('error', $e->getMessage());
        }

        $message = 'Buku "' . $returned->book->title . '" berhasil dikembalikan.';
        if ($returned->fine > 0) {
            $message .= ' Denda: Rp ' . number_format($returned->fine, 0, ',', '.') . '.';
        }

        return back()->with('success', $message);
    }
}
