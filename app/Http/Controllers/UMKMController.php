<?php

namespace App\Http\Controllers;

use App\Models\UMKM;

class UMKMController extends Controller
{
    public function index()
    {
        $umkms = UMKM::all();

        return view('umkm', compact('umkms'));
    }
}