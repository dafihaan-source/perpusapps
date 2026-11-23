<script setup>
import { Link, usePage } from '@inertiajs/vue3';
import { computed } from 'vue';

const page = usePage();
const currentUrl = computed(() => page.url);

// Menentukan apakah menu sedang aktif berdasarkan awalan URL
function isActive(path) {
    if (path === '/' || path === '/dashboard') {
        return currentUrl.value === path;
    }
    return currentUrl.value.startsWith(path);
}
</script>

<template>
    <aside class="w-64 bg-gray-800 text-white min-h-screen flex flex-col justify-between flex-shrink-0">
        <!-- Bagian Atas: Brand & Menu Navigasi -->
        <div class="p-4">
            <div class="border-b border-gray-700 pb-4 mb-4">
                <h2 class="text-lg font-bold tracking-wide text-white">Perpustakaan Mini</h2>
                <p class="text-xs text-gray-400 mt-0.5">Sistem Peminjaman Buku</p>
            </div>

            <nav class="space-y-1.5">
                <p class="px-3 text-[11px] font-semibold uppercase tracking-wider text-gray-400 mb-1">
                    Menu Utama
                </p>

                <Link
                    href="/dashboard"
                    :class="[
                        'block px-3 py-2 rounded-md text-sm font-medium transition',
                        isActive('/dashboard')
                            ? 'bg-gray-900 text-white'
                            : 'text-gray-300 hover:bg-gray-700 hover:text-white'
                    ]"
                >
                    Dashboard
                </Link>

                <Link
                    href="/"
                    :class="[
                        'block px-3 py-2 rounded-md text-sm font-medium transition',
                        isActive('/')
                            ? 'bg-gray-900 text-white'
                            : 'text-gray-300 hover:bg-gray-700 hover:text-white'
                    ]"
                >
                    Beranda
                </Link>

                <p class="px-3 pt-4 text-[11px] font-semibold uppercase tracking-wider text-gray-400 mb-1">
                    Master Data
                </p>

                <!-- Menu menuju daftar buku (sesuai Route::resource('books', ...)) -->
                <Link
                    href="/books"
                    :class="[
                        'block px-3 py-2 rounded-md text-sm font-medium transition',
                        isActive('/books')
                            ? 'bg-gray-900 text-white'
                            : 'text-gray-300 hover:bg-gray-700 hover:text-white'
                    ]"
                >
                    Data Buku
                </Link>

                <!-- Menu menuju daftar anggota (sesuai Route::resource('members', ...)) -->
                <Link
                    href="/members"
                    :class="[
                        'block px-3 py-2 rounded-md text-sm font-medium transition',
                        isActive('/members')
                            ? 'bg-gray-900 text-white'
                            : 'text-gray-300 hover:bg-gray-700 hover:text-white'
                    ]"
                >
                    Data Anggota
                </Link>
            </nav>
        </div>

        <!-- Bagian Bawah: Footer Sidebar -->
        <div class="p-4 border-t border-gray-700 text-xs text-gray-400">
            Pemrograman Web II • Laravel + Vue
        </div>
    </aside>
</template>