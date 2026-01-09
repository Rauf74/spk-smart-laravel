<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class SubkriteriaController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $subkriterias = \App\Models\Subkriteria::with('kriteria')->get();
        return view('subkriteria.index', compact('subkriterias'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $kriterias = \App\Models\Kriteria::all();
        return view('subkriteria.create', compact('kriterias'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'id_kriteria' => 'required|exists:kriteria,id_kriteria',
            'nama_subkriteria' => 'required|max:50',
            'nilai' => 'required|numeric',
        ]);

        \App\Models\Subkriteria::create($request->all());

        return redirect()->route('subkriteria.index')->with('success', 'Sub Kriteria berhasil ditambahkan.');
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
        $subkriteria = \App\Models\Subkriteria::findOrFail($id);
        $kriterias = \App\Models\Kriteria::all();
        return view('subkriteria.edit', compact('subkriteria', 'kriterias'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $subkriteria = \App\Models\Subkriteria::findOrFail($id);

        $request->validate([
            'id_kriteria' => 'required|exists:kriteria,id_kriteria',
            'nama_subkriteria' => 'required|max:50',
            'nilai' => 'required|numeric',
        ]);

        $subkriteria->update($request->all());

        return redirect()->route('subkriteria.index')->with('success', 'Sub Kriteria berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $subkriteria = \App\Models\Subkriteria::findOrFail($id);
        $subkriteria->delete();

        return redirect()->route('subkriteria.index')->with('success', 'Sub Kriteria berhasil dihapus.');
    }
}
