<?php

namespace App\Http\Controllers;
use Spatie\Holidays\Holidays;
 

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;

class Controller extends BaseController
{
    use AuthorizesRequests, ValidatesRequests;

    public function getAllHolidays(){
        $holidays = Holidays::for(country: 'co', year: 2024)->get();
        return response()->json($holidays);
    }
}
