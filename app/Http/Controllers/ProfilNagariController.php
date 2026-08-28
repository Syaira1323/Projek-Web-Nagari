<?php

namespace App\Http\Controllers;

use App\Models\ProfilNagari;
use App\Models\AdatBudayaNagari;

class ProfilNagariController extends Controller
{
    public function index()
    {
        $profil = ProfilNagari::first();
        $adatBudaya = AdatBudayaNagari::first();

        return view('profil-nagari.index', compact('profil', 'adatBudaya'));
    }
}