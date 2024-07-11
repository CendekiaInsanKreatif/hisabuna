<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Menu;

class MenuSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $mainApp = Menu::create([
            'name' => 'MAIN APPLICATION',
            'route' => '#',
            'icon' => '',
        ]);

        Menu::create([
            'name' => 'COA',
            'route' => 'coas.index',
            'icon' => 'images/icons/ic-saldo-awal.svg',
            'parent_id' => $mainApp->id,
        ]);

        Menu::create([
            'name' => 'Jurnal',
            'route' => 'jurnal.index',
            'icon' => 'images/icons/ic-laporan.svg',
            'parent_id' => $mainApp->id,
        ]);

        $laporan = Menu::create([
            'name' => 'LAPORAN',
            'route' => '#',
            'icon' => '',
        ]);

        Menu::create([
            'name' => 'Buku Besar',
            'route' => 'report.bukubesar',
            'icon' => 'images/icons/ic-laporan.svg',
            'parent_id' => $laporan->id,
        ]);

        Menu::create([
            'name' => 'Laba Rugi',
            'route' => 'report.labarugi',
            'icon' => 'images/icons/ic-laporan.svg',
            'parent_id' => $laporan->id,
        ]);

        Menu::create([
            'name' => 'Perubahan Ekuitas',
            'route' => '#',
            'icon' => 'images/icons/ic-laporan.svg',
            'parent_id' => $laporan->id,
        ]);

        Menu::create([
            'name' => 'Neraca',
            'route' => 'report.neraca',
            'icon' => 'images/icons/ic-laporan.svg',
            'parent_id' => $laporan->id,
        ]);

        Menu::create([
            'name' => 'Neraca Saldo',
            'route' => 'report.neracasaldo',
            'icon' => 'images/icons/ic-laporan.svg',
            'parent_id' => $laporan->id,
        ]);

        Menu::create([
            'name' => 'Neraca Perbandingan',
            'route' => 'report.neracaperbandingan',
            'icon' => 'images/icons/ic-laporan.svg',
            'parent_id' => $laporan->id,
        ]);

        Menu::create([
            'name' => 'Laporan Arus Kas',
            'route' => '#',
            'icon' => 'images/icons/ic-laporan.svg',
            'parent_id' => $laporan->id,
        ]);

        $pengaturan = Menu::create([
            'name' => 'PENGATURAN',
            'route' => '#',
            'icon' => '',
        ]);

        Menu::create([
            'name' => 'User Access',
            'route' => '#',
            'icon' => 'images/icons/ic-laporan.svg',
            'parent_id' => $pengaturan->id,
        ]);
    }
}
