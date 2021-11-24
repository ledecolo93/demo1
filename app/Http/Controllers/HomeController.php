<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use File;
use App\Models\Models\Fichiers;
use Validator;
use DB;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        $fichier = Fichiers::all();
        return view('home')->with(compact('fichier'));
    }

    public function upload(Request $request)
    {
        $resultat = $request->file('fichier')->store('public/videos');
        return redirect()->back();
    }

    public function enreg(Request $request)
    {
        $typeF = $request->type;
        if ($typeF == "video") {
            $validator = Validator::make($request->all(), [
            'affiche' => 'required|image|mimes:jpeg,png,jpg,gif|max:3072',
            'fichier' => 'required|mimes:mp4,3gp,AVI'
            ]);

            $file =  $request->file('affiche');
            $file1 =  $request->file('fichier'); 
            $nameAff = str_replace(" ", "_", $request['titre']).time().'.'.$file->getClientOriginalExtension();
            $nameFic = str_replace(" ", "_", $request['titre']).time().'.'.$file1->getClientOriginalExtension();          
            $path1 = base_path() . '/public/affiches/';
            $file->move($path1, $nameAff);
            
            $path2 = base_path() . '/public/videos/';
            $file1->move($path2, $nameFic);
            DB::table('fichiers')->insert([
                'type'=>$request->type,
                'categorie'=>$request->categorie,
                'lien_fichier'=>'/videos/'.$nameFic,
                'lien_affiche'=>'/affiches/'.$nameAff,
                'description'=>$request->description,
                'duree'=>'inconnue',
                'titre'=>$request->titre,
            ]); 

            return 1;
        }

        if ($typeF == "audio") {
            $validator = Validator::make($request->all(), [
            'affiche' => 'required|image|mimes:jpeg,png,jpg,gif|max:3072',
            'fichier' => 'required|mimes:mp3'
            ]);

            $file =  $request->file('affiche');
            $file1 =  $request->file('fichier'); 
            $nameAff = str_replace(" ", "_", $request['titre']).time().'.'.$file->getClientOriginalExtension();
            $nameFic = str_replace(" ", "_", $request['titre']).time().'.'.$file1->getClientOriginalExtension();          
            //$path1 = base_path() . '/public/affiches/';
           // $file->move($path1, $nameAff);

            $path2 = base_path() . '/public/audios/';
            //$file1->move($path2, $nameFic);

            $disk = Storage::disk('s3');
            $disk->put($path2, fopen($file1, 'r+'));
            DB::table('fichiers')->insert([
                'type'=>$request->type,
                'categorie'=>$request->categorie,
                'lien_fichier'=>'/audios/'.$nameFic,
                'lien_affiche'=>'/affiches/'.$nameAff,
                'description'=>$request->description,
                'duree'=>'inconnue',
                'titre'=>$request->titre,
            ]);

            return 2; 
        }


        if ($typeF == "lecture") {
            $validator = Validator::make($request->all(), [
            'affiche' => 'required|image|mimes:jpeg,png,jpg,gif|max:3072',
            'fichier' => 'required|mimes:pdf'
            ]);

            $file =  $request->file('affiche');
            $file1 =  $request->file('fichier'); 
            $nameAff = str_replace(" ", "_", $request['titre']).time().'.'.$file->getClientOriginalExtension();
            $nameFic = str_replace(" ", "_", $request['titre']).time().'.'.$file1->getClientOriginalExtension();          
            $path1 = base_path() . '/public/affiches/';
            $file->move($path1, $nameAff);

            $path2 = base_path() . '/public/lectures/';
            $file1->move($path2, $nameFic);
            DB::table('fichiers')->insert([
                'type'=>$request->type,
                'categorie'=>$request->categorie,
                'lien_fichier'=>'/audios/'.$nameFic,
                'lien_affiche'=>'/affiches/'.$nameAff,
                'description'=>$request->description,
                'duree'=>'inconnue',
                'titre'=>$request->titre,
            ]); 

            return 3;
        }

        return $request->titre;




       /* $validator = Validator::make($request->all(), [
            'affiche' => 'required|image|mimes:jpeg,png,jpg,gif|max:3072',
            'fichier' => 'required'
        ]);
            $file =  $request->file('affiche');
            $file1 =  $request->file('fichier');
            $nameAff = $request['titre'].'.'.$file->getClientOriginalExtension();
            $nameFic = $request['titre'].time().'.'.$file1->getClientOriginalExtension();

            $path = base_path() . '/public/affiches/';
            $request->file('affiche')->move($path, $nameFic);

            //$file->store('', ['disk'=>'my_files']);

            //$file1->store('', ['disk'=>'my_files']);
            return $file;

        $lienF = explode("/", $request->fichier);
        $fichier = new Fichiers;
        $lienfic = "/videos/".$lienF[sizeof($lienF)-1];
        $lienaff;

        DB::table('pictbl')->insert([
            'orginal_name'=>$name,
            'format'=>$base,
            'patch'=>$patch
        ]); 
        if ($request->etat == 0) {
            $lienA = explode("/", $request->affiche);
            $lienaff = "/affiches/".$lienA[sizeof($lienA)-1];
            File::copy(public_path($request->affiche), public_path($lienaff));
        } else {
            $lienaff = $request->affiche;
        }


        File::copy(public_path($request->fichier), public_path($lienfic));

        $fichier->type = $request->type;
        $fichier->categorie = $request->categorie;
        $fichier->lien_fichier = $lienfic;
        $fichier->lien_affiche = $lienaff;
        $fichier->titre = $request->titre;
        $fichier->description = $request->description;

        $fichier.save();

        return $fichier;*/
    }
}
