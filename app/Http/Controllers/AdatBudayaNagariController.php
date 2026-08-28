<?php

namespace App\Http\Controllers;

use App\Models\AdatBudayaNagari;
use Illuminate\Http\Request;

class AdatBudayaNagariController extends Controller
{
    public function index()
    {
        $adatBudaya = AdatBudayaNagari::first();

        return view('profil-nagari.adat-budaya-nagari', compact('adatBudaya'));
    }
}