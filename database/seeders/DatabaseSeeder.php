<?php

namespace Database\Seeders;

use App\Models\Book;
use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        User::firstOrCreate(
            ['email' => 'admin@perpus.test'],
            ['name' => 'Admin', 'password' => 'password', 'role' => 'admin']
        );

        User::firstOrCreate(
            ['email' => 'siswa@perpus.test'],
            ['name' => 'Dimas', 'password' => 'password', 'role' => 'siswa', 'kelas' => 'XII RPL 2']
        );

        $books = [
            ['title' => 'Laskar Pelangi', 'author' => 'Andrea Hirata', 'publisher' => 'Bentang', 'year' => 2005, 'stock' => 4],
            ['title' => 'Bumi', 'author' => 'Tere Liye', 'publisher' => 'Gramedia', 'year' => 2014, 'stock' => 3],
            ['title' => 'Belajar Laravel', 'author' => 'Budi Raharjo', 'publisher' => 'Informatika', 'year' => 2023, 'stock' => 5],
            ['title' => 'Negeri 5 Menara', 'author' => 'Ahmad Fuadi', 'publisher' => 'Gramedia', 'year' => 2009, 'stock' => 4],
            ['title' => 'Filosofi Teras', 'author' => 'Henry Manampiring', 'publisher' => 'Kompas', 'year' => 2018, 'stock' => 2],
            ['title' => 'Clean Code', 'author' => 'Robert C. Martin', 'publisher' => 'Prentice Hall', 'year' => 2008, 'stock' => 3],
            ['title' => 'Perahu Kertas', 'author' => 'Dee Lestari', 'publisher' => 'Bentang', 'year' => 2009, 'stock' => 2],
            ['title' => 'Algoritma dan Pemrograman', 'author' => 'Rinaldi Munir', 'publisher' => 'Informatika', 'year' => 2021, 'stock' => 6],
        ];

        foreach ($books as $book) {
            Book::firstOrCreate(
                ['title' => $book['title'], 'author' => $book['author']],
                $book
            );
        }
    }
}
