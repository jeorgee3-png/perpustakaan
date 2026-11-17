<?php

namespace App\Models;

use Carbon\CarbonInterface;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Loan extends Model
{
    public const MAX_ACTIVE_LOANS = 3;
    public const LOAN_DAYS = 7;
    public const DAILY_FINE = 1000;

    protected $fillable = ['user_id', 'book_id', 'loan_date', 'due_date', 'returned_at', 'status', 'fine'];

    protected $casts = [
        'loan_date' => 'date',
        'due_date' => 'date',
        'returned_at' => 'date',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function book(): BelongsTo
    {
        return $this->belongsTo(Book::class);
    }

    public function isOverdue(): bool
    {
        return $this->status === 'dipinjam' && $this->due_date->startOfDay()->lt(now()->startOfDay());
    }

    public function overdueDays(?CarbonInterface $referenceDate = null): int
    {
        $date = ($referenceDate ?? ($this->status === 'dikembalikan' && $this->returned_at ? $this->returned_at : now()))
            ->copy()
            ->startOfDay();

        return max(0, (int) $this->due_date->copy()->startOfDay()->diffInDays($date, false));
    }

    public function estimatedFine(): int
    {
        if ($this->status === 'dikembalikan') {
            return (int) $this->fine;
        }

        return $this->overdueDays() * self::DAILY_FINE;
    }
}
