<?php

namespace Tests\Unit;

use App\Models\Loan;
use Carbon\Carbon;
use Illuminate\Database\Connection;
use Illuminate\Database\ConnectionResolverInterface;
use Illuminate\Database\Eloquent\Model;
use PHPUnit\Framework\TestCase;

class LoanModelTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        $connection = new class(fn () => null) extends Connection {
            public function getDateFormat()
            {
                return 'Y-m-d H:i:s';
            }
        };

        $resolver = new class($connection) implements ConnectionResolverInterface {
            public function __construct(private Connection $connection) {}

            public function connection($name = null)
            {
                return $this->connection;
            }

            public function getDefaultConnection()
            {
                return 'default';
            }

            public function setDefaultConnection($name) {}
        };

        Model::setConnectionResolver($resolver);
    }

    public function test_overdue_days_and_estimated_fine_for_late_active_loan(): void
    {
        Carbon::setTestNow(Carbon::parse('2026-11-12 10:00:00'));

        $loan = new Loan([
            'loan_date' => '2026-11-01',
            'due_date' => '2026-11-08',
            'status' => 'dipinjam',
            'fine' => 0,
        ]);

        $this->assertTrue($loan->isOverdue());
        $this->assertSame(4, $loan->overdueDays());
        $this->assertSame(4000, $loan->estimatedFine());

        Carbon::setTestNow();
    }

    public function test_on_time_active_loan_has_zero_fine(): void
    {
        Carbon::setTestNow(Carbon::parse('2026-11-12 10:00:00'));

        $loan = new Loan([
            'loan_date' => '2026-11-10',
            'due_date' => '2026-11-17',
            'status' => 'dipinjam',
            'fine' => 0,
        ]);

        $this->assertFalse($loan->isOverdue());
        $this->assertSame(0, $loan->overdueDays());
        $this->assertSame(0, $loan->estimatedFine());

        Carbon::setTestNow();
    }

    public function test_returned_loan_returns_recorded_fine(): void
    {
        Carbon::setTestNow(Carbon::parse('2026-11-12 10:00:00'));

        $loan = new Loan([
            'loan_date' => '2026-11-01',
            'due_date' => '2026-11-08',
            'returned_at' => '2026-11-10',
            'status' => 'dikembalikan',
            'fine' => 2000,
        ]);

        $this->assertFalse($loan->isOverdue());
        $this->assertSame(2, $loan->overdueDays());
        $this->assertSame(2000, $loan->estimatedFine());

        Carbon::setTestNow();
    }
}
