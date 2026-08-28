<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AdminWargaController extends Controller
{
    /**
     * Tampilkan daftar data warga terdaftar
     */
    public function index(Request $request): View
    {
        $search = $request->query('search');

        $query = User::where('role', 'warga')->withCount('suratPermohonans')->latest();

        if (!empty($search)) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('nik', 'like', "%{$search}%")
                  ->orWhere('username', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%")
                  ->orWhere('address', 'like', "%{$search}%");
            });
        }

        $wargaList = $query->paginate(15);
        $totalWarga = User::where('role', 'warga')->count();

        return view('admin.warga.index', compact('wargaList', 'search', 'totalWarga'));
    }
}
