<?php

namespace App\Exports;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;

class DisplayCalibrationExport implements FromView
{
    private $_data, $_from, $_to, $_site;

    public function __construct($data, $from, $to, $site) {
        $this->_data = $data;
        $this->_from = $from;
        $this->_to = $to;
        $this->_site = $site;
    }
    
    public function view(): View
    {
        return view('reports.display_calibration_excel', [
            'data' => $this->_data,
            'from' => $this->_from,
            'to' => $this->_to,
            'site' => $this->_site
        ]);
    }

}
