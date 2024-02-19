<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    protected $exclude_orders = [];

    protected function sortModelFromRequest($model, $request)
    {
        if (!empty($request->get('order'))) {
            foreach ($request->get('order') as $order) {
                if ($order['column'] == 0) {
                    continue;
                }

                if (in_array($request->get('columns')[$order['column']]['name'], $this->exclude_orders)) {
                    continue;
                }

                $model->orderBy($request->get('columns')[$order['column']]['name'], $order['dir']);
            }
        }

        return $model;
    }

    protected function getDatatableBaseData($model, $request)
    {
        return [
            'draw'            => (int) $request->get('draw'),
            'recordsTotal'    => $model->total(),
            'recordsFiltered' => $model->total(),
            'pagination'      => [
                'first'   => 1,
                'last'    => $model->lastPage(),
                'current' => $model->currentPage(),
                'total'   => $model->total(),
            ],
            'data' => []
        ];
    }
}
