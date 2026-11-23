<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, useForm } from '@inertiajs/vue3';
import { ref } from 'vue';

// 'member' diterima dari MemberController@edit, berisi data lama anggota ini
const props = defineProps({
    member: Object,
});

const form = useForm({
    _method: 'put', // "menyamar" sebagai PUT agar unggahan berkas terbaca PHP
    nama: props.member.nama,
    nis_nim: props.member.nis_nim,
    no_hp: props.member.no_hp,
    foto: null, // tetap null apabila pengguna tidak memilih foto baru
});

const pratinjau = ref(null);

function pilihFoto(event) {
    const berkas = event.target.files[0];
    form.foto = berkas ?? null;
    pratinjau.value = berkas ? URL.createObjectURL(berkas) : null;
}

function submit() {
    // Dikirim dengan POST (bukan PUT) karena PHP tidak bisa membaca berkas dari permintaan PUT;
    // Laravel membaca _method: 'put' lalu meneruskannya ke method update()
    form.post(route('members.update', props.member.id));
}
</script>

<template>
    <Head title="Edit Anggota" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Edit Anggota</h2>
        </template>

        <div class="py-12">
            <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                    <form @submit.prevent="submit" class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Nama</label>
                            <input
                                v-model="form.nama"
                                type="text"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                            />
                            <div v-if="form.errors.nama" class="mt-1 text-sm text-red-600">
                                {{ form.errors.nama }}
                            </div>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700">NIS/NIM</label>
                            <input
                                v-model="form.nis_nim"
                                type="text"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                            />
                            <div v-if="form.errors.nis_nim" class="mt-1 text-sm text-red-600">
                                {{ form.errors.nis_nim }}
                            </div>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700">No. HP</label>
                            <input
                                v-model="form.no_hp"
                                type="text"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                            />
                            <div v-if="form.errors.no_hp" class="mt-1 text-sm text-red-600">
                                {{ form.errors.no_hp }}
                            </div>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700">Foto Saat Ini</label>
                            <img
                                v-if="member.foto"
                                :src="`/storage/${member.foto}`"
                                alt="Foto saat ini"
                                class="mt-2 h-32 w-32 object-cover rounded border"
                            />
                            <span v-else class="mt-1 block text-sm text-gray-500">Belum ada foto</span>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700">
                                Ganti Foto (kosongkan jika tidak ingin mengganti)
                            </label>
                            <input
                                type="file"
                                accept="image/png, image/jpeg"
                                @change="pilihFoto"
                                class="mt-1 block w-full text-sm text-gray-700"
                            />
                            <div v-if="form.errors.foto" class="mt-1 text-sm text-red-600">
                                {{ form.errors.foto }}
                            </div>
                            <img
                                v-if="pratinjau"
                                :src="pratinjau"
                                alt="Pratinjau foto baru"
                                class="mt-2 h-32 w-32 object-cover rounded border"
                            />
                        </div>

                        <div class="pt-2">
                            <button
                                type="submit"
                                :disabled="form.processing"
                                class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 disabled:opacity-50"
                            >
                                Simpan Perubahan
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>