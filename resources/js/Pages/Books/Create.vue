<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';

const form = useForm({
    kode_buku: '',
    judul: '',
    pengarang: '',
    penerbit: '',
    tahun: '',
    stok: 0,
});

function submit() {
    form.post(route('books.store'));
}
</script>

<template>
    <Head title="Tambah Buku" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex justify-between items-center">
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">Tambah Buku Baru</h2>
                <Link
                    href="/books"
                    class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-md text-sm font-medium transition"
                >
                    Kembali
                </Link>
            </div>
        </template>

        <div class="py-12">
            <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                    
                    <form @submit.prevent="submit" class="space-y-6">
                        <!-- Kode Buku -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Kode Buku</label>
                            <input
                                v-model="form.kode_buku"
                                type="text"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm border p-2"
                                placeholder="Contoh: BKO01"
                            />
                            <div v-if="form.errors.kode_buku" class="text-sm text-red-600 mt-1">{{ form.errors.kode_buku }}</div>
                        </div>

                        <!-- Judul Buku -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Judul Buku</label>
                            <input
                                v-model="form.judul"
                                type="text"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm border p-2"
                                placeholder="Contoh: Belajar Laravel"
                            />
                            <div v-if="form.errors.judul" class="text-sm text-red-600 mt-1">{{ form.errors.judul }}</div>
                        </div>

                        <!-- Pengarang -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Pengarang</label>
                            <input
                                v-model="form.pengarang"
                                type="text"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm border p-2"
                                placeholder="Contoh: Budi"
                            />
                            <div v-if="form.errors.pengarang" class="text-sm text-red-600 mt-1">{{ form.errors.pengarang }}</div>
                        </div>

                        <!-- Penerbit -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Penerbit</label>
                            <input
                                v-model="form.penerbit"
                                type="text"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm border p-2"
                                placeholder="Contoh: Gramedia"
                            />
                        </div>

                        <!-- Tahun -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Tahun Terbit</label>
                            <input
                                v-model="form.tahun"
                                type="text"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm border p-2"
                                placeholder="Contoh: 2026"
                            />
                        </div>

                        <!-- Stok -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Stok</label>
                            <input
                                v-model="form.stok"
                                type="number"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm border p-2"
                            />
                        </div>

                        <!-- Tombol Aksi -->
                        <div class="flex justify-end gap-3">
                            <Link
                                href="/books"
                                class="bg-gray-200 hover:bg-gray-300 text-gray-700 px-4 py-2 rounded-md text-sm font-medium transition"
                            >
                                Batal
                            </Link>
                            <button
                                type="submit"
                                :disabled="form.processing"
                                class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-md text-sm font-medium transition"
                            >
                                Simpan Buku
                            </button>
                        </div>
                    </form>

                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>