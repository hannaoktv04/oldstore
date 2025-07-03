<?php

namespace App\Http\Controllers;

use App\Models\ItemRequest;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Endroid\QrCode\Builder\Builder;
use Endroid\QrCode\Writer\PngWriter;

class PengajuanController extends Controller
{
    public function downloadNota($id)
    {
        $request = ItemRequest::with(['user', 'details.item.category'])->findOrFail($id);

        $pdf = Pdf::loadView('user.e-nota-pdf', compact('request'))
                  ->setPaper('a4');

        return $pdf->download('e-nota-pengajuan-' . str_pad($request->id, 3, '0', STR_PAD_LEFT) . '.pdf');
    }

    public function cetakResi($id)
    {
        $pengajuan = ItemRequest::with(['user', 'details.item'])->findOrFail($id);

        $kodeResi = 'KP' . str_pad($pengajuan->id, 6, '0', STR_PAD_LEFT);
        $qrLink = route('staff-pengiriman.konfirmasi', $kodeResi); // halaman kurir konfirmasi

        $result = Builder::create()
            ->writer(new PngWriter())
            ->data($qrLink)
            ->logoPath(public_path('assets/img/logo-komdigi.png'))
            ->logoResizeToWidth(100)
            ->size(200)
            ->margin(10)
            ->build();

        $qrBase64 = base64_encode($result->getString());

        return view('admin.resi', compact('pengajuan', 'kodeResi', 'qrBase64'));
    }
}
