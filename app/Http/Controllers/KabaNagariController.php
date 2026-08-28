<?php

namespace App\Http\Controllers;

use App\Models\KabaNagari;

class KabaNagariController extends Controller
{
    public function index()
    {
        $kabaNagaris = KabaNagari::orderBy('tanggal', 'desc')->get();

        return view('kaba-nagari', compact('kabaNagaris'));
    }
}