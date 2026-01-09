<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class KriteriaController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $kriterias = \App\Models\Kriteria::all();
        return view('kriteria.index', compact('kriterias'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('kriteria.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'kode_kriteria' => 'required|unique:kriteria,kode_kriteria|max:10',
            'nama_kriteria' => 'required|unique:kriteria,nama_kriteria|max:100',
            'jenis' => 'required|in:Benefit,Cost',
            'bobot' => 'required|numeric|min:0|max:100',
        ]);

        // Validation logic for total bobot
        $totalBobot = \App\Models\Kriteria::sum('bobot');
        $newBobot = $request->bobot;

        if (($totalBobot + $newBobot) > 100) {
            return back()->withErrors(['bobot' => 'Total bobot melebihi 100%. Total saat ini: ' . $totalBobot . '%'])->withInput();
        }

        \App\Models\Kriteria::create($request->all());

        return redirect()->route('kriteria.index')->with('success', 'Kriteria berhasil ditambahkan.');
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
        $kriteria = \App\Models\Kriteria::findOrFail($id);
        return view('kriteria.edit', compact('kriteria'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $kriteria = \App\Models\Kriteria::findOrFail($id);

        $request->validate([
            'kode_kriteria' => 'required|max:10|unique:kriteria,kode_kriteria,' . $id . ',id_kriteria',
            'nama_kriteria' => 'required|max:100|unique:kriteria,nama_kriteria,' . $id . ',id_kriteria',
            'jenis' => 'required|in:Benefit,Cost',
            'bobot' => 'required|numeric|min:0|max:100',
        ]);

        // Validation logic for total bobot
        $totalBobot = \App\Models\Kriteria::sum('bobot');
        $oldBobot = $kriteria->bobot;
        $newBobot = $request->bobot;

        if (($totalBobot - $oldBobot + $newBobot) > 100) {
            return back()->withErrors(['bobot' => 'Total bobot melebihi 100%. Total saat ini: ' . ($totalBobot - $oldBobot) . '%'])->withInput();
        }

        $kriteria->update($request->all());

        return redirect()->route('kriteria.index')->with('success', 'Kriteria berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $kriteria = \App\Models\Kriteria::findOrFail($id);
        $kriteria->delete();

        return redirect()->route('kriteria.index')->with('success', 'Kriteria berhasil dihapus.');
    }
}
