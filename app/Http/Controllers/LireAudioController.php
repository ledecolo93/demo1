<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Models\Fichiers;

class LireAudioController extends Controller
{
    public function lireAudio($id){
		$audio = Fichiers::where('id','=', $id)->get();
		$audios = Fichiers::where('id','!=', $id)->where('type','=', 'audio')->get();
		return view('lireAudio', $data = ['audio'=>$audio, 'audios'=>$audios]);
    }
}
