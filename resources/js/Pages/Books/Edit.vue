<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, useForm } from '@inertiajs/vue3';

// Menerima props 'book' dari BookController method edit
const props = defineProps({
    book: Object,
});

// Menginisialisasi form dengan data buku yang sudah ada
const form = useForm({
    kode_buku: props.book.kode_buku || '',
    judul: props.book.judul || '',
    pengarang: props.book.pengarang || '',
    penerbit: props.book.penerbit || '',
    tahun: props.book.tahun || '',
    stok: props.book.stok || '',
});

// Fungsi untuk mengirim data update ke server
function submit() {
    form.put(route('books.update', props.book.id));
}
</script>

<template>
    <Head title="Edit Buku" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Edit Data Buku</h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6 max-w-2xl mx-auto">
                    
                    <form @submit.prevent="submit" class="space-y-4">
                        <!-- Kode Buku -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Kode Buku</label>
                            <input 
                                v-model="form.kode_buku" 
                                type="text" 
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm"
                            />
                            <div v-if="form.errors.kode_buku" class="text-red-600 text-xs mt-1">{{ form.errors.kode_buku }}</div>
                        </div>

                        <!-- Judul -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Judul Buku</label>
                            <input 
                                v-model="form.judul" 
                                type="text" 
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm"
                            />
                            <div v-if="form.errors.judul" class="text-red-600 text-xs mt-1">{{ form.errors.judul }}</div>
                        </div>

                        <!-- Pengarang -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Pengarang</label>
                            <input 
                                v-model="form.pengarang" 
                                type="text" 
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm"
                            />
                            <div v-if="form.errors.pengarang" class="text-red-600 text-xs mt-1">{{ form.errors.pengarang }}</div>
                        </div>

                        <!-- Penerbit -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Penerbit</label>
                            <input 
                                v-model="form.penerbit" 
                                type="text" 
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm"
                            />
                            <div v-if="form.errors.penerbit" class="text-red-600 text-xs mt-1">{{ form.errors.penerbit }}</div>
                        </div>

                        <!-- Tahun -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Tahun Terbit</label>
                            <input 
                                v-model="form.tahun" 
                                type="number" 
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm"
                            />
                            <div v-if="form.errors.tahun" class="text-red-600 text-xs mt-1">{{ form.errors.tahun }}</div>
                        </div>

                        <!-- Stok -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Stok</label>
                            <input 
                                v-model="form.stok" 
                                type="number" 
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm"
                            />
                            <div v-if="form.errors.stok" class="text-red-600 text-xs mt-1">{{ form.errors.stok }}</div>
                        </div>

                        <!-- Tombol Aksi -->
                        <div class="flex items-center justify-end space-x-3 pt-4">
                            <a 
                                href="/books" 
                                class="px-4 py-2 bg-gray-300 text-gray-700 rounded-md text-xs font-semibold uppercase tracking-widest hover:bg-gray-400 transition"
                            >
                                Batal
                            </a>
                            <button 
                                type="submit" 
                                :disabled="form.processing"
                                class="px-4 py-2 bg-indigo-600 text-white rounded-md text-xs font-semibold uppercase tracking-widest hover:bg-indigo-700 transition"
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