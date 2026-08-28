<?php

namespace App\Http\Controllers;

use App\Models\SejarahNagari;

class SejarahNagariController extends Controller
{
    public function index()
    {
        $sejarah = SejarahNagari::orderBy('urutan')->get();

           return view('sejarah', compact('sejarah'));
    }
}