<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class BarcodeExport implements FromCollection, WithHeadings
{
    public function __construct($collections, $headings)
    {
        $this->collections = $collections;
        $this->headings = $headings;
    }

    public function collection() {
        return $this->collections;
    }

    public function headings() : array {
        return $this->headings;
    }
}
