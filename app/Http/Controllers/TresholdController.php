<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Treshold;

class TresholdController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function list()
    {
        return Treshold::all();
    }

    public function update($amount)
    {
        return Treshold::create(['amount' => floatval($amount)]);
    }
}
