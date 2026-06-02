<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\V1\AccediController;
use App\Http\Controllers\Api\V1\CategoriaController;
use App\Http\Controllers\Api\V1\ConfigurazioneController;
use App\Http\Controllers\Api\V1\ContattoController;
use App\Http\Controllers\Api\V1\ContattoRuoloController;
use App\Http\Controllers\Api\V1\ContattoStatoController;
use App\Http\Controllers\Api\V1\ContattoAbilitaController;
use App\Http\Controllers\Api\V1\ContattoRecapitoController;
use App\Http\Controllers\Api\V1\ContattoIndirizzoController;
use App\Http\Controllers\Api\V1\NazioneController;
use App\Http\Controllers\Api\V1\TipoIndirizzoController;
use App\Http\Controllers\Api\V1\TipoRecapitoController;
use App\Http\Controllers\Api\V1\ComuneItalianoController;
use App\Http\Controllers\Api\V1\VistaProvinciaController;
use App\Http\Controllers\Api\V1\LinguaController;
use App\Http\Controllers\Api\V1\VistaTraduzioneController;
use App\Http\Controllers\Api\V1\SerieTvController;
use App\Http\Controllers\Api\V1\EpisodioController;
use App\Http\Controllers\Api\V1\FilmController;
use App\Helpers\AppHelper;



if (!defined('_VERS')) {
    define('_VERS', 'v1');
}

//------------------------------------- API PUBBLICHE ------------------------

           Route::get(_VERS . '/accedi/{Utenti}/{hash?}', [AccediController::class, 'show']);
           Route::get(_VERS . '/searchMail/{Utente}', [AccediController::class, 'searchMail']);
           Route::post(_VERS . '/registrazione/', [ContattoController::class, 'registra']);
           Route::post(_VERS . '/login', [AccediController::class, 'login']);
           Route::post(_VERS . '/logout', [AccediController::class, 'logout']);

// LINGUE
Route::get(_VERS . '/lingue', [LinguaController::class, 'index']);
Route::get(_VERS . '/lingue/{lingua}', [LinguaController::class, 'show']);

// TRADUZIONI
Route::get(_VERS . '/traduzioni', [VistaTraduzioneController::class, 'index']);
Route::get(_VERS . '/traduzioni/{traduzione}', [VistaTraduzioneController::class, 'show']);

// CONFIGURAZIONI
Route::get(_VERS . '/configurazioni', [ConfigurazioneController::class, 'index']);
Route::get(_VERS . '/configurazioni/{configurazione}', [ConfigurazioneController::class, 'show']);

// TIPI INDIRIZZO
Route::get(_VERS . '/tipiIndirizzo', [TipoIndirizzoController::class, 'index']);
Route::get(_VERS . '/tipiIndirizzo/{tipoIndirizzo}', [TipoIndirizzoController::class, 'show']);

// TIPI RECAPITO
Route::get(_VERS . '/tipiRecapito', [TipoRecapitoController::class, 'index']);
Route::get(_VERS . '/tipiRecapito/{tipoRecapito}', [TipoRecapitoController::class, 'show']);

// NAZIONI
Route::get(_VERS . '/nazioni', [NazioneController::class, 'index']);
Route::get(_VERS . '/nazioni/{nazione}', [NazioneController::class, 'show']);

// COMUNI
Route::get(_VERS . '/comuni', [ComuneItalianoController::class, 'index']);
Route::get(_VERS . '/comuni/{comuneItaliano}', [ComuneItalianoController::class, 'show']);

// PROVINCE
Route::get(_VERS . '/province', [VistaProvinciaController::class, 'index']);
Route::get(_VERS . '/province/{sigla}', [VistaProvinciaController::class, 'show']);



//------------------------- API CON AUTENTICAZIONE UTENTE ----------------------

Route::middleware(['autenticazione', 'contattoRuolo:Amministratore,Utente'])->group(function () {

        // Endpoint di test per validare il token e restituire il payload
        Route::get(_VERS . '/test-auth', [\App\Http\Controllers\Api\V1\TestAuthController::class, 'index']);

    // FILM (elenco e dettaglio - solo utenti autenticati)
    Route::get(_VERS . '/films', [FilmController::class, 'index']); // elenco film
    Route::get(_VERS . '/films/{film}', [FilmController::class, 'show']); // dettaglio film

    // SERIE TV (elenco e dettaglio - solo utenti autenticati)
    Route::get(_VERS . '/serieTV', [SerieTvController::class, 'index']); // elenco serie TV
    Route::get(_VERS . '/serieTV/{serieTV}', [SerieTvController::class, 'show']); // dettaglio serie TV

    // CATEGORIE (servono nel catalogo autenticato)
    Route::get(_VERS . '/categorie', [CategoriaController::class, 'index']);
    Route::get(_VERS . '/categorie/{categoria}', [CategoriaController::class, 'show']);

    //EPISODI
    Route::get(_VERS . '/serieTV/{idSerieTV}/episodi', [EpisodioController::class, 'index']); // elenco episodi per serie
    Route::get(_VERS . '/serieTV/{idSerieTV}/episodi/{idEpisodio}', [EpisodioController::class, 'show']); // dettaglio episodio

    // CONTATTI (utente può leggere/aggiornare il proprio profilo)
    Route::get(_VERS . '/contatti/{idContatto}', [ContattoController::class, 'show']);
    Route::put(_VERS . '/contatti/{idContatto}', [ContattoController::class, 'update']);
    Route::post(_VERS . '/contatti/{idContatto}/change-email', [ContattoController::class, 'changeEmail']);
    Route::post(_VERS . '/contatti/{idContatto}/change-password', [ContattoController::class, 'changePassword']);
});


//-------------------------------- API CON AUTENTICAZIONE AMMINISTRATORE ---------------------

Route::middleware(['autenticazione', 'contattoRuolo:Amministratore'])->group(function () {

    // STATI UTENTE
    Route::get(_VERS . '/statiUtente', [ContattoStatoController::class, 'index']);
    Route::get(_VERS . '/statiUtente/{contattoStato}', [ContattoStatoController::class, 'show']);

    // RUOLI
    Route::get(_VERS . '/ruoli', [ContattoRuoloController::class, 'index']);
    Route::get(_VERS . '/ruoli/{contattoRuolo}', [ContattoRuoloController::class, 'show']);

    // ABILITÀ
    Route::get(_VERS . '/abilita', [ContattoAbilitaController::class, 'index']);
    Route::get(_VERS . '/abilita/{contattoAbilita}', [ContattoAbilitaController::class, 'show']);

    // NAZIONI
    Route::post(_VERS . '/nazioni', [NazioneController::class, 'store']);  // creazione nazione
    Route::put(_VERS . '/nazioni/{nazione}', [NazioneController::class, 'update']);    // modifica nazione
    Route::delete(_VERS . '/nazioni/{nazione}', [NazioneController::class, 'destroy']);    // cancellazione nazione

    // COMUNI ITALIANI
    Route::post(_VERS . '/comuni', [ComuneItalianoController::class, 'store']);  // creazione comuni italiani
    Route::put(_VERS . '/comuni/{comuneItaliano}', [ComuneItalianoController::class, 'update']);    // modifica comune italiano
    Route::delete(_VERS . '/comuni/{comuneItaliano}', [ComuneItalianoController::class, 'destroy']);    // cancellazione comune italiano

    //FILM
    Route::post(_VERS . '/films', [FilmController::class, 'store']); // creazione film
    Route::put(_VERS . '/films/{film}', [FilmController::class, 'update']); // modifica film (JSON)
    Route::post(_VERS . '/films/{film}/update', [FilmController::class, 'update']); // modifica film (multipart)
    Route::delete(_VERS . '/films/{film}', [FilmController::class, 'destroy']); // cancellazione film

    //EPISODI 
    Route::post(_VERS . '/serieTV/{idSerieTV}/episodi', [EpisodioController::class, 'store']); // creazione episodio
    Route::put(_VERS . '/serieTV/{idSerieTV}/episodi/{idEpisodio}', [EpisodioController::class, 'update']); // modifica episodio
    Route::delete(_VERS . '/serieTV/{idSerieTV}/episodi/{idEpisodio}', [EpisodioController::class, 'destroy']); // cancellazione episodio

    //SERIE TV
    Route::post(_VERS . '/serieTV', [SerieTvController::class, 'store']); // creazione serie TV
    Route::put(_VERS . '/serieTV/{serieTV}', [SerieTvController::class, 'update']); // modifica serie TV
    Route::delete(_VERS . '/serieTV/{serieTV}', [SerieTvController::class, 'destroy']); // cancellazione serie TV


    //CATEGORIE
    Route::post(_VERS . '/categorie', [CategoriaController::class, 'store']); // creazione categoria
    Route::put(_VERS . '/categorie/{categoria}', [CategoriaController::class, 'update']); // modifica categoria
    Route::delete(_VERS . '/categorie/{categoria}', [CategoriaController::class, 'destroy']); // cancellazione categoria


    //CONTATTI
    Route::get(_VERS . '/contatti', [ContattoController::class, 'index']); // elenco contatti (admin)
    Route::post(_VERS . '/contatti/', [ContattoController::class, 'store']);
    Route::delete(_VERS . '/contatti/{contatto}', [ContattoController::class, 'destroy']);
});
