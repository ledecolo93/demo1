<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\FichierController;
use App\Http\Controllers\LireVideoController;
use App\Http\Controllers\FichierAudioController;
use App\Http\Controllers\LireAudioController;
use App\Http\Controllers\FichierAllMediaController;
use App\Http\Controllers\FichierLectureController;
use App\Http\Controllers\LireLectureController;
use App\Models\Models\Fichiers;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', [FichierAllMediaController::class, 'getAllMedia']);

Route::get('/video/categorie/{id}', [FichierController::class, 'getVideocategorie']);

Route::get('/video/categorie/sous-categorie/{id}', [FichierController::class, 'getVideoSousCategorie']);

Route::get('/video/categorie/sous-categorie/fichier/{id}', [FichierController::class, 'getVideoFichier']);

Route::get('/video/categorie/sous-categorie/fichier/lire/{id}', [FichierController::class, 'lireVideoFichier']);

Route::get('/video', [FichierController::class, 'getVideo']);

Route::get('/audio', [FichierAudioController::class, 'getAudio']);

Route::get('/lecture', [FichierLectureController::class, 'getLecture']);

Route::get('/audio/item/{id}', [LireAudioController::class, 'lireAudio']);

Route::get('/video/item/{id}', [LireVideoController::class, 'lireVideo']);

Route::get('/lecture/item/{id}', [LireLectureController::class, 'lireLecture']);
Auth::routes();

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');

Route::get('/home/ajout', [App\Http\Controllers\AjoutFichier::class, 'ajout'])->name('ajout');

Route::post('/home/enreg', [App\Http\Controllers\HomeController::class, 'enreg'])->name('enreg');