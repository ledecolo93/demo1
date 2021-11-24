<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Models\Fichiers;

class LireLectureController extends Controller
{
    public function lireLecture($id){
		$lecture = Fichiers::where('id','=', $id)->get();
		$lectures = Fichiers::where('id','!=', $id)->where('type','=', 'lecture')->get();
		return view('lirelecture', $data = ['lecture'=>$lecture, 'lectures'=>$lectures]);
    }
}
