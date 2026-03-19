<?php

namespace App\Exports;

use App\Display;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;

class AllDisplaysExport implements FromView
{
    private $_data, $_from, $_to;

    public function __construct($data, $from, $to) {
        $this->_data = $data;
        $this->_from = $from;
        $this->_to = $to;
    }
    
    public function view(): View
    {
        return view('reports.displays_excel', [
            'data' => $this->_data,
            'from' => $this->_from,
            'to' => $this->_to
        ]);
    }

}
