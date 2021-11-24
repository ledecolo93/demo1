<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\fichier;
use App\Models\type;

class FichierAllMediaController extends Controller
{
    public function getAllMedia()
    {
    	$type = type::all();
    	$resultatFinal = array();
    	foreach ($type as $key) {
    		$tabF = array();
    		$tabF["type"] = $key->nom;
    		$fic = fichier::where('type','=', $key->id)->get();
    		$tabF['donnee'] = $fic;
            $tabF['idType'] = $key->id;
    		$resultatFinal[] = $tabF;
    	}

    	return view('acceuil', $data = ['resultatFinal'=>$resultatFinal]);


    }
}
