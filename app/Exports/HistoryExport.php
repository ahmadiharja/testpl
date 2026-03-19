<?php

namespace App\Exports;

use App\Display;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;

class HistoryExport implements FromView
{
    private $_data;

    public function __construct($data) {
        $this->_data = $data;
    }
    
    public function view(): View
    {
        return view('reports.displays', [
            'data' => $this->_data,
            
        ]);
    }

}
