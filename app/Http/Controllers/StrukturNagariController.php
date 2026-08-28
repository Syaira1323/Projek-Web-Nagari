<?php

namespace App\Http\Controllers;

use App\Models\StrukturNagari;

class StrukturNagariController extends Controller
{
    public function index()
    {
        $strukturNagari = StrukturNagari::all();

       return view('struktur-nagari.struktur-nagari', compact('strukturNagari'));
    }
}