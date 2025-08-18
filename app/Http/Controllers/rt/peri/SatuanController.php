<?php
namespace App\Http\Controllers\rt\peri;
use App\Http\Controllers\Controller;  
use Illuminate\Http\Request;
use App\Models\Satuan;

class SatuanController extends Controller
{
    public function index()
    {
        $satuan = Satuan::all();
        return view('peri::admin.satuan.index', compact('satuan'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama_satuan' => 'required|unique:satuan,nama_satuan|max:50'
        ]);

        Satuan::create($request->all());
        return redirect()->route('admin.satuan.index')->with('success', 'Satuan berhasil ditambahkan');
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'nama_satuan' => 'required|unique:satuan,nama_satuan,'.$id.'|max:50'
        ]);

        $satuan = Satuan::findOrFail($id);
        $satuan->update($request->all());
        return redirect()->route('admin.satuan.index')->with('success', 'Satuan berhasil diperbarui');
    }

    public function destroy($id)
    {
        $satuan = Satuan::findOrFail($id);
        $satuan->delete();
        return redirect()->route('admin.satuan.index')->with('success', 'Satuan berhasil dihapus');
    }
}
