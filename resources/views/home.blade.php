@extends('layouts.app') 
 
@section('title', $judul) 
 
@section('content') 
    <h1>Selamat datang di {{ $judul }}</h1> 
    <p>Ini adalah halaman utama aplikasi yang sedang dibangun pada mata kuliah Pemrograman Web II.</p> 
@endsection