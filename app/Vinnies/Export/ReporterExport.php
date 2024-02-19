<?php

namespace App\Vinnies\Export;

use App\Student;
use App\Donation;
use Carbon\Carbon;
use App\Vinnies\Helper;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Events\AfterSheet;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithProperties;
use Maatwebsite\Excel\Concerns\WithColumnWidths;

class ReporterExport implements FromView, WithEvents, WithColumnWidths, WithProperties
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

    public function getData()
    {
        $data = [];

        foreach ($this->state as $state) {
            $data[$state] = [
                'header' => $this->getReportHeader($state),
                'data'   => $this->getReportData($state)
            ];

            //$data[$state]['total'] = $data[$state]['data']->sum();
        }

        return collect($data);
    }

    public function view(): View
    {
        $name = sprintf(
            'Allocated Students per State per Country for Donations Received between %s and %s inclusive',
            $this->date_start->format('d/m/Y'),
            $this->date_end->format('d/m/Y')
        );

        return view('exports.students', [
            'data' => $this->getData(),
            'name' => $name
        ]);
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
                       'size' => 14,
                       'color' => ['argb' => 'FFFFFFFF'],
                   ],
                   'alignment' => [
                     'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                     'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
                   ],

               ];

               //$event->sheet->getStyle('A1:M1')->getFont()->setBold(true)->getColor()->setARGB(\PhpOffice\PhpSpreadsheet\Style\Color::COLOR_WHITE);
               //$event->sheet->getStyle('B2:B100')->getFont()->getColor()->setARGB(\PhpOffice\PhpSpreadsheet\Style\Color::COLOR_WHITE);

               $conditional1 = new \PhpOffice\PhpSpreadsheet\Style\Conditional();
               $conditional1->setConditionType(\PhpOffice\PhpSpreadsheet\Style\Conditional::CONDITION_CONTAINSTEXT);
               $conditional1->setOperatorType(\PhpOffice\PhpSpreadsheet\Style\Conditional::OPERATOR_CONTAINSTEXT);
               $conditional1->setText("ACT");
               $conditional1->getStyle()->getFont()->getColor()->setARGB(\PhpOffice\PhpSpreadsheet\Style\Color::COLOR_WHITE);

               $conditional2 = new \PhpOffice\PhpSpreadsheet\Style\Conditional();
               $conditional2->setConditionType(\PhpOffice\PhpSpreadsheet\Style\Conditional::CONDITION_CONTAINSTEXT);
               $conditional2->setOperatorType(\PhpOffice\PhpSpreadsheet\Style\Conditional::OPERATOR_CONTAINSTEXT);
               $conditional2->setText("NSW");
               $conditional2->getStyle()->getFont()->getColor()->setARGB(\PhpOffice\PhpSpreadsheet\Style\Color::COLOR_WHITE);

               $conditional3 = new \PhpOffice\PhpSpreadsheet\Style\Conditional();
               $conditional3->setConditionType(\PhpOffice\PhpSpreadsheet\Style\Conditional::CONDITION_CONTAINSTEXT);
               $conditional3->setOperatorType(\PhpOffice\PhpSpreadsheet\Style\Conditional::OPERATOR_CONTAINSTEXT);
               $conditional3->setText("NT");
               $conditional3->getStyle()->getFont()->getColor()->setARGB(\PhpOffice\PhpSpreadsheet\Style\Color::COLOR_WHITE);

               $conditional4 = new \PhpOffice\PhpSpreadsheet\Style\Conditional();
               $conditional4->setConditionType(\PhpOffice\PhpSpreadsheet\Style\Conditional::CONDITION_CONTAINSTEXT);
               $conditional4->setOperatorType(\PhpOffice\PhpSpreadsheet\Style\Conditional::OPERATOR_CONTAINSTEXT);
               $conditional4->setText("QLD");
               $conditional4->getStyle()->getFont()->getColor()->setARGB(\PhpOffice\PhpSpreadsheet\Style\Color::COLOR_WHITE);

               $conditional5 = new \PhpOffice\PhpSpreadsheet\Style\Conditional();
               $conditional5->setConditionType(\PhpOffice\PhpSpreadsheet\Style\Conditional::CONDITION_CONTAINSTEXT);
               $conditional5->setOperatorType(\PhpOffice\PhpSpreadsheet\Style\Conditional::OPERATOR_CONTAINSTEXT);
               $conditional5->setText("TAS");
               $conditional5->getStyle()->getFont()->getColor()->setARGB(\PhpOffice\PhpSpreadsheet\Style\Color::COLOR_WHITE);

               $conditional6 = new \PhpOffice\PhpSpreadsheet\Style\Conditional();
               $conditional6->setConditionType(\PhpOffice\PhpSpreadsheet\Style\Conditional::CONDITION_CONTAINSTEXT);
               $conditional6->setOperatorType(\PhpOffice\PhpSpreadsheet\Style\Conditional::OPERATOR_CONTAINSTEXT);
               $conditional6->setText("VIC");
               $conditional6->getStyle()->getFont()->getColor()->setARGB(\PhpOffice\PhpSpreadsheet\Style\Color::COLOR_WHITE);

               $conditional7 = new \PhpOffice\PhpSpreadsheet\Style\Conditional();
               $conditional7->setConditionType(\PhpOffice\PhpSpreadsheet\Style\Conditional::CONDITION_CONTAINSTEXT);
               $conditional7->setOperatorType(\PhpOffice\PhpSpreadsheet\Style\Conditional::OPERATOR_CONTAINSTEXT);
               $conditional7->setText("SA");
               $conditional7->getStyle()->getFont()->getColor()->setARGB(\PhpOffice\PhpSpreadsheet\Style\Color::COLOR_WHITE);

               $conditional8 = new \PhpOffice\PhpSpreadsheet\Style\Conditional();
               $conditional8->setConditionType(\PhpOffice\PhpSpreadsheet\Style\Conditional::CONDITION_CONTAINSTEXT);
               $conditional8->setOperatorType(\PhpOffice\PhpSpreadsheet\Style\Conditional::OPERATOR_CONTAINSTEXT);
               $conditional8->setText("WA");
               $conditional8->getStyle()->getFont()->getColor()->setARGB(\PhpOffice\PhpSpreadsheet\Style\Color::COLOR_WHITE);

               $conditional9 = new \PhpOffice\PhpSpreadsheet\Style\Conditional();
               $conditional9->setConditionType(\PhpOffice\PhpSpreadsheet\Style\Conditional::CONDITION_CONTAINSTEXT);
               $conditional9->setOperatorType(\PhpOffice\PhpSpreadsheet\Style\Conditional::OPERATOR_CONTAINSTEXT);
               $conditional9->setText("Website/National");
               $conditional9->getStyle()->getFont()->getColor()->setARGB(\PhpOffice\PhpSpreadsheet\Style\Color::COLOR_WHITE);

               $conditionalStyles = $event->sheet->getStyle('A2:A100')->getConditionalStyles();
               $conditionalStyles[] = $conditional1;
               $conditionalStyles[] = $conditional2;
               $conditionalStyles[] = $conditional3;
               $conditionalStyles[] = $conditional4;
               $conditionalStyles[] = $conditional5;
               $conditionalStyles[] = $conditional6;
               $conditionalStyles[] = $conditional7;
               $conditionalStyles[] = $conditional8;
               $conditionalStyles[] = $conditional9;

               $event->sheet->getStyle('A2:A500')->setConditionalStyles($conditionalStyles);

               //$event->sheet->getStyle('B:F')->getAlignment()->setHorizontal('center');
               $event->sheet->getDelegate()->getStyle('A1:F1')->applyFromArray($styleArray)->getAlignment()->setWrapText(true);
               //$event->sheet->getDelegate()->getStyle('A1:F1')->applyFromArray($styleArray)->getAlignment();
               $event->sheet->getDelegate()->getStyle('B2:F500')->getAlignment()->setHorizontal('right');
           },
       ];
    }

    private function getReportData($state)
    {
        $students = Donation::where('is_active', 1)
            ->where('state', $state)
            ->where('received_at', '>=', $this->date_start)
            ->where('received_at', '<=', $this->date_end)
            ->get()
            ->filter(function ($donation) {
                return $donation->sponsorships->count() > 0;
            })
            ->map(function ($donation) {
                return $donation->sponsorships;
            })
            ->map(function ($sponsorships) {
                return $sponsorships->map(function ($sponsorship) {
                    return $sponsorship->student;
                });
            })
            ->flatten(2)
            ->groupBy('country')
            ->sortBy('country')
            ->map(function ($students) {
                return $students->groupBy('education_sector');
            });

        return $students;
    }

    public function columnWidths(): array
    {
        return [
            'A' => 100,
            'B' => 25,
            'C' => 25,
            'D' => 25,
            'E' => 25,
            'F' => 25,
        ];
    }

    private function getReportHeader($state)
    {
        switch ($state) {
            case 'national':
                $header = 'Website/National';
                break;

            default:
                $header = strtoupper($state);
                break;
        }

        return $header;
    }

    public function properties(): array
    {
        return [
            'creator'        => 'St Vincent de Paul Society',
            'lastModifiedBy' => 'St Vincent de Paul Society',
            'title'          => sprintf(
                                    'Allocated Students per State per Country for Donations Received between %s and %s inclusive',
                                    $this->date_start->format('d/m/Y'),
                                    $this->date_end->format('d/m/Y')
                                ),
            'manager'        => 'St Vincent de Paul Society',
            'company'        => 'St Vincent de Paul Society',
        ];
    }


}
