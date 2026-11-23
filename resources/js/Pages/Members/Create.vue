<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, useForm } from '@inertiajs/vue3';
import { ref } from 'vue';

const form = useForm({
    nama: '',
    nis_nim: '',
    no_hp: '',
    foto: null,
});

const pratinjau = ref(null);

function pilihFoto(event) {
    const berkas = event.target.files[0];
    form.foto = berkas ?? null;
    pratinjau.value = berkas ? URL.createObjectURL(berkas) : null;
}

function submit() {
    form.post(route('members.store'));
}
</script>

<template>
    <Head title="Tambah Anggota" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Tambah Anggota</h2>
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
                            <div v-if="form.errors.nama" class="mt-1 text-sm text-red-600">{{ form.errors.nama }}</div>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700">NIS/NIM</label>
                            <input
                                v-model="form.nis_nim"
                                type="text"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                            />
                            <div v-if="form.errors.nis_nim" class="mt-1 text-sm text-red-600">{{ form.errors.nis_nim }}</div>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700">No. HP</label>
                            <input
                                v-model="form.no_hp"
                                type="text"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                            />
                            <div v-if="form.errors.no_hp" class="mt-1 text-sm text-red-600">{{ form.errors.no_hp }}</div>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700">Foto (JPG/PNG, maksimal 2 MB)</label>
                            <input
                                type="file"
                                accept="image/png, image/jpeg"
                                @change="pilihFoto"
                                class="mt-1 block w-full text-sm text-gray-700"
                            />
                            <div v-if="form.errors.foto" class="mt-1 text-sm text-red-600">{{ form.errors.foto }}</div>

                            <img
                                v-if="pratinjau"
                                :src="pratinjau"
                                alt="Pratinjau foto"
                                class="mt-3 h-32 w-32 object-cover rounded border"
                            />
                        </div>

                        <div class="pt-2">
                            <button
                                type="submit"
                                :disabled="form.processing"
                                class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 disabled:opacity-50"
                            >
                                Simpan
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>