<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Book;

class BookController extends Controller
{
    /**
     * Menampilkan daftar buku (Langkah 4).
     */
    public function index()
    {
        $books = Book::latest()->paginate(10);
        
        return inertia('Books/Index', [
            'books' => $books,
        ]);
    }

    /**
     * Menampilkan form tambah buku (Langkah 5).
     */
    public function create()
    {
        return inertia('Books/Create');
    }

    /**
     * Menyimpan data buku baru ke database (Langkah 5).
     */
    public function store(Request $request)
    {
        // 1. Validasi data yang dikirim dari form
        $validated = $request->validate([
            'kode_buku' => 'required|unique:books',
            'judul'     => 'required|max:255',
            'pengarang' => 'required|max:255',
            'penerbit'  => 'nullable|max:255',
            'tahun'     => 'nullable|digits:4',
            'stok'      => 'required|integer|min:0',
        ]);

        // 2. Simpan data ke tabel books jika valid
        Book::create($validated);

        // 3. Redirect kembali ke halaman daftar buku dengan pesan sukses
        return redirect()->route('books.index')->with('success', 'Buku berhasil ditambahkan');
    }
}