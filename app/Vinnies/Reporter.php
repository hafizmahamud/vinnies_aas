<?php

namespace App\Vinnies;

use Carbon\Carbon;
use App\Donation;
use Excel;

class Reporter
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
        $data = $this->getData();
        $name = sprintf(
            'Allocated Students per State per Country for Donations Received between %s and %s inclusive',
            $this->date_start->format('d/m/Y'),
            $this->date_end->format('d/m/Y')
        );

        Excel::create($name, function($excel) use ($data) {
            // Set the title
            $excel->setTitle(
                sprintf(
                    'Allocated Students per State per Country for Donations Received between %s and %s inclusive',
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
                $sheet->setWidth('A', 100);
                $sheet->setWidth('B', 25);
                $sheet->setWidth('C', 25);
                $sheet->setWidth('D', 25);
                $sheet->setWidth('E', 25);
                $sheet->setWidth('F', 15);

                // Set sheet headers
                $sheet->row($rowNum, [
                    sprintf(
                        'Allocated Students per State per Country for Donations Received between %s and %s inclusive',
                        $this->date_start->format('d/m/Y'),
                        $this->date_end->format('d/m/Y')
                    ),
                    'Education Sector Primary',
                    'Education Sector Secondary',
                    'Education Sector Tertiary',
                    'Education Sector N/A',
                    'Total'
                ]);

                // Style sheet headers
                $sheet->row($rowNum, function($row) {
                    $row->setFontColor('#ffffff');
                    $row->setBackground('#0070c0');
                    $row->setFontSize('16');
                    $row->setFontWeight('bold');
                });

                $sheet->cell('A1:F1', function ($cells) {
                    $cells->setAlignment('center');
                    $cells->setValignment('center');
                });

                // Different background color for education sector headers
                $sheet->cells('B1:E1', function ($cells) {
                    $cells->setBackground('#002060');
                });

                // Wrap header
                $sheet->getStyle('A1:F1')->getAlignment()->setWrapText(true);

                // Add separator
                $rowNum++;
                $sheet->appendRow([null, null]);

                // Loop for all states
                foreach ($data as $state => $stats) {

                    // Append the state header
                    $rowNum++;
                    $sheet->appendRow([strtoupper($stats['header']), null]);

                    $sheet->row($rowNum, function($row) {
                        $row->setFontColor('#ffffff');
                        $row->setBackground('#7030a0');
                        $row->setFontSize('11');
                        $row->setFontWeight('bold');
                    });

                    // Then all country with counts
                    if ($stats['data']->isEmpty()) {
                        $rowNum++;
                        $sheet->appendRow(['No student allocated', null]);

                        $sheet->row($rowNum, function($row) {
                            $row->setFontSize('11');
                        });
                    } else {

                        $total_primary   = 0;
                        $total_secondary = 0;
                        $total_tertiary  = 0;
                        $total_na        = 0;
                        $total_all       = 0;

                        foreach ($stats['data'] as $country => $sector) {
                            $rowNum++;

                            $edu_primary   = !empty($sector['Primary']) ? $sector['Primary']->count() : 0;
                            $edu_secondary = !empty($sector['Secondary']) ? $sector['Secondary']->count() : 0;
                            $edu_tertiary  = !empty($sector['Tertiary']) ? $sector['Tertiary']->count() : 0;
                            $edu_na        = !empty($sector['N/A']) ? $sector['N/A']->count() : 0;
                            $total_row     = $edu_primary + $edu_secondary + $edu_tertiary + $edu_na;

                            $total_primary   += $edu_primary;
                            $total_secondary += $edu_secondary;
                            $total_tertiary  += $edu_tertiary;
                            $total_na        += $edu_na;
                            $total_all       += $total_row;

                            $sheet->appendRow([
                                $country,
                                $edu_primary,
                                $edu_secondary,
                                $edu_tertiary,
                                $edu_na,
                                $total_row
                            ]);

                            $sheet->row($rowNum, function($row) {
                                $row->setFontSize('11');
                            });
                        }

                        $rowNum++;
                        $sheet->appendRow([
                            sprintf(
                                'TOTAL %s for Donations Received between %s and %s inclusive',
                                strtoupper($stats['header']),
                                $this->date_start->format('d/m/Y'),
                                $this->date_end->format('d/m/Y')
                            ),
                            $total_primary,
                            $total_secondary,
                            $total_tertiary,
                            $total_na,
                            $total_all
                        ]);

                        $sheet->row($rowNum, function($row) {
                            $row->setFontSize('11');
                            $row->setBackground('#c6e0b4');
                            $row->setFontSize('11');
                            $row->setFontWeight('bold');
                        });
                    }

                    $rowNum++;
                    $sheet->appendRow([null, null]);
                }

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
