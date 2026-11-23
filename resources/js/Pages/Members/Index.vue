<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link, router } from '@inertiajs/vue3';
import { ref } from 'vue';

const props = defineProps({
    members: Object,
    search: String,
});

// Nilai awal kotak pencarian diisi dari props.search agar kata kunci tidak hilang saat halaman dimuat ulang
const keyword = ref(props.search || '');

function cari() {
    router.get('/members', { search: keyword.value });
}

function hapus(id) {
    if (confirm('Yakin ingin menghapus anggota ini?')) {
        router.delete(route('members.destroy', id));
    }
}
</script>

<template>
    <Head title="Data Anggota" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Data Anggota</h2>
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
                            href="/members/create"
                            class="inline-flex items-center justify-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 focus:bg-indigo-700 active:bg-indigo-900 transition duration-150 ease-in-out w-fit"
                        >
                            + Tambah Anggota
                        </Link>

                        <div class="w-full md:w-72">
                            <input
                                v-model="keyword"
                                @keyup.enter="cari"
                                type="text"
                                placeholder="Cari nama atau NIS/NIM..."
                                class="w-full px-4 py-2 text-sm border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500"
                            />
                        </div>
                    </div>

                    <!-- Tabel Data Anggota -->
                    <div class="overflow-x-auto border border-gray-200 rounded-lg">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Foto</th>
                                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">NIS/NIM</th>
                                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Nama</th>
                                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">No. HP</th>
                                    <th class="px-6 py-3 text-center text-xs font-semibold text-gray-500 uppercase tracking-wider">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                <tr v-for="member in members.data" :key="member.id" class="hover:bg-gray-50 transition">
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">
                                        <img
                                            v-if="member.foto"
                                            :src="`/storage/${member.foto}`"
                                            alt="Foto anggota"
                                            class="h-16 w-16 object-cover rounded"
                                        />
                                        <span v-else>-</span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ member.nis_nim }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">{{ member.nama }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">{{ member.no_hp }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-center space-x-2">
                                        <Link
                                            :href="`/members/${member.id}/edit`"
                                            class="inline-flex items-center px-3 py-1 bg-amber-500 border border-transparent rounded text-xs font-medium text-white hover:bg-amber-600 transition"
                                        >
                                            Edit
                                        </Link>

                                        <button
                                            @click="hapus(member.id)"
                                            class="inline-flex items-center px-3 py-1 bg-red-600 border border-transparent rounded text-xs font-medium text-white hover:bg-red-700 transition"
                                        >
                                            Hapus
                                        </button>
                                    </td>
                                </tr>
                                <tr v-if="!members.data || members.data.length === 0">
                                    <td colspan="5" class="px-6 py-4 text-center text-sm text-gray-500">
                                        Belum ada data anggota.
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <!-- Tautan Halaman (Paginasi) -->
                    <div v-if="members.links" class="mt-4 flex flex-wrap gap-3 text-sm">
                        <template v-for="link in members.links" :key="link.label">
                            <Link
                                v-if="link.url"
                                :href="link.url"
                                v-html="link.label"
                                class="px-2 py-1 rounded hover:bg-gray-100"
                            />
                            <span v-else v-html="link.label" class="px-2 py-1 text-gray-400" />
                        </template>
                    </div>

                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>