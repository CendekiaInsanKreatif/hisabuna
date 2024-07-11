<?php

namespace App\Exports;


use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;

class DaftarJurnalPDF implements FromView
{
    /**
    * @return \Illuminate\Support\Collection
    */

    protected $jurnal;

    public function __construct($jurnal)
    {
        $this->jurnal = $jurnal;
    }

    public function view(): View
    {
        return view('report.daftarjurnal', [
            'jurnal' => $this->jurnal
        ]);
    }
}
