<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Models\Fichiers;

class FichierLectureController extends Controller
{
    public function getLecture() {
    	$lecture = Fichiers::where('type','=', 'lecture')->get();
    	return view('lecture', $data = ['lecture'=>$lecture]);
    }
}
