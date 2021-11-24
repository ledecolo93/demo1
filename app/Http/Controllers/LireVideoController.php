<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Models\Fichiers;

class LireVideoController extends Controller
{
    public function lireVideo($id){
		$video = Fichiers::where('id','=', $id)->get();
		$videos = Fichiers::where('id','!=', $id)->where('type','=', 'video')->get();
		return view('lireVideo', $data = ['video'=>$video, 'videos'=>$videos]);
    }
}
 