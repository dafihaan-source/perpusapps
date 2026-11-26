<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Link, router } from '@inertiajs/vue3';
import { ref, computed, watch, onBeforeUnmount } from 'vue';
import axios from 'axios';

const props = defineProps({
    books: Object,
    search: String,
});

// Isi kotak pencarian; ?? berarti "jika kosong, pakai teks kosong" agar tidak pernah bernilai null
const keyword = ref(props.search ?? '');

// Hasil pencarian dari server. Nilai null artinya "belum ada pencarian",
// sehingga tabel menampilkan daftar bawaan
const hasilCari = ref(null);
const sedangMencari = ref(false);
const pesanError = ref('');

// timer menyimpan hitungan mundur debounce; permintaan menyimpan pembatal untuk
// permintaan Axios yang sedang berjalan
let timer = null;
let permintaan = null;

// Data yang ditampilkan di tabel: hasil pencarian jika ada, jika tidak daftar bawaan dari BookController@index
const daftarBuku = computed(() => hasilCari.value ?? props.books.data);

// Mengirim permintaan AJAX ke /books/search lalu menyimpan hasilnya
async function ambilHasil() {
    // Batalkan permintaan sebelumnya yang belum selesai agar jawaban lama tidak menimpa jawaban baru
    if (permintaan) {
        permintaan.abort();
    }

    permintaan = new AbortController();
    sedangMencari.value = true;
    pesanError.value = '';

    try {
        const respons = await axios.get(route('books.search'), {
            params: { q: keyword.value.trim() },
            signal: permintaan.signal,
        });

        hasilCari.value = respons.data;
        sedangMencari.value = false;
    } catch (error) {
        // Permintaan yang sengaja dibatalkan bukan kesalahan; permintaan penggantinya sedang berjalan
        if (axios.isCancel(error)) {
            return;
        }

        sedangMencari.value = false;
        pesanError.value = 'Pencarian gagal. Muat ulang halaman, lalu coba lagi.';
    }
}

// watch dijalankan setiap kali isi kotak pencarian berubah (setiap ketikan)
watch(keyword, (nilai) => {
    // Hentikan hitungan mundur lama (debounce)
    clearTimeout(timer);

    // Kotak dikosongkan: batalkan permintaan, kembali ke daftar bawaan
    if (nilai.trim() === '') {
        if (permintaan) {
            permintaan.abort();
        }
        hasilCari.value = null;
        sedangMencari.value = false;
        pesanError.value = '';
        return;
    }

    // Mulai hitungan mundur 300 milidetik; permintaan baru dikirim hanya jika pengguna berhenti mengetik
    timer = setTimeout(ambilHasil, 300);
});

// Function hapus ditambah onSuccess agar hasil pencarian ikut diperbarui setelah menghapus
function hapus(id) {
    if (confirm('Yakin ingin menghapus buku ini?')) {
        router.delete(route('books.destroy', id), {
            onSuccess: () => {
                if (keyword.value.trim() !== '') {
                    ambilHasil();
                }
            },
        });
    }
}

// Saat halaman ditinggalkan, hentikan timer dan permintaan yang masih berjalan
onBeforeUnmount(() => {
    clearTimeout(timer);
    if (permintaan) {
        permintaan.abort();
    }
});
</script>

<template>
    <AuthenticatedLayout>
        <h1>Data Buku</h1>

        <!-- Flash message: Menampilkan pesan sukses dari session jika ada -->
        <div
            v-if="$page.props.flash.success"
            class="alert alert-success"
            style="color: green; margin-bottom: 10px;"
        >
            {{ $page.props.flash.success }}
        </div>

        <!-- Link menuju form tambah buku, akan diarahkan ke Books/Create.vue -->
        <Link href="/books/create">+ Tambah Buku</Link>

        <!-- Tidak ada lagi @keyup.enter: pencarian berjalan otomatis lewat watch(keyword) -->
        <input
            v-model="keyword"
            type="text"
            placeholder="Ketik judul atau pengarang..."
        />

        <!-- Penanda bahwa permintaan AJAX sedang berjalan -->
        <span v-if="sedangMencari">Mencari...</span>

        <!-- Pesan jika permintaan AJAX gagal -->
        <p v-if="pesanError">{{ pesanError }}</p>

        <table>
            <thead>
                <tr>
                    <th>Kode</th>
                    <th>Judul</th>
                    <th>Pengarang</th>
                    <th>Stok</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                <!-- v-for kini membaca daftarBuku (hasil pencarian atau daftar bawaan), bukan books.data -->
                <tr v-for="book in daftarBuku" :key="book.id">
                    <td>{{ book.kode_buku }}</td>
                    <td>{{ book.judul }}</td>
                    <td>{{ book.pengarang }}</td>
                    <td>{{ book.stok }}</td>
                    <td>
                        <!-- Link menuju form edit buku, akan diarahkan ke Books/Edit.vue -->
                        <Link :href="`/books/${book.id}/edit`">Edit</Link>

                        <!-- Tombol hapus buku, memanggil function hapus() di atas -->
                        <button type="button" @click="hapus(book.id)">Hapus</button>
                    </td>
                </tr>

                <!-- Pesan jika tidak ada data yang cocok -->
                <tr v-if="daftarBuku.length === 0">
                    <td colspan="5">Tidak ada buku yang ditemukan.</td>
                </tr>
            </tbody>
        </table>
    </AuthenticatedLayout>
</template>