<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { useForm } from '@inertiajs/vue3';
import { ref, watch, onBeforeUnmount } from 'vue';
import axios from 'axios';

const props = defineProps({
    member: Object,
});

const form = useForm({
    nama: props.member.nama ?? '',
    nis_nim: props.member.nis_nim ?? '',
    no_hp: props.member.no_hp ?? '',
    foto: null,
});

// Pratinjau foto baru (jika memilih file baru) atau foto lama dari storage
const pratinjau = ref(
    props.member.foto ? `/storage/${props.member.foto}` : null
);

function pilihFoto(event) {
    const berkas = event.target.files[0];
    form.foto = berkas ?? null;

    if (berkas) {
        pratinjau.value = URL.createObjectURL(berkas);
    } else {
        pratinjau.value = props.member.foto
            ? `/storage/${props.member.foto}`
            : null;
    }
}

// Validasi realtime NIS/NIM:
// '' (belum diperiksa), 'memeriksa', 'tersedia', 'terpakai', 'gagal'
const statusNis = ref('');

let timerNis = null;
let permintaanNis = null;

async function periksaNis() {
    if (permintaanNis) {
        permintaanNis.abort();
    }

    permintaanNis = new AbortController();
    statusNis.value = 'memeriksa';

    try {
        const respons = await axios.get(route('members.cek-nis'), {
            params: {
                nis_nim: form.nis_nim.trim(),
                abaikan: props.member.id,
            },
            signal: permintaanNis.signal,
        });

        statusNis.value = respons.data.tersedia ? 'tersedia' : 'terpakai';
    } catch (error) {
        if (axios.isCancel(error)) {
            return;
        }

        statusNis.value = 'gagal';
    }
}

watch(
    () => form.nis_nim,
    (nilai) => {
        clearTimeout(timerNis);

        if (!nilai || nilai.trim() === '') {
            if (permintaanNis) {
                permintaanNis.abort();
            }
            statusNis.value = '';
            return;
        }

        timerNis = setTimeout(periksaNis, 500);
    }
);

onBeforeUnmount(() => {
    clearTimeout(timerNis);
    if (permintaanNis) {
        permintaanNis.abort();
    }
});

function submit() {
    // Menggunakan POST dengan _method: 'put' agar upload file/foto pada form edit bekerja lancar di Laravel
    form.transform((data) => ({
        ...data,
        _method: 'put',
    })).post(route('members.update', props.member.id));
}
</script>

<template>
    <AuthenticatedLayout>
        <div class="p-4">
            <h1 class="text-xl font-bold">Edit Anggota</h1>

            <form @submit.prevent="submit" class="mt-3 space-y-3">
                <div>
                    <label class="block">Nama</label>
                    <input v-model="form.nama" type="text" />
                    <div v-if="form.errors.nama" class="text-red-600">
                        {{ form.errors.nama }}
                    </div>
                </div>

                <div>
                    <label class="block">NIS/NIM</label>
                    <input v-model="form.nis_nim" type="text" />

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

                    <div v-if="form.errors.nis_nim" class="text-red-600">
                        {{ form.errors.nis_nim }}
                    </div>
                </div>

                <div>
                    <label class="block">No. HP</label>
                    <input v-model="form.no_hp" type="text" />
                    <div v-if="form.errors.no_hp" class="text-red-600">
                        {{ form.errors.no_hp }}
                    </div>
                </div>

                <div>
                    <label class="block">Foto (JPG/PNG, maksimal 2 MB)</label>
                    <input type="file" accept="image/png, image/jpeg" @change="pilihFoto" />
                    <div v-if="form.errors.foto" class="text-red-600">
                        {{ form.errors.foto }}
                    </div>
                    <img
                        v-if="pratinjau"
                        :src="pratinjau"
                        alt="Pratinjau foto"
                        class="mt-2 h-32 w-32 object-cover"
                    />
                </div>

                <button
                    type="submit"
                    :disabled="form.processing || statusNis === 'terpakai'"
                >
                    Perbarui
                </button>
            </form>
        </div>
    </AuthenticatedLayout>
</template>