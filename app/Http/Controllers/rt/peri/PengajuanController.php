<?php
namespace App\Http\Controllers\rt\peri;
use App\Http\Controllers\Controller;  
use App\Models\ItemRequest;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Endroid\QrCode\Builder\Builder;
use Endroid\QrCode\Writer\PngWriter;
use Illuminate\Support\Facades\Storage;


class PengajuanController extends Controller
{
    public function downloadNota($id)
    {
        $request = ItemRequest::with(['user', 'details.item.category'])->findOrFail($id);

        $pdf = Pdf::loadview('peri::user.e-nota-pdf', compact('request'))
                  ->setPaper('a4');

        return $pdf->download('e-nota-pengajuan-' . str_pad($request->id, 3, '0', STR_PAD_LEFT) . '.pdf');
    }

    public function cetakResi($id, Request $request)
    {
        $pengajuan = ItemRequest::with(['user', 'details.item'])->findOrFail($id);

        $kodeResi = 'KP' . str_pad($pengajuan->id, 6, '0', STR_PAD_LEFT);
        $qrLink = route('staff-pengiriman.konfirmasi', $kodeResi); 

        $result = Builder::create()
            ->writer(new PngWriter())
            ->data($qrLink)
            ->logoPath(public_path('assets/img/logo-komdigi.png'))
            ->logoResizeToWidth(100)
            ->size(200)
            ->margin(10)
            ->build();

        $qrBase64 = base64_encode($result->getString());

        $autoPrint = $request->query('auto') == 1;

        return view('peri::admin.resi', compact('pengajuan', 'kodeResi', 'qrBase64'));
    }

    public function signature($id)
    {
        $request = ItemRequest::findOrFail($id);
        return view('peri::user.signature', compact('request'));
    }

    public function signatureStore(Request $http, $id)
    {
        $http->validate([
            'signature' => ['required','string'],
        ]);

        $dataUrl = $http->input('signature');
        $png = preg_replace('/^data:image\/\w+;base64,/', '', $dataUrl);
        $png = str_replace(' ', '+', $png);
        $binary = base64_decode($png);

        $path = 'signatures/' . now()->format('YmdHis') . "_req{$id}.png";
        Storage::disk('public')->put($path, $binary);

        $pengajuan = ItemRequest::findOrFail($id);
        $pengajuan->ttd_path = $path;      
        $pengajuan->save();

        return redirect()
            ->route('pengajuan.enota', $pengajuan->id)  
            ->with('success', 'Tanda tangan berhasil disimpan.');
    }
}