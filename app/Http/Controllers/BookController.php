<?php

namespace App\Http\Controllers;

use App\Models\Book;
use Illuminate\Http\Request;

class BookController extends Controller
{
    /**
     * Menampilkan daftar buku (dengan fitur pencarian).
     */
    public function index(Request $request)
    {
        $books = Book::when($request->search, function ($query, $search) {
            $query->where('judul', 'like', "%{$search}%")
                  ->orWhere('pengarang', 'like', "%{$search}%");
        })->latest()->paginate(10)->withQueryString();

        return inertia('Books/Index', [
            'books' => $books,
            'search' => $request->search,
        ]);
    }

    /**
     * Endpoint JSON untuk live search buku (dipanggil oleh Axios).
     */
    public function search(Request $request)
    {
        $katakunci = $request->query('q', '');

        $books = Book::where(function ($query) use ($katakunci) {
            $query->where('judul', 'like', "%{$katakunci}%")
                  ->orWhere('pengarang', 'like', "%{$katakunci}%");
        })
            ->latest()
            ->limit(10)
            ->get(['id', 'kode_buku', 'judul', 'pengarang', 'stok']);

        return response()->json($books);
    }

    /**
     * Menampilkan form untuk menambah buku baru.
     */
    public function create()
    {
        return inertia('Books/Create');
    }

    /**
     * Menyimpan data buku baru ke database.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'kode_buku' => 'required|unique:books,kode_buku',
            'judul'     => 'required|max:255',
            'pengarang' => 'required|max:255',
            'penerbit'  => 'nullable|max:255',
            'tahun'     => 'nullable|digits:4',
            'stok'      => 'required|integer|min:0',
        ]);

        Book::create($validated);

        return redirect()->route('books.index')->with('success', 'Buku berhasil ditambahkan');
    }

    /**
     * Menampilkan detail buku (opsional/tidak digunakan).
     */
    public function show(Book $book)
    {
        //
    }

    /**
     * Menampilkan form untuk mengedit data buku.
     */
    public function edit(Book $book)
    {
        return inertia('Books/Edit', [
            'book' => $book,
        ]);
    }

    /**
     * Memperbarui data buku di database.
     */
    public function update(Request $request, Book $book)
    {
        $validated = $request->validate([
            'kode_buku' => 'required|unique:books,kode_buku,' . $book->id,
            'judul'     => 'required|max:255',
            'pengarang' => 'required|max:255',
            'penerbit'  => 'nullable|max:255',
            'tahun'     => 'nullable|digits:4',
            'stok'      => 'required|integer|min:0',
        ]);

        $book->update($validated);

        return redirect()->route('books.index')->with('success', 'Buku berhasil diperbarui');
    }

    /**
     * Menghapus data buku dari database.
     */
    public function destroy(Book $book)
    {
        $book->delete();

        return redirect()->route('books.index')->with('success', 'Buku berhasil dihapus');
    }
}