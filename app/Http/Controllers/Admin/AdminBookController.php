<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Book;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AdminBookController extends Controller
{
    public function index(Request $request): View
    {
        $search = trim((string) $request->query('q', ''));

        $query = Book::withCount([
            'loans as active_loans_count' => fn ($q) => $q->where('status', 'dipinjam'),
        ]);

        if ($search !== '') {
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                    ->orWhere('author', 'like', "%{$search}%")
                    ->orWhere('publisher', 'like', "%{$search}%");
            });
        }

        $books = $query->latest()->paginate(10)->withQueryString();

        return view('admin.books.index', compact('books', 'search'));
    }

    public function create(): View
    {
        return view('admin.books.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $this->validateBook($request);

        Book::create($validated);

        return redirect()->route('admin.books.index')
            ->with('success', 'Buku "' . $validated['title'] . '" berhasil ditambahkan.');
    }

    public function edit(Book $book): View
    {
        return view('admin.books.edit', compact('book'));
    }

    public function update(Request $request, Book $book): RedirectResponse
    {
        $validated = $this->validateBook($request);

        $book->update($validated);

        return redirect()->route('admin.books.index')
            ->with('success', 'Data buku "' . $book->title . '" berhasil diperbarui.');
    }

    public function destroy(Book $book): RedirectResponse
    {
        if ($book->activeLoans()->exists()) {
            return back()->with('error', 'Buku "' . $book->title . '" tidak dapat dihapus karena masih sedang dipinjam.');
        }

        $title = $book->title;
        $book->delete();

        return redirect()->route('admin.books.index')
            ->with('success', 'Buku "' . $title . '" berhasil dihapus.');
    }

    /**
     * @return array<string, mixed>
     */
    private function validateBook(Request $request): array
    {
        $maxYear = (int) now()->year + 1;

        return $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'author' => ['required', 'string', 'max:255'],
            'publisher' => ['required', 'string', 'max:255'],
            'year' => ['required', 'integer', 'min:1900', "max:{$maxYear}"],
            'stock' => ['required', 'integer', 'min:0', 'max:10000'],
        ], [
            'title.required' => 'Judul buku wajib diisi.',
            'author.required' => 'Nama penulis wajib diisi.',
            'publisher.required' => 'Nama penerbit wajib diisi.',
            'year.required' => 'Tahun terbit wajib diisi.',
            'year.integer' => 'Tahun terbit harus berupa angka.',
            'stock.required' => 'Stok buku wajib diisi.',
            'stock.min' => 'Stok tidak boleh negatif.',
        ]);
    }
}
