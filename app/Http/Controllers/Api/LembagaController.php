<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Lembaga;
use Illuminate\Http\Request;

class LembagaController extends Controller
{
    public function index()
    {
        return response()->json(Lembaga::orderBy('nama')->get());
    }

    public function store(Request $request)
    {
        $request->validate(['nama' => 'required|string|max:100|unique:lembaga,nama']);
        // ID akan dibuat otomatis oleh Model
        $lembaga = Lembaga::create(['nama' => $request->nama]);
        return response()->json($lembaga, 201);
    }

    public function show(Lembaga $lembaga)
    {
        return response()->json($lembaga);
    }

    public function update(Request $request, Lembaga $lembaga)
    {
        $request->validate(['nama' => 'required|string|max:100|unique:lembaga,nama,' . $lembaga->id_lb . ',id_lb']);
        $lembaga->update($request->only('nama'));
        return response()->json($lembaga);
    }

    public function destroy(Lembaga $lembaga)
    {
        $lembaga->delete();
        return response()->json(null, 204);
    }
}
