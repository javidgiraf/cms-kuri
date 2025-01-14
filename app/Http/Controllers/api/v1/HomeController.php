<?php

namespace App\Http\Controllers\api\v1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\GoldRate;
use App\Models\Scheme;

class HomeController extends Controller
{
    //
    public function gold_rate(){
      $gold_rate=GoldRate::orderBy('id','desc')->first();
      $gold_rate['per_gram']=number_format($gold_rate['per_gram'],2);
      $gold_rate['per_pavan']=number_format($gold_rate['per_pavan'],2);

      return response()->json([
        'gold_rate' => $gold_rate,
         'status' => '1'
       ]);

    }
    public function all_schemes(){
        $schemes=Scheme::orderBy('id','desc')->get();
        return response()->json([
            'schemes' => $schemes,
            'status' => '1'
           ]);
    }
}
