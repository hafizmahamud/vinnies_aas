<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Vinnies\Helper;
use App\Vinnies\Reporter;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use App\Vinnies\DonationsReport;
use Maatwebsite\Excel\Facades\Excel;
use App\Vinnies\Export\ReporterExport;
use App\Vinnies\Export\DonationsExport;

class ReportsController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $this->authorize('read.reports');

        return view('reports.index');
    }

    public function export(Request $request)
    {
        $this->authorize('create.reports');

        $this->validate(
            $request,
            [
                'state'            => [
                    'required',
                    Rule::in(
                        array_merge(
                            ['all'],
                            array_keys(
                                Helper::getStates()
                            )
                        )
                    )
                ],
                'received_at_from' => 'required|date_format:d/m/Y',
                'received_at_to'   => 'required|date_format:d/m/Y|after_or_equal:received_at_from',
            ],
            [
                'received_at_to.after_or_equal' => 'Please check your Date range, the End date cannot be prior to Start date.'
            ]
        );

        $file_name = sprintf(
            'Allocated Students per State per Country for Donations Received between %s and %s inclusive',
            Carbon::createFromFormat('d/m/Y', $request->get('received_at_from'))->format('d-m-Y'),
            Carbon::createFromFormat('d/m/Y', $request->get('received_at_to'))->format('d-m-Y'),
        );

        return Excel::download(new ReporterExport($request->get('state'), $request->get('received_at_from'), $request->get('received_at_to')), $file_name . '.xlsx', \Maatwebsite\Excel\Excel::XLSX);


        // $reporter = new Reporter($request->get('state'), $request->get('received_at_from'), $request->get('received_at_to'));
        // $reporter->export();
    }

    public function donationsExport(Request $request)
    {
        $this->authorize('create.reports');

        $this->validate(
            $request,
            [
                'state'            => [
                    'required',
                    Rule::in(
                        array_merge(
                            ['all'],
                            array_keys(
                                Helper::getStates()
                            )
                        )
                    )
                ],
                'received_at_from' => 'required|date_format:d/m/Y',
                'received_at_to'   => 'required|date_format:d/m/Y|after_or_equal:received_at_from',
            ],
            [
                'received_at_to.after_or_equal' => 'Please check your Date range, the End date cannot be prior to Start date.'
            ]
        );

        $file_name = sprintf(
            'Donations Received between %s and %s inclusive per State per Country',
            Carbon::createFromFormat('d/m/Y', $request->get('received_at_from'))->format('d-m-Y'),
            Carbon::createFromFormat('d/m/Y', $request->get('received_at_to'))->format('d-m-Y'),
        );

        return Excel::download(new DonationsExport($request->get('state'), $request->get('received_at_from'), $request->get('received_at_to')), $file_name . '.xlsx', \Maatwebsite\Excel\Excel::XLSX);
    }
}
