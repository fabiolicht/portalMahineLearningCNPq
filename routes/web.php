<?php

use App\Http\Controllers\EventController;
use App\Http\Controllers\ImageValidationController;
use App\Http\Controllers\PhotoController;
use App\Http\Controllers\ResultController;
use App\Http\Controllers\ResultControllerCelularCervical;
use App\Http\Controllers\ResultControllerCelularColon;
use App\Http\Controllers\ResultControllerCelularMama;
use App\Http\Controllers\ResultControllerCelularOral;
use App\Http\Controllers\ResultControllerCelularPulmao;
use App\Http\Controllers\ResultControllerCelularRim;
use App\Http\Controllers\ResultControllerCelularUtero;
use App\Http\Controllers\ResultControllerFotografiaPele;
use App\Http\Controllers\ResultControllerMamografia;
use App\Http\Controllers\ResultControllerTomografiaAbdomen;
use App\Http\Controllers\ResultControllerTomografiaCerebro;
use App\Http\Controllers\ResultControllerTomografiaFigado;
use App\Http\Controllers\ResultControllerUltrassomFigado;
use App\Http\Controllers\ResultControllerTomografiaRim;
use App\Http\Controllers\ResultControllerUltrassomMama;
use App\Http\Controllers\VideoController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('home');
});

Route::get('/home', function () {
    return view('home');
});

Route::get('/receberImagem', function () {
    return view('receberImagem');
});

Route::get('/contato', function () {
    return view('contato');
});

Route::get('/sobre', function () {
    return view('sobre');
});
Route::get('/erro', function () {
    return view('erro');
});

Route::get('/upload', [PhotoController::class, 'create']);
Route::post('/upload', [PhotoController::class, 'store']);

Route::get('/uploadV', [VideoController::class, 'create']);
Route::post('/uploadV', [VideoController::class, 'store']);

//Route::get('/classification/{resultado}', [ResultController::class, '__invoke'])->name('classification');
Route::get('/classificationUM/{resultado}/{path}', [ResultControllerUltrassomMama::class, '__invoke'])->name('classificationUM');
Route::post('/classificationUM/{resultado}/{path}', [ResultControllerUltrassomMama::class, '__invoke']);

Route::get('/classificationM/{resultado}/{path}', [ResultControllerMamografia::class, '__invoke'])->name('classificationM');
Route::post('/classificationM/{resultado}/{path}', [ResultControllerMamografia::class, '__invoke']);

Route::get('/classificationTF/{resultado}/{path}', [ResultControllerTomografiaFigado::class, '__invoke'])->name('classificationTF');
Route::post('/classificationTF/{resultado}/{path}', [ResultControllerTomografiaFigado::class, '__invoke']);

Route::get('/classificationUF/{resultado}/{path}', [ResultControllerUltrassomFigado::class, '__invoke'])->name('classificationUF');
Route::post('/classificationUF/{resultado}/{path}', [ResultControllerUltrassomFigado::class, '__invoke']);
//Route::get('/resultado', 'ResultController@resultado');


//Route::get('/classification', [ ResultController::class, 'classification' ]);
//Route::post('/classification', [ ResultController::class, 'classification' ]);

Route::get('/classificationCCv/{resultado}/{path}', [ResultControllerCelularCervical::class, '__invoke'])->name('classificationCCv');
Route::post('/classificationCCv/{resultado}/{path}', [ResultControllerCelularCervical::class, '__invoke']);

Route::get('/classificationCC/{resultado}/{path}', [ResultControllerCelularColon::class, '__invoke'])->name('classificationCC');
Route::post('/classificationCC/{resultado}/{path}', [ResultControllerCelularColon::class, '__invoke']);

Route::get('/classificationCM/{resultado}/{path}', [ResultControllerCelularMama::class, '__invoke'])->name('classificationCM');
Route::post('/classificationCM/{resultado}/{path}', [ResultControllerCelularMama::class, '__invoke']);

Route::get('/classificationCO/{resultado}/{path}', [ResultControllerCelularOral::class, '__invoke'])->name('classificationCO');
Route::post('/classificationCO/{resultado}/{path}', [ResultControllerCelularOral::class, '__invoke']);

Route::get('/classificationCP/{resultado}/{path}', [ResultControllerCelularPulmao::class, '__invoke'])->name('classificationCP');
Route::post('/classificationCP/{resultado}/{path}', [ResultControllerCelularPulmao::class, '__invoke']);

Route::get('/classificationCR/{resultado}/{path}', [ResultControllerCelularRim::class, '__invoke'])->name('classificationCR');
Route::post('/classificationCR/{resultado}/{path}', [ResultControllerCelularRim::class, '__invoke']);

Route::get('/classificationTR/{resultado}/{path}', [ResultControllerTomografiaRim::class, '__invoke'])->name('classificationTR');
Route::post('/classificationTR/{resultado}/{path}', [ResultControllerTomografiaRim::class, '__invoke']);

Route::get('/classificationCU/{resultado}/{path}', [ResultControllerCelularUtero::class, '__invoke'])->name('classificationCU');
Route::post('/classificationCU/{resultado}/{path}', [ResultControllerCelularUtero::class, '__invoke']);

Route::get('/classificationTA/{resultado}/{path}', [ResultControllerTomografiaAbdomen::class, '__invoke'])->name('classificationTA');
Route::post('/classificationTA/{resultado}/{path}', [ResultControllerTomografiaAbdomen::class, '__invoke']);

Route::get('/classificationTC/{resultado}/{path}', [ResultControllerTomografiaCerebro::class, '__invoke'])->name('classificationTC');
Route::post('/classificationTC/{resultado}/{path}', [ResultControllerTomografiaCerebro::class, '__invoke']);

//inclusão da rota para fotografia de pele
Route::get('/classificationFP/{resultado}/{path}', [ResultControllerFotografiaPele::class, '__invoke'])->name('classificationFP');
Route::post('/classificationFP/{resultado}/{path}', [ResultControllerFotografiaPele::class, '__invoke']);

Route::post('/validate-image', [ImageValidationController::class, 'validateImage']);



Route::get('/uploadImagem', function () {
    return view('uploadImagem');
})->name('uploadImagem');

Route::get('/uploadVideo', function () {
    return view('uploadVideo');
})->name('uploadVideo');