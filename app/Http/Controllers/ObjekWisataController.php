<?php

namespace App\Http\Controllers;

use App\Models\ObjekWisata;

class ObjekWisataController extends Controller
{
    public function index()
    {
        $objekWisatas = ObjekWisata::orderBy('nama')->get();

        return view('objek-wisata', compact('objekWisatas'));
    }
}