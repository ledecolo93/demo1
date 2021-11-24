<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\fichier;
use Illuminate\Support\Facades\DB;

class FichierController extends Controller
{
    public function getVideo()
    {
    	$video = Fichiers::where('type','=', 'video')->get();
    	return view('video', $data = ['video'=>$video]);
    }

    public function getVideocategorie($id)
    {
    	$resultat = DB::table('categories')
        ->where('idType', '=', $id)
        ->where('nbreFichier', '!=', 0)
        ->orderBy('nom')
        ->get();

        return view('videoCategorie', $data = ['resultatFinal'=>$resultat]);
    }

    public function getVideoSousCategorie($id)
    {
    	$resultat = DB::table('sous-categories')
        ->where('idCategorie', '=', $id)
        ->where('nbreFichier', '!=', 0)
        ->orderBy('nom')
        ->get();

        return view('videoSousCategorie', $data = ['resultatFinal'=>$resultat]);
    }

    public function getVideoFichier($id)
    {
    	$resultat = DB::table('fichiers')
        ->where('sous-categorie', '=', $id)
        ->orderBy('nomf')
        ->get();

        return view('videoFichier', $data = ['resultatFinal'=>$resultat]);
    }

    public function lireVideoFichier($id)
    {
    	$resultat = fichier::find($id);

        return view('lireVideo', $data = ['resultatFinal'=>$resultat]);
    }
}
