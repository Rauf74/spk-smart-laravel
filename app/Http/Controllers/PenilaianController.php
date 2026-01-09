<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PenilaianController extends Controller
{
    /**
     * Display list of alternatives for user to assess
     */
    public function index()
    {
        $id_user = \Illuminate\Support\Facades\Auth::id();

        // Logic: getAllPenilaianByUser
        // Get all alternatifs and check if current user has reviewed them
        $alternatifs = \App\Models\Alternatif::all()->map(function ($alternatif) use ($id_user) {
            $is_dinilai = \App\Models\Penilaian::where('id_alternatif', $alternatif->id_alternatif)
                ->where('id_user', $id_user)
                ->exists();
            $alternatif->status_penilaian = $is_dinilai;
            return $alternatif;
        });

        return view('penilaian.index', compact('alternatifs'));
    }

    /**
     * Show the assessment form for specific alternatif
     */
    public function create(string $id_alternatif)
    {
        $alternatif = \App\Models\Alternatif::findOrFail($id_alternatif);

        // Get Pertanyaan by Alternatif with Kriteria
        $pertanyaans = \App\Models\Pertanyaan::with(['kriteria.subkriteria'])
            ->where('id_alternatif', $id_alternatif)
            ->get()
            ->sortBy(function ($query) {
                return $query->kriteria->nama_kriteria;
            });

        // Group by Kriteria for easier display in view if needed
        return view('penilaian.create', compact('alternatif', 'pertanyaans'));
    }

    /**
     * Store assessment data
     */
    public function store(Request $request)
    {
        $request->validate([
            'id_alternatif' => 'required|exists:alternatif,id_alternatif',
            'jawaban' => 'required|array', // Array of [id_pertanyaan => id_subkriteria]
            'jawaban.*' => 'exists:subkriteria,id_subkriteria',
        ]);

        $id_user = \Illuminate\Support\Facades\Auth::id();
        $id_alternatif = $request->id_alternatif;

        DB::beginTransaction();
        try {
            // Delete existing penilaian for this user & alternatif (Override method)
            \App\Models\Penilaian::where('id_user', $id_user)
                ->where('id_alternatif', $id_alternatif)
                ->delete();

            foreach ($request->jawaban as $id_pertanyaan => $id_subkriteria) {
                // Get related data needed for the table
                $pertanyaan = \App\Models\Pertanyaan::find($id_pertanyaan);
                $subkriteria = \App\Models\Subkriteria::find($id_subkriteria);

                // Insert
                \App\Models\Penilaian::create([
                    'id_user' => $id_user,
                    'id_alternatif' => $id_alternatif,
                    'id_kriteria' => $pertanyaan->id_kriteria, // From pertanyaan relation
                    'id_pertanyaan' => $id_pertanyaan,
                    'id_subkriteria' => $id_subkriteria,
                    'jawaban' => $subkriteria->nilai // Value from subkriteria
                ]);
            }

            DB::commit();
            return redirect()->route('penilaian.index')->with('success', 'Penilaian berhasil disimpan.');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['error' => 'Terjadi kesalahan saat menyimpan penilaian: ' . $e->getMessage()]);
        }
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
        // Typically penilaian is re-taken, so redirect to create
        return redirect()->route('penilaian.create', $id);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
