<?php

namespace App\Imports;

use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Illuminate\Support\Collection;

class JurnalDetailImport implements ToCollection, WithHeadingRow
{
    public function collection(Collection $rows)
    {
        return $rows;
    }
}

