<?php

namespace App\Vinnies;

use App\Vinnies\Helper;
use Carbon\Carbon;
use App\Donation;
use Excel;

class DonationsReport
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

            $data[$state]['total'] = $data[$state]['data']->sum();
        }

        return collect($data);
    }

    public function export()
    {
        //$data = $this->getData();
        $data = Donation::where('is_active', 1)
            ->whereIn('state', $this->state)
            ->where('received_at', '>=', $this->date_start)
            ->where('received_at', '<=', $this->date_end)
            ->get()
            ->sortBy('received_at');

        $name = sprintf(
            'Donations Received between %s and %s inclusive per State per Country',
            $this->date_start->format('d/m/Y'),
            $this->date_end->format('d/m/Y')
        );

        Excel::create($name, function($excel) use ($data) {
            // Set the title
            $excel->setTitle(
                sprintf(
                    'Donations Received between %s and %s inclusive per State per Country',
                    $this->date_start->format('d/m/Y'),
                    $this->date_end->format('d/m/Y')
                )
            )
            ->setKeywords('')
            ->setCreator('St Vincent de Paul Society')
            ->setCompany('St Vincent de Paul Society')
            ->setManager('St Vincent de Paul Society')
            ->setLastModifiedBy('St Vincent de Paul Society');

            $excel->sheet('Sheet1', function($sheet) use ($data) {
                $rowNum = 1;

                // Set columns width
                $sheet->setWidth('A', 30);
                $sheet->setWidth('B', 30);
                $sheet->setWidth('C', 17);
                $sheet->setWidth('D', 30);
                $sheet->setWidth('E', 15);
                $sheet->setWidth('F', 15);
                $sheet->setWidth('G', 30);
                $sheet->setWidth('H', 15);
                $sheet->setWidth('I', 15);
                $sheet->setWidth('J', 17);


                // Set sheet headers
                $sheet->row($rowNum, [
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
                ]);

                // Style sheet headers
                $sheet->row($rowNum, function($row) {
                    //$row->setFontColor('#ffffff');
                    //$row->setBackground('#0070c0');
                    //$row->setFontSize('16');
                    $row->setFontWeight('bold');
                });

                $sheet->cell('A1:J1', function ($cells) {
                    $cells->setAlignment('center');
                    $cells->setValignment('center');
                });

                // Different background color for education sector headers
                // $sheet->cells('B1:E1', function ($cells) {
                //     $cells->setBackground('#002060');
                // });

                // Wrap header
                $sheet->getStyle('A1:J1')->getAlignment()->setWrapText(true);

                // Add separator
                $rowNum++;
                //$sheet->appendRow([null, null]);

                // Loop for all states
                foreach ($data as $donation) {

                  $sheet->appendRow([
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
                      ]);

                      // $sheet->row($rowNum, function($row) {
                      //     $row->setFontSize('11');
                      //     $row->setBackground('#c6e0b4');
                      //     $row->setFontSize('11');
                      //     $row->setFontWeight('bold');
                      // });




                      $rowNum++;
                }

                $sheet->cell('E2:E'.$rowNum, function ($cells) {
                    $cells->setAlignment('center');
                    $cells->setValignment('center');
                });

                $sheet->cell('J2:J'.$rowNum, function ($cells) {
                    $cells->setAlignment('center');
                    $cells->setValignment('center');
                });

                $sheet->cell('A2:J'.$rowNum, function ($cells) {
                    //$cells->setAlignment('center');
                    $cells->setValignment('top');
                });

                $sheet->getStyle('A2:J'.$rowNum)->getAlignment()->setWrapText(true);


            });
        })->export('xlsx');
        exit;
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
}
