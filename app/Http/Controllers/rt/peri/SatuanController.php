<?php

namespace App\Http\Controllers\rt\peri;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Satuan;
use Illuminate\Validation\Rule;

class SatuanController extends Controller
{
    public function index()
    {
        $satuan = Satuan::orderBy('nama_satuan')->get();
        return view('peri::admin.satuan.index', compact('satuan'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama_satuan' => ['required', 'max:50', 'unique:satuan,nama_satuan'],
        ]);
        $s = Satuan::create($validated);
        if ($request->ajax()) {
            return response()->json([
                'status'  => 'ok',
                'message' => 'Satuan berhasil ditambahkan',
                'data'    => $s,
            ]);
        }
        return redirect()->route('admin.satuan.index')
            ->with('success', 'Satuan berhasil ditambahkan');
    }

    public function update(Request $request, $id)
    {
        $satuan = Satuan::findOrFail($id);
        $validated = $request->validate([
            'nama_satuan' => [
                'required',
                'max:50',
                Rule::unique('satuan', 'nama_satuan')->ignore($satuan->id),
            ],
        ]);
        $satuan->update($validated);
        if ($request->ajax()) {
            return response()->json([
                'status'  => 'ok',
                'message' => 'Satuan berhasil diperbarui',
                'data'    => $satuan,
            ]);
        }
        return redirect()->route('admin.satuan.index')
            ->with('success', 'Satuan berhasil diperbarui');
    }

    public function destroy(Request $request, $id)
    {
        $satuan = Satuan::findOrFail($id);
        $satuan->delete();

        if ($request->ajax()) {
            return response()->json([
                'status'  => 'ok',
                'message' => 'Satuan berhasil dihapus',
                'id'      => (int) $id,
            ]);
        }
        return redirect()->route('admin.satuan.index')
            ->with('success', 'Satuan berhasil dihapus');
    }
}
