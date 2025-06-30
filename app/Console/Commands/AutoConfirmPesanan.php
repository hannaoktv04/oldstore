<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\ItemRequest;

class AutoConfirmPesanan extends Command
{
    protected $signature = 'pengajuan:autokonfirmasi';
    protected $description = 'Otomatis konfirmasi pesanan jika lebih dari 3 hari tidak dikonfirmasi oleh user';

    public function handle()
    {
        $requests = ItemRequest::autoConfirmable()->get();

        foreach ($requests as $request) {
            $request->user_confirmed = true;
            $request->save();

            $this->info("Pengajuan #{$request->id} otomatis dikonfirmasi.");
        }

        return Command::SUCCESS;
    }
}
