<?php

namespace App\Services;

use App\Models\Book;
use App\Models\Loan;
use App\Models\User;
use Carbon\CarbonInterface;
use Illuminate\Support\Facades\DB;
use RuntimeException;

class LoanService
{
    /**
     * Create a book loan atomically while enforcing stock and active-loan limits.
     */
    public function borrowBook(
        User $user,
        Book $book,
        ?CarbonInterface $loanDate = null,
        ?CarbonInterface $dueDate = null
    ): Loan {
        $loanDate ??= now();
        $dueDate ??= $loanDate->copy()->addDays(Loan::LOAN_DAYS);

        return DB::transaction(function () use ($user, $book, $loanDate, $dueDate): Loan {
            $lockedBook = Book::whereKey($book->id)->lockForUpdate()->firstOrFail();

            if ($lockedBook->stock < 1) {
                throw new RuntimeException('Maaf, stok buku "' . $lockedBook->title . '" sedang habis.');
            }

            $activeCount = Loan::where('user_id', $user->id)
                ->where('status', 'dipinjam')
                ->lockForUpdate()
                ->count();

            if ($activeCount >= Loan::MAX_ACTIVE_LOANS) {
                throw new RuntimeException('Maksimal peminjaman aktif adalah ' . Loan::MAX_ACTIVE_LOANS . ' buku.');
            }

            $alreadyBorrowed = Loan::where('user_id', $user->id)
                ->where('book_id', $lockedBook->id)
                ->where('status', 'dipinjam')
                ->exists();

            if ($alreadyBorrowed) {
                throw new RuntimeException('Buku "' . $lockedBook->title . '" masih dalam status dipinjam oleh anggota ini.');
            }

            $loan = Loan::create([
                'user_id' => $user->id,
                'book_id' => $lockedBook->id,
                'loan_date' => $loanDate->toDateString(),
                'due_date' => $dueDate->toDateString(),
                'status' => 'dipinjam',
                'fine' => 0,
            ]);

            $lockedBook->decrement('stock');

            return $loan;
        });
    }

    /**
     * Mark an active loan as returned, calculate any late fine, and restore book stock.
     */
    public function returnLoan(Loan $loan): Loan
    {
        return DB::transaction(function () use ($loan): Loan {
            $lockedLoan = Loan::with('book')->whereKey($loan->id)->lockForUpdate()->firstOrFail();

            if ($lockedLoan->status !== 'dipinjam') {
                throw new RuntimeException('Peminjaman ini sudah dikembalikan sebelumnya.');
            }

            $fine = $lockedLoan->overdueDays(now()) * Loan::DAILY_FINE;

            $lockedLoan->update([
                'returned_at' => now()->toDateString(),
                'status' => 'dikembalikan',
                'fine' => $fine,
            ]);

            $lockedLoan->book->increment('stock');

            return $lockedLoan->fresh(['book', 'user']);
        });
    }
}
