<?php

namespace App\Http\Controllers;

use App\Models\Karyawan;
use Illuminate\Http\Request;

class KaryawanController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $karyawan = Karyawan::latest()->paginate(10);
        return view('karyawan.index', compact('karyawan'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('karyawan.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'nomor_karyawan' => 'required|string|unique:karyawans,nomor_karyawan',
            'nama_karyawan' => 'required|string|max:255',
            'shift' => 'required|integer',
        ]);

        Karyawan::create($request->all());

        return redirect()->route('karyawan.index')
                        ->with('success', 'Karyawan berhasil ditambahkan.');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Karyawan $karyawan)
    {
        return view('karyawan.edit', compact('karyawan'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Karyawan $karyawan)
    {
        /** @var \App\Models\Karyawan $karyawan */ // <-- KOMENTAR INI DITAMBAHKAN UNTUK MEMBANTU EDITOR
        
        $request->validate([
            'nomor_karyawan' => 'required|string|unique:karyawans,nomor_karyawan,' . $karyawan->id,
            'nama_karyawan' => 'required|string|max:255',
            'shift' => 'required|integer',
        ]);

        $karyawan->update($request->all());

        return redirect()->route('karyawan.index')
                        ->with('success', 'Data karyawan berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Karyawan $karyawan)
    {
        $karyawan->delete();
        return redirect()->route('karyawan.index')
                        ->with('success', 'Karyawan berhasil dihapus.');
    }
}