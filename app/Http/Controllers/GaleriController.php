<?php

namespace App\Http\Controllers;

use App\Models\FotoKabaNagari;
use Illuminate\View\View;

class GaleriController extends Controller
{
    public function index(): View
    {
        $galeriList = FotoKabaNagari::with('kabaNagari')->latest()->paginate(12);

        return view('galeri', compact('galeriList'));
    }
}
