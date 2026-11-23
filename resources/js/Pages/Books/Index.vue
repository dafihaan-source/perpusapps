<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link, router } from '@inertiajs/vue3';
import { ref } from 'vue';

const props = defineProps({
    books: Object,
    search: String,
});

const keyword = ref(props.search || '');

function cari() {
    router.get('/books', { search: keyword.value });
}

function hapus(id) {
    if (confirm('Yakin ingin menghapus buku ini?')) {
        router.delete(route('books.destroy', id));
    }
}
</script>

<template>
    <Head title="Data Buku" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Data Buku</h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-4">
                
                <!-- Alert Flash Message -->
                <div 
                    v-if="$page.props.flash?.success" 
                    class="p-4 bg-green-100 border border-green-400 text-green-700 rounded-lg shadow-sm"
                >
                    {{ $page.props.flash.success }}
                </div>

                <!-- Card Container Utama -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                    
                    <!-- Header Atas: Tombol Tambah & Input Pencarian -->
                    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 mb-6">
                        <Link 
                            href="/books/create" 
                            class="inline-flex items-center justify-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 focus:bg-indigo-700 active:bg-indigo-900 transition duration-150 ease-in-out w-fit"
                        >
                            + Tambah Buku
                        </Link>

                        <div class="w-full md:w-72">
                            <input 
                                v-model="keyword" 
                                @keyup.enter="cari" 
                                type="text" 
                                placeholder="Cari judul atau pengarang..."
                                class="w-full px-4 py-2 text-sm border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500"
                            />
                        </div>
                    </div>

                    <!-- Tabel Data Buku -->
                    <div class="overflow-x-auto border border-gray-200 rounded-lg">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Kode</th>
                                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Judul</th>
                                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Pengarang</th>
                                    <th class="px-6 py-3 text-center text-xs font-semibold text-gray-500 uppercase tracking-wider">Stok</th>
                                    <th class="px-6 py-3 text-center text-xs font-semibold text-gray-500 uppercase tracking-wider">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                <tr v-for="book in books.data" :key="book.id" class="hover:bg-gray-50 transition">
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ book.kode_buku }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">{{ book.judul }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">{{ book.pengarang }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-center text-gray-700 font-semibold">{{ book.stok }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-center space-x-2">
                                        <!-- Tombol Edit -->
                                        <Link 
                                            :href="`/books/${book.id}/edit`" 
                                            class="inline-flex items-center px-3 py-1 bg-amber-500 border border-transparent rounded text-xs font-medium text-white hover:bg-amber-600 transition"
                                        >
                                            Edit
                                        </Link>

                                        <!-- Tombol Hapus -->
                                        <button 
                                            @click="hapus(book.id)" 
                                            class="inline-flex items-center px-3 py-1 bg-red-600 border border-transparent rounded text-xs font-medium text-white hover:bg-red-700 transition"
                                        >
                                            Hapus
                                        </button>
                                    </td>
                                </tr>
                                <tr v-if="!books.data || books.data.length === 0">
                                    <td colspan="5" class="px-6 py-4 text-center text-sm text-gray-500">
                                        Data buku tidak ditemukan.
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>