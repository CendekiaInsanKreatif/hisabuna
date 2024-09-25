<?php

namespace App\Imports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class MultipleJurnal implements WithMultipleSheets 
{
    public function sheets(): array
    {
        return [
            1 => new MultipleHeader(),
            2 => new MultipleDetail()
        ];
    }
}

class MultipleHeader implements ToCollection, WithHeadingRow
{
    public function collection(Collection $rows)
    {
        return $rows;
    }
}

class MultipleDetail implements ToCollection, WithHeadingRow
{
    public function collection(Collection $rows)
    {
        return $rows;
    }
}
