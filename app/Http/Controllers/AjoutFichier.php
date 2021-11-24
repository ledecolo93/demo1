<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class AjoutFichier extends Controller
{
    public function __construct() {
    	$this->middleware('auth');
    }

    public function ajout() {
    	return view('AjoutFichier');
    }
}
