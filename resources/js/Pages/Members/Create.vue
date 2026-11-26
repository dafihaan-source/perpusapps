<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { useForm } from '@inertiajs/vue3';
import { ref, watch, onBeforeUnmount } from 'vue';
import axios from 'axios';

const form = useForm({
    nama: '',
    nis_nim: '',
    no_hp: '',
    foto: null, // berisi berkas (File) yang dipilih, bukan teks
});

// Alamat sementara gambar di browser, hanya untuk pratinjau sebelum disimpan
const pratinjau = ref(null);

// Dipanggil saat pengguna memilih berkas pada <input type="file">
function pilihFoto(event) {
    const berkas = event.target.files[0];
    form.foto = berkas ?? null;
    pratinjau.value = berkas ? URL.createObjectURL(berkas) : null;
}

// Validasi realtime NIS/NIM (baru pada Pertemuan 10)
// statusNis berisi salah satu dari: '' (belum diperiksa), 'memeriksa', 'tersedia', 'terpakai', 'gagal'
const statusNis = ref('');

// timerNis = hitungan mundur debounce; permintaanNis = pembatal untuk permintaan Axios yang sedang berjalan
let timerNis = null;
let permintaanNis = null;

// Bertanya ke server: apakah NIS/NIM yang sedang diketik sudah dipakai anggota lain?
async function periksaNis() {
    // Batalkan permintaan sebelumnya yang belum selesai
    if (permintaanNis) {
        permintaanNis.abort();
    }

    permintaanNis = new AbortController();
    statusNis.value = 'memeriksa';

    try {
        const respons = await axios.get(route('members.cek-nis'), {
            params: {
                nis_nim: form.nis_nim.trim(),
            },
            signal: permintaanNis.signal,
        });

        // Server membalas {"tersedia": true} atau {"tersedia": false}
        statusNis.value = respons.data.tersedia ? 'tersedia' : 'terpakai';
    } catch (error) {
        // Permintaan yang sengaja dibatalkan bukan kesalahan
        if (axios.isCancel(error)) {
            return;
        }

        statusNis.value = 'gagal';
    }
}

// Dijalankan setiap kali isi kolom NIS/NIM berubah
watch(() => form.nis_nim, (nilai) => {
    clearTimeout(timerNis);

    // Kolom dikosongkan: batalkan permintaan dan hapus pesan
    if (!nilai || nilai.trim() === '') {
        if (permintaanNis) {
            permintaanNis.abort();
        }
        statusNis.value = '';
        return;
    }

    // Tunggu 500 milidetik setelah ketikan terakhir, baru bertanya ke server
    timerNis = setTimeout(periksaNis, 500);
});

// Saat halaman ditinggalkan, hentikan timer dan permintaan yang masih berjalan
onBeforeUnmount(() => {
    clearTimeout(timerNis);
    if (permintaanNis) {
        permintaanNis.abort();
    }
});

function submit() {
    // Karena form.foto berisi berkas, Inertia otomatis mengirim sebagai multipart/form-data
    form.post(route('members.store'));
}
</script>

<template>
    <AuthenticatedLayout>
        <div class="p-4">
            <h1 class="text-xl font-bold">Tambah Anggota</h1>

            <form @submit.prevent="submit" class="mt-3 space-y-3">
                <div>
                    <label class="block">Nama</label>
                    <input v-model="form.nama" type="text" />
                    <div v-if="form.errors.nama" class="text-red-600">{{ form.errors.nama }}</div>
                </div>

                <div>
                    <label class="block">NIS/NIM</label>
                    <input v-model="form.nis_nim" type="text" />

                    <!-- Pesan hasil pemeriksaan realtime; hanya satu yang tampil sesuai statusNis -->
                    <div v-if="statusNis === 'memeriksa'" class="text-gray-500">
                        Memeriksa NIS/NIM...
                    </div>
                    <div v-else-if="statusNis === 'tersedia'" class="text-green-700">
                        NIS/NIM ini tersedia.
                    </div>
                    <div v-else-if="statusNis === 'terpakai'" class="text-red-600">
                        NIS/NIM ini sudah terdaftar. Gunakan nomor lain.
                    </div>
                    <div v-else-if="statusNis === 'gagal'" class="text-yellow-700">
                        Pemeriksaan otomatis gagal. Anda tetap dapat menyimpan; server akan memeriksa ulang.
                    </div>

                    <div v-if="form.errors.nis_nim" class="text-red-600">{{ form.errors.nis_nim }}</div>
                </div>

                <div>
                    <label class="block">No. HP</label>
                    <input v-model="form.no_hp" type="text" />
                    <div v-if="form.errors.no_hp" class="text-red-600">{{ form.errors.no_hp }}</div>
                </div>

                <div>
                    <label class="block">Foto (JPG/PNG, maksimal 2 MB)</label>
                    <!-- Input berkas tidak bisa memakai v-model, sehingga memakai @change -->
                    <input type="file" accept="image/png, image/jpeg" @change="pilihFoto" />
                    <div v-if="form.errors.foto" class="text-red-600">{{ form.errors.foto }}</div>
                    <img
                        v-if="pratinjau"
                        :src="pratinjau"
                        alt="Pratinjau foto"
                        class="mt-2 h-32 w-32 object-cover"
                    />
                </div>

                <button type="submit" :disabled="form.processing || statusNis === 'terpakai'">
                    Simpan
                </button>
            </form>
        </div>
    </AuthenticatedLayout>
</template>