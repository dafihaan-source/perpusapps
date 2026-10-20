<script setup> 
import { Link } from '@inertiajs/vue3'; 
</script> 
<template> 
<aside class="w-64 bg-gray-800 text-white min-h-screen p-4 flex-shrink-0"> 
<h2 class="text-lg font-bold mb-4">Perpustakaan Mini</h2> 
<nav class="space-y-2"> 
<Link href="/dashboard" class="block hover:text-gray-300">Dashboard</Link> 
<Link href="/" class="block hover:text-gray-300">Beranda</Link> 
<!-- Baris baru: menu menuju daftar buku, href harus sama dengan 
Route::resource pada Langkah 3 --> 
<Link href="/books" class="block hover:text-gray-300">Data Buku</Link> 
</nav> 
</aside> 
</template> 