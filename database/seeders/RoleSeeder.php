<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Role;

class RoleSeeder extends Seeder
{
    public function run()
    {
        $roles = [
            'admin',
            'pegawai',
            'staff_pengiriman',
            'Administrator',
            'Bagian TLHP',
            'Bagian Program dan Evaluasi',
            'Auditor',
            'Bagian Keuangan dan Rumah Tangga',
            'Inspektur',
            'Bagian Kepegawaian',
            'Subbag Tata Usaha',
            'Sekretaris Inspektorat Jenderal',
            'PIC Eselon1',
            'PIC Obris',
            'PPK',
            'Koordinator TLHP',
            'Sub Koordinator TLHP',
            'Koordinator Bagian Program dan Evaluasi',
            'Sub Koordinator Bagian Program dan Evaluasi',
            'Koordinator Bagian Keuangan dan Rumah Tangga',
            'Sub Koordinator Bagian Keuangan dan Rumah Tangga',
            'Koordinator Bagian Kepegawaian',
            'Sub Koordinator Bagian Kepegawaian',
            'BPK',
            'Staff PPK',
            'Bendahara',
            'Staff',
            'PIC TLHP',
            'BPK LK',
            'Staff RT',
            'Staff PPPN',
            'Bagian Tata Kelola dan Kepatuhan Internal'
        ];

        foreach ($roles as $role) {
            Role::firstOrCreate(['nama_role' => $role]);
        }
    }
}
