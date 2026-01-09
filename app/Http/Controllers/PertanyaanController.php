<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class PertanyaanController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $pertanyaans = \App\Models\Pertanyaan::with(['kriteria', 'alternatif'])->get();
        return view('pertanyaan.index', compact('pertanyaans'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $kriterias = \App\Models\Kriteria::all();
        $alternatifs = \App\Models\Alternatif::all();
        return view('pertanyaan.create', compact('kriterias', 'alternatifs'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'id_kriteria' => 'required|exists:kriteria,id_kriteria',
            'id_alternatif' => 'required|exists:alternatif,id_alternatif',
            'teks_pertanyaan' => 'required|string',
        ]);

        \App\Models\Pertanyaan::create($request->all());

        return redirect()->route('pertanyaan.index')->with('success', 'Pertanyaan berhasil ditambahkan.');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $pertanyaan = \App\Models\Pertanyaan::findOrFail($id);
        $kriterias = \App\Models\Kriteria::all();
        $alternatifs = \App\Models\Alternatif::all();
        return view('pertanyaan.edit', compact('pertanyaan', 'kriterias', 'alternatifs'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $pertanyaan = \App\Models\Pertanyaan::findOrFail($id);

        $request->validate([
            'id_kriteria' => 'required|exists:kriteria,id_kriteria',
            'id_alternatif' => 'required|exists:alternatif,id_alternatif',
            'teks_pertanyaan' => 'required|string',
        ]);

        $pertanyaan->update($request->all());

        return redirect()->route('pertanyaan.index')->with('success', 'Pertanyaan berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $pertanyaan = \App\Models\Pertanyaan::findOrFail($id);
        $pertanyaan->delete();

        return redirect()->route('pertanyaan.index')->with('success', 'Pertanyaan berhasil dihapus.');
    }
}
