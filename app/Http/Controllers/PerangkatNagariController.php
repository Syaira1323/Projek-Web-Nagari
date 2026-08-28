<?php

namespace App\Http\Controllers;

use App\Models\PerangkatNagari;

class PerangkatNagariController extends Controller
{
    public function index()
    {
        $perangkatNagaris = PerangkatNagari::orderBy('id')->get();

        return view('perangkat-nagari.index', compact('perangkatNagaris'));
    }
}