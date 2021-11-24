<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Models\Fichiers;

class FichierAudioController extends Controller
{
    public function getAudio()
    {
    	$audio = Fichiers::where('type','=', 'audio')->get();
    	return view('audio', $data = ['audio'=>$audio]);
    }
}
