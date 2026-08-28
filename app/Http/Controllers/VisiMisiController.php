<?php

namespace App\Http\Controllers;

use App\Models\Visi;

class VisiMisiController extends Controller
{
    public function index()
{
    $visi = Visi::with([
        'maknaVisis' => function ($query) {
            $query->orderBy('urutan');
        },
        'misis' => function ($query) {
            $query->orderBy('nomor');
        }
    ])->first();

    return view('visi-misi', compact('visi'));
}
}