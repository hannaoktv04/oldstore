<?php

namespace App\Http\Controllers;

use App\Models\ItemRequest;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;

class PengajuanController extends Controller
{
    public function downloadNota($id)
    {
        $request = ItemRequest::with(['user', 'details.item.category'])->findOrFail($id);

        $pdf = Pdf::loadView('user.e-nota-pdf', compact('request'))
                  ->setPaper('a4');

        return $pdf->download('e-nota-pengajuan-' . str_pad($request->id, 3, '0', STR_PAD_LEFT) . '.pdf');
    }
}
