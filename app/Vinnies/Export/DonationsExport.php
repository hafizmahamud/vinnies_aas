<?php

namespace App\Vinnies\Export;

use App\Donation;
use Carbon\Carbon;
use App\Vinnies\Helper;
use Maatwebsite\Excel\Events\AfterSheet;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithProperties;

class DonationsExport implements FromCollection, WithHeadings, ShouldAutoSize, WithMapping, WithEvents, WithProperties
{
    private $state;
    private $date_start;
    private $date_end;

    public function __construct($state = 'all', $date_start, $date_end)
    {
        if ($state == 'all') {
            $this->state = array_keys(Helper::getStates());
        } else {
            $this->state = (array) $state;
        }

        $this->date_start = Carbon::createFromFormat('d/m/Y H:i:s', $date_start . ' 00:00:00');
        $this->date_end   = Carbon::createFromFormat('d/m/Y H:i:s', $date_end . ' 23:59:59');
    }

    public function collection()
    {
        return Donation::where('is_active', 1)
            ->whereIn('state', $this->state)
            ->where('received_at', '>=', $this->date_start)
            ->where('received_at', '<=', $this->date_end)
            ->get()
            ->sortBy('id');
    }

    public function headings(): array
    {
        return [
            'Donation ID',
            'Received Date',
            'Donor State',
            'Donor Certificate Name',
            'Donation Amount',
            'Address',
            'City/Suburb',
            'Postcode',
            'Email',
            'Phone',
            'Mobile',
            'Online Donation',
            'Desires Certificate/Letter',
            'Uploaded Date',
            'Approved Date',

        ];
    }

    /**
    * @var Donation $donation
    */
    public function map($donation): array
    {
        return [
            $donation->id,
            Carbon::createFromFormat('d/m/Y H:i', $donation->received_at)->format('d/m/Y'),
            Helper::getStateNameByKey($donation->state) . ' (' . strtoupper($donation->state) . ')',
            $donation->name_on_certificate,
            $donation->amount,
            $donation->contact_address,
            $donation->contact_suburb,
            $donation->contact_postcode,
            $donation->contact_email,
            $donation->contact_phone,
            $donation->contact_mobile,
            ($donation->online_donation) ? 'YES' : 'NO',
            ((bool) $donation->certificate_needed ? 'YES' : 'NO'),
            $donation->fileImport && $donation->fileImport->created_at ? $donation->fileImport->created_at->format(config('vinnies.datetime_format')) : 'NA',
            $donation->fileImport && $donation->fileImport->approved_at ? $donation->fileImport->approved_at->format(config('vinnies.datetime_format')) : 'NA',
        ];
    }

    /**
    * @return array
    */
    public function registerEvents(): array
    {
       return [
           AfterSheet::class    => function(AfterSheet $event) {
               $styleArray = [
                   'font' => [
                       'bold' => true,
                   ],
                   'alignment' => [
                       'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                   ],
                   'fill' => [
                       'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_GRADIENT_LINEAR,
                       'rotation' => 90,
                       'startColor' => [
                           'argb' => 'FFA0A0A0',
                       ],
                       'endColor' => [
                           'argb' => 'FFFFFFFF',
                       ],
                   ],
               ];

               $event->sheet->getDelegate()->getStyle('A1:O1')->applyFromArray($styleArray);
               $event->sheet->getStyle('A:O')->getAlignment()->setHorizontal('center');
           },
       ];
    }

    public function properties(): array
    {
        return [
            'creator'        => 'St Vincent de Paul Society',
            'lastModifiedBy' => 'St Vincent de Paul Society',
            'title'          => sprintf(
                                    'Donations Received between %s and %s inclusive per State per Country',
                                    $this->date_start->format('d/m/Y'),
                                    $this->date_end->format('d/m/Y')
                                ),
            'manager'        => 'St Vincent de Paul Society',
            'company'        => 'St Vincent de Paul Society',
        ];
    }

}
