<?php

namespace App\Http\Middleware;

use Illuminate\Http\Request;
use Inertia\Middleware;

class HandleInertiaRequests extends Middleware
{
    /**
     * The root template that is loaded on the first page visit.
     *
     * @var string
     */
    protected $rootView = 'app';

    /**
     * Determine the current asset version.
     */
    public function version(Request $request): ?string
    {
        return parent::version($request);
    }

    /**
     * Define the props that are shared by default.
     *
     * @return array<string, mixed>
     */
    public function share(Request $request): array 
{ 
    return [ 
        ...parent::share($request), 
        'auth' => [ 
            'user' => $request->user(), 
        ], 
        // Blok baru: membagikan pesan flash ke SELURUH halaman Vue secara otomatis 
        'flash' => [ 
            // session('success') membaca pesan yang dikirim lewat ->with('success', ...) 
            // pada Langkah 2 dan 4; bernilai null apabila tidak ada pesan 
            'success' => session('success'), 
        ], 
    ]; 
}
}
