<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Link, router } from '@inertiajs/vue3';
import { ref, computed, watch, onBeforeUnmount } from 'vue';
import axios from 'axios';

const props = defineProps({
    members: Object,
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

// Data yang ditampilkan di tabel: hasil pencarian jika ada, jika tidak daftar bawaan dari MemberController@index
const daftarAnggota = computed(() => hasilCari.value ?? props.members.data);

// Mengirim permintaan AJAX ke /members/search lalu menyimpan hasilnya
async function ambilHasil() {
    // Batalkan permintaan sebelumnya yang belum selesai agar jawaban lama tidak menimpa jawaban baru
    if (permintaan) {
        permintaan.abort();
    }

    permintaan = new AbortController();
    sedangMencari.value = true;
    pesanError.value = '';

    try {
        const respons = await axios.get(route('members.search'), {
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
    if (confirm('Yakin ingin menghapus anggota ini?')) {
        router.delete(route('members.destroy', id), {
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
        <div class="p-4">
            <h1 class="text-xl font-bold">Data Anggota</h1>

            <div v-if="$page.props.flash.success" class="my-2 text-green-700">
                {{ $page.props.flash.success }}
            </div>

            <div class="my-3 flex items-center gap-3">
                <Link href="/members/create">+ Tambah Anggota</Link>

                <!-- Tidak ada lagi @keyup.enter: pencarian berjalan otomatis lewat watch(keyword) -->
                <input
                    v-model="keyword"
                    type="text"
                    placeholder="Ketik nama atau NIS/NIM..."
                />

                <!-- Penanda bahwa permintaan AJAX sedang berjalan -->
                <span v-if="sedangMencari" class="text-gray-500">Mencari...</span>
            </div>

            <!-- Pesan jika permintaan AJAX gagal -->
            <div v-if="pesanError" class="my-2 text-red-600">{{ pesanError }}</div>

            <table class="border-collapse">
                <thead>
                    <tr>
                        <th class="border px-3 py-2">Foto</th>
                        <th class="border px-3 py-2">NIS/NIM</th>
                        <th class="border px-3 py-2">Nama</th>
                        <th class="border px-3 py-2">No. HP</th>
                        <th class="border px-3 py-2">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <!-- v-for kini membaca daftarAnggota (hasil pencarian atau daftar bawaan), bukan members.data -->
                    <tr v-for="member in daftarAnggota" :key="member.id">
                        <td class="border px-3 py-2">
                            <!-- Foto hanya ditampilkan jika kolom foto terisi; alamatnya diawali /storage/ -->
                            <img
                                v-if="member.foto"
                                :src="`/storage/${member.foto}`"
                                alt="Foto anggota"
                                class="h-16 w-16 object-cover"
                            />
                            <span v-else>-</span>
                        </td>
                        <td class="border px-3 py-2">{{ member.nis_nim }}</td>
                        <td class="border px-3 py-2">{{ member.nama }}</td>
                        <td class="border px-3 py-2">{{ member.no_hp }}</td>
                        <td class="border px-3 py-2">
                            <Link :href="`/members/${member.id}/edit`" class="mr-3">Edit</Link>
                            <button type="button" @click="hapus(member.id)">Hapus</button>
                        </td>
                    </tr>

                    <tr v-if="daftarAnggota.length === 0">
                        <!-- Teks berbeda: data memang kosong, atau pencarian tidak menemukan apa pun -->
                        <td colspan="5" class="border px-3 py-2">
                            {{
                                keyword.trim() !== ''
                                    ? 'Tidak ada anggota yang ditemukan.'
                                    : 'Belum ada data anggota.'
                            }}
                        </td>
                    </tr>
                </tbody>
            </table>

            <!-- Paginasi hanya relevan untuk daftar bawaan; disembunyikan saat menampilkan hasil pencarian -->
            <div v-if="hasilCari === null" class="mt-3 flex gap-3">
                <template v-for="link in members.links" :key="link.label">
                    <Link v-if="link.url" :href="link.url" v-html="link.label" />
                    <span v-else v-html="link.label" class="text-gray-400" />
                </template>
            </div>
        </div>
    </AuthenticatedLayout>
</template>