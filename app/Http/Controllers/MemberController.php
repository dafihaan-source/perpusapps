<?php

namespace App\Http\Controllers;

use App\Models\Member;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class MemberController extends Controller
{
    /**
     * Menampilkan daftar anggota (dengan fitur pencarian).
     */
    public function index(Request $request)
    {
        $members = Member::when($request->search, function ($query, $search) {
            $query->where('nama', 'like', "%{$search}%")
                  ->orWhere('nis_nim', 'like', "%{$search}%");
        })->latest()->paginate(10)->withQueryString();

        return inertia('Members/Index', [
            'members' => $members,
            'search' => $request->search,
        ]);
    }

    /**
     * Endpoint JSON untuk live search anggota (dipanggil oleh Axios).
     */
    public function search(Request $request)
    {
        $katakunci = $request->query('q', '');

        $members = Member::where(function ($query) use ($katakunci) {
            $query->where('nama', 'like', "%{$katakunci}%")
                  ->orWhere('nis_nim', 'like', "%{$katakunci}%");
        })
            ->latest()
            ->limit(10)
            ->get(['id', 'nama', 'nis_nim', 'no_hp', 'foto']);

        return response()->json($members);
    }

    /**
     * Endpoint JSON untuk validasi realtime: apakah NIS/NIM masih tersedia?
     */
    public function cekNis(Request $request)
    {
        // NIS/NIM yang sedang diketik, misalnya /members/cek-nis?nis_nim=2023001
        $nisNim = $request->query('nis_nim', '');

        // abaikan: id anggota yang sedang diedit (kosong pada form tambah)
        $abaikanId = $request->query('abaikan');

        // Apakah ada anggota lain yang sudah memakai NIS/NIM ini?
        $sudahDipakai = Member::where('nis_nim', $nisNim)
            ->when($abaikanId, function ($query, $id) {
                // Jika ada id yang diabaikan, jangan hitung anggota itu sendiri
                $query->where('id', '!=', $id);
            })
            ->exists();

        // Dibalas sebagai JSON, misalnya {"tersedia":true}
        return response()->json([
            'tersedia' => ! $sudahDipakai,
        ]);
    }

    /**
     * Menampilkan form untuk menambah anggota baru.
     */
    public function create()
    {
        return inertia('Members/Create');
    }

    /**
     * Menyimpan data anggota baru ke database.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama'    => 'required|max:255',
            'nis_nim' => 'required|unique:members,nis_nim',
            'no_hp'   => 'nullable|max:30',
            'foto'    => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
        ]);

        if ($request->hasFile('foto')) {
            $validated['foto'] = $request->file('foto')->store('members', 'public');
        }

        Member::create($validated);

        return redirect()->route('members.index')->with('success', 'Anggota berhasil ditambahkan');
    }

    /**
     * Menampilkan form untuk mengedit data anggota.
     */
    public function edit(Member $member)
    {
        return inertia('Members/Edit', [
            'member' => $member,
        ]);
    }

    /**
     * Memperbarui data anggota di database.
     */
    public function update(Request $request, Member $member)
    {
        $validated = $request->validate([
            'nama'    => 'required|max:255',
            'nis_nim' => 'required|unique:members,nis_nim,' . $member->id,
            'no_hp'   => 'nullable|max:30',
            'foto'    => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
        ]);

        if ($request->hasFile('foto')) {
            if ($member->foto && Storage::disk('public')->exists($member->foto)) {
                Storage::disk('public')->delete($member->foto);
            }
            $validated['foto'] = $request->file('foto')->store('members', 'public');
        }

        $member->update($validated);

        return redirect()->route('members.index')->with('success', 'Anggota berhasil diperbarui');
    }

    /**
     * Menghapus data anggota dari database.
     */
    public function destroy(Member $member)
    {
        if ($member->foto && Storage::disk('public')->exists($member->foto)) {
            Storage::disk('public')->delete($member->foto);
        }

        $member->delete();

        return redirect()->route('members.index')->with('success', 'Anggota berhasil dihapus');
    }
}