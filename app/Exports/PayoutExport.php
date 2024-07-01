<?php

namespace App\Exports;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Config;

class PayoutExport implements FromCollection, WithHeadings, ShouldAutoSize
{
    use Exportable;
    protected $data;

    function __construct($data) {
        $this->data = $data;
    }

    public function collection()
    { 
        $payoutArray    = $this->data;
        $payoutData     = array();
        $i = 1;

        foreach($payoutArray as $data) {
            $payout_date = date(config::get("Reading.date_format"),strtotime($data->payout_date));

            $payoutData[$i]['Name']         = (isset($data->name))?$data->name:'';
            $payoutData[$i]['Email']        = (isset($data->email))?$data->email:'';
            $payoutData[$i]['Amount']       = (isset($data->amount))?'$'.$data->amount:'';
            $payoutData[$i]['Payout Date']  = (isset($data->payout_date))?$payout_date:'';
            
            $i++;	

        }
        return collect([$payoutData]);
    }

    public function headings(): array
    {
        return [
            'Name',
            'Email',
            'Amount',
            'Payout Date'
            
        ];
    }
}