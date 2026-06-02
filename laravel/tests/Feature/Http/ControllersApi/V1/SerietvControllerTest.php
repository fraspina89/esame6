<?php

namespace Tests\Feature\Http\Controllers\Api\V1;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\Configurazione;
use App\Models\Contatto;
use App\Models\ContattoAbilita;
use App\Models\ContattoAuth;
use App\Models\ContattoRuolo;
use App\Models\ContattoSessione;
use App\Models\SerieTv;
use App\Helpers\AppHelper;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Gate;

class SerieTvControllerTest extends TestCase
{
    use RefreshDatabase;
    // ---PUBLIC---------------
   
    // /** @test */
    public function test_tutte_serieTv()
    {

        $this->impostaAmbiente();

        $contatto = $this->impostaContatto();
        $token = $this->impostaToken($contatto);
        // $contatto = Contatto::where('idContatto', $contatto->idContatto)->first();
        // $this->assertEquals($Token, $sessione->token);

        $serieModel = SerieTv::factory()->count(rand(1, 4))->create();
        // TESTO COME ADMIN
        $ruoli = Contatto::sincronizzaContattoRuoli($contatto->idContatto, 1);

        $response = $this->withHeader('Authorization', 'Bearer ' . $token) ->json('GET', $this->ritornaUrlSerie());
                        
        $response->assertStatus(200);
        $response->assertJsonStructure(['data' => $this->ritornaStrutturaJsonMultiplaSerie(1)]);
        $response->assertJson(['data' => $serieModel->toArray()]);

        // TESTO COME UTENTE
        $ruoli = Contatto::sincronizzaContattoRuoli($contatto->idContatto, 2);

        $tmpModel = $serieModel->map(
            function ($model) {
            $arr=$this->ritornaStrutturaJsonSingolaSerie(0);
            $dati = $model->only($arr); 
            $tmp = array();
foreach ($arr as $item) {
    if ($item == 'episodi') {
        $tmp[$item] = array();
    } else {
        $tmp[$item] = $dati[$item];
    }
}
return $tmp;
}
);
$response = $this->withHeader('Authorization', 'Bearer ' . $token)->json('GET', $this->ritornaUrlSerie());

$response->assertStatus(200);
$response->assertJsonStructure(['data' => $this->ritornaStrutturaJsonMultiplaSerie(0)]);

$response->assertJson(['data' => $tmpModel->toArray()]);

// TESTO COME OSPITE
$ruoli = Contatto::sincronizzaContattoRuoli($contatto->idContatto, 3);

$response = $this->json('GET', $this->ritornaUrlSerie());

$response->assertStatus(403);
}

//----------------------------
// /** @test */
public function test_tutte_serieTv_vuoto()
{
    $this->impostaAmbiente();
    $contatto = $this->impostaContatto();
    $token = $this->impostaToken($contatto);

    // TESTO COME ADMIN
    $ruoli = Contatto::sincronizzaContattoRuoli($contatto->idContatto, 1);

    $response = $this->withHeader('Authorization', 'Bearer ' . $token)->json('GET', $this->ritornaUrlSerie());

    $response->assertStatus(200);
    $response->assertJson(['data' => []]);

    //TESTO COME UTENTE
    $ruoli = Contatto::sincronizzaContattoRuoli($contatto->idContatto, 2);

    $response = $this->withHeader('Authorization', 'Bearer ' . $token)->json('GET', $this->ritornaUrlSerie());

    $response->assertStatus(200);
    $response->assertJson(['data' => []]);

    // TESTO COME OSPITE
    $ruoli = Contatto::sincronizzaContattoRuoli($contatto->idContatto, 3);

    $response = $this->json('GET', $this->ritornaUrlSerie());

    $response->assertStatus(403);
}

//----------------------------
/** @test */
public function test_creo_serieTv() 
{
    $this->impostaAmbiente();

    $contatto = $this->impostaContatto();
    $token = $this->impostaToken($contatto);

    // TESTO COME ADMIN
    $ruoli = Contatto::sincronizzaContattoRuoli($contatto->idContatto, 1);
    $arrKey = $this->ritornaStrutturaJsonSingolaSerie(1);
    $arrKey = SerieTvControllerTest::pulisciArray($arrKey);
    $requestData= SerieTv::factory()->make()->only($arrKey);

    $response = $this->withHeader('Authorization', 'Bearer ' . $token)->json('POST', $this->ritornaUrlSerie(), $requestData);

    $response->assertStatus(201);

    $id= $response['data']['idSerieTv'];
    $requestData['idSerieTv'] = $id;

    $response->assertJsonStructure(['data' => $arrKey]);
    $response->assertJson(['data' => $requestData]);

    //TESTO COME UTENTE
    $ruoli = Contatto::sincronizzaContattoRuoli($contatto->idContatto, 2);

    $arrKey = $this->ritornaStrutturaJsonSingolaSerie(0);
    $arrKey = SerieTvControllerTest::pulisciArray($arrKey);
    $requestData= SerieTv::factory()->make()->only($arrKey);
    $response = $this->withHeader('Authorization', 'Bearer ' . $token)->json('POST', $this->ritornaUrlSerie(), $requestData);

    $response->assertStatus(403);

    // TESTO COME OSPITE
    $ruoli = Contatto::sincronizzaContattoRuoli($contatto->idContatto, 3);

    $response = $this->json('POST', $this->ritornaUrlSerie(), $requestData);

    $response->assertStatus(403);
}

//----------------------------
/** @test */
public function test_leggo_singola_serieTv()
{
    $this->impostaAmbiente();

    $contatto = $this->impostaContatto();
    $token = $this->impostaToken($contatto);

    // TESTO COME ADMIN
    $ruoli = Contatto::sincronizzaContattoRuoli($contatto->idContatto, 1);
    $arrKey = $this->ritornaStrutturaJsonSingolaSerie(1);
    $arrKey = SerieTvControllerTest::pulisciArray($arrKey);
    $serieModel = SerieTv::factory()->create();

    $response = $this->withHeader('Authorization', 'Bearer ' . $token)->json('GET', $this->ritornaUrlSerie($serieModel->idSerieTv));

    $response->assertStatus(200);

    $$arrKey = $this->ritornaStrutturaJsonSingolaSerie(1);
    $response->assertJsonStructure(['data' => $arrKey]);
    $response->assertJson(['data' => $serieModel->toArray()]);

    //TESTO COME UTENTE
    $ruoli = Contatto::sincronizzaContattoRuoli($contatto->idContatto, 2);

    $arrKey = $this->ritornaStrutturaJsonSingolaSerie(0);
    $arrKey = SerieTvControllerTest::pulisciArray($arrKey);
    $serieModel = SerieTv::factory()->create();

    $response = $this->withHeader('Authorization', 'Bearer ' . $token)->json('GET', $this->ritornaUrlSerie($serieModel->idSerieTv));

    $response->assertStatus(200);

    $serieModel = $serieModel->only($arrKey);
    $serieModel['episodi'] = array();

    $response->assertJsonStructure(['data' => $arrKey]);
    $response->assertJson(['data' => $serieModel]);

    // TESTO COME OSPITE
    $ruoli = Contatto::sincronizzaContattoRuoli($contatto->idContatto, 3);
    $arrKey = $this->ritornaStrutturaJsonSingolaSerie(0);
    $arrKey = SerieTvControllerTest::pulisciArray($arrKey);
    $serieModel = SerieTv::factory()->create();
    $response = $this->withHeader('Authorization', 'Bearer ' . $token)->json('GET', $this->ritornaUrlSerie($serieModel->idSerieTv));

    $response->assertStatus(403);
}

//----------------------------
/** @test */
public function test_leggo_singola_serieTv_vuota()
{
    $this->impostaAmbiente();

    $contatto = $this->impostaContatto();
    $token = $this->impostaToken($contatto);

    // TESTO COME ADMIN
    $ruoli = Contatto::sincronizzaContattoRuoli($contatto->idContatto, 1);
    $id = rand(1, 10);

    $response = $this->withHeader('Authorization', 'Bearer ' . $token)->json('GET', $this->ritornaUrlSerie($id));

    $response->assertStatus(404);

    //TESTO COME UTENTE
    $ruoli = Contatto::sincronizzaContattoRuoli($contatto->idContatto, 2);
    $id = rand(1, 10);

    $response = $this->withHeader('Authorization', 'Bearer ' . $token)->json('GET', $this->ritornaUrlSerie($id));

    $response->assertStatus(404);

    // TESTO COME OSPITE
   $ruoli = Contatto::sincronizzaContattoRuoli($contatto->idContatto, 3);
    $id = rand(1, 10);

    $response = $this->withHeader('Authorization', 'Bearer ' . $token)->json('GET', $this->ritornaUrlSerie($id));

    $response->assertStatus(403);
}

//----------------------------
/** @test */
public function test_aggiorno_singola_serieTv()
{
    $this->impostaAmbiente();

    $contatto = $this->impostaContatto();
    $token = $this->impostaToken($contatto);

    // TESTO COME ADMIN
    $ruoli = Contatto::sincronizzaContattoRuoli($contatto->idContatto, 1);
    $arrKey = $this->ritornaStrutturaJsonSingolaSerie(1);
    $arrKey = SerieTvControllerTest::pulisciArray($arrKey);
    $serieModel = SerieTv::factory()->create();
    $requestData= SerieTv::factory()->make()->only($arrKey);

    $response = $this->withHeader('Authorization', 'Bearer ' . $token)->json('PUT', $this->ritornaUrlSerie($serieModel->idSerieTv), $requestData);

    $response->assertStatus(200);
    //controllo che il nuovo valore sia stato aggiornato
    $ritorno= array('nome' => $requestData['nome']);
    $response->assertJson(['data' => $ritorno]);
    // verifico che il nuovo valore sia presente nel db
    $serieModel->refresh();
    $ritorno= array('nome' => $serieModel['nome']);
    $response->assertJson(['data' => $ritorno]);

    //TESTO COME UTENTE
    $ruoli = Contatto::sincronizzaContattoRuoli($contatto->idContatto, 2);
    $arrKey = $this->ritornaStrutturaJsonSingolaSerie(0);
    $arrKey = SerieTvControllerTest::pulisciArray($arrKey);
    $serieModel = SerieTv::factory()->create();
    $requestData= SerieTv::factory()->make()->only($arrKey);

    $response = $this->withHeader('Authorization', 'Bearer ' . $token)->json('PUT', $this->ritornaUrlSerie($serieModel->idSerieTv), $requestData);

    $response->assertStatus(403);

    // TESTO COME OSPITE
    $ruoli = Contatto::sincronizzaContattoRuoli($contatto->idContatto, 3);
    $arrKey = $this->ritornaStrutturaJsonSingolaSerie(0);
    $arrKey = SerieTvControllerTest::pulisciArray($arrKey);
    $serieModel = SerieTv::factory()->create();
    $requestData= SerieTv::factory()->make()->only($arrKey);

    $response = $this->json('PUT', $this->ritornaUrlSerie($serieModel->idSerieTv), $requestData);

    $response->assertStatus(403);
}

//----------------------------
/** @test */
public function test_aggiorno_singola_serieTv_vuoto()
{
$this->impostaAmbiente();

$contatto = $this->impostaContatto();
$token = $this->impostaToken($contatto);

// TESTO COME ADMIN
$ruoli = Contatto::sincronizzaContattoRuoli($contatto->idContatto, 1);
$id = rand(1, 10);

$response = $this->withHeader('Authorization', 'Bearer ' . $token)->json('PUT', $this->ritornaUrlSerie($id));

$response->assertStatus(404);


//TESTO COME UTENTE
$ruoli = Contatto::sincronizzaContattoRuoli($contatto->idContatto, 2);
$id = rand(1, 10);

$response = $this->withHeader('Authorization', 'Bearer ' . $token)->json('PUT', $this->ritornaUrlSerie($id));

$response->assertStatus(403);

// TESTO COME OSPITE
$ruoli = Contatto::sincronizzaContattoRuoli($contatto->idContatto, 3);
$id = rand(1, 10);

$response = $this->json('PUT', $this->ritornaUrlSerie($id));

$response->assertStatus(403);
}

//----------------------------
/** @test */
public function test_cancello_singola_serieTv()
{
  $this->impostaAmbiente();

  $contatto = $this->impostaContatto();
    $token = $this->impostaToken($contatto);

// TESTO COME ADMIN
$ruoli = Contatto::sincronizzaContattoRuoli($contatto->idContatto, 1);
$serieModel = SerieTv::factory()->create();

$response = $this->withHeader('Authorization', 'Bearer ' . $token)->json('DELETE', $this->ritornaUrlSerie($serieModel->idSerieTv));

$response->assertStatus(204);

//TESTO COME UTENTE
$ruoli = Contatto::sincronizzaContattoRuoli($contatto->idContatto, 2);
$serieModel = SerieTv::factory()->create();

$response = $this->withHeader('Authorization', 'Bearer ' . $token)->json('DELETE', $this->ritornaUrlSerie($serieModel->idSerieTv));

$response->assertStatus(403);

//TESTO COME OSPITE
$ruoli = Contatto::sincronizzaContattoRuoli($contatto->idContatto, 3);
$serieModel = SerieTv::factory()->create();

$response = $this->json('DELETE', $this->ritornaUrlSerie($serieModel->idSerieTv));

$response->assertStatus(403);
}
//----------------------------
/** @test */
public function test_cancello_singola_serieTv_vuoto()
{
    $this->impostaAmbiente();

    $contatto = $this->impostaContatto();
    $token = $this->impostaToken($contatto);

    // TESTO COME ADMIN
    $ruoli = Contatto::sincronizzaContattoRuoli($contatto->idContatto, 1);
    $id = rand(1, 10);

    $response = $this->withHeader('Authorization', 'Bearer ' . $token)->json('DELETE', $this->ritornaUrlSerie($id));

    $response->assertStatus(404);


    //TESTO COME UTENTE
    $ruoli = Contatto::sincronizzaContattoRuoli($contatto->idContatto, 2);
    $id = rand(1, 10);

    $response = $this->withHeader('Authorization', 'Bearer ' . $token)->json('DELETE', $this->ritornaUrlSerie($id));

    $response->assertStatus(403);

    // TESTO COME OSPITE
    $ruoli = Contatto::sincronizzaContattoRuoli($contatto->idContatto, 3);
    $id = rand(1, 10);

    $response = $this->json('DELETE', $this->ritornaUrlSerie($id));

    $response->assertStatus(403);
}
//----------------------------PROTECTED
protected function impostaAmbiente() {

    $this->impostaConfigurazioni();
    $n = Configurazione::all()->count();
    $this->assertEquals($n, 4);

    $this->impostaDBAbilita();
    $n = ContattoAbilita::all()->count();
    $this->assertEquals($n, 4);

    $this->impostaDBRuoli();
    $n = ContattoRuolo::all()->count();
    $this->assertEquals($n, 3);

    $this->impostaDBRuoloAbilita();
    $this->impostaGate();
}
//----------------------------
protected function impostaConfigurazioni()
{
    Configurazione::create(['idConfigurazione' => 1, 'chiave' => 'maxLoginErrati', 'valore' => '5']);
    Configurazione::create(['idConfigurazione' => 2, 'chiave' => 'durataSfida', 'valore' => '30']);
    Configurazione::create(['idConfigurazione' => 3, 'chiave' => 'durataSessione', 'valore' => '300']);
    Configurazione::create(['idConfigurazione' => 4, 'chiave' => 'storicoPsw', 'valore' => '3']);
}
//-------------
protected function impostaContatto()
{
    $utente = hash("sha512", trim("Utente"));
    $sfida  = hash("sha512", trim("Sfida"));
    $secret = trim(Str::random(20));

    $contatto = Contatto::factory()->create();
    $contatto->idContattoStato = 1;
    $contatto->archiviato = 0;
    $contatto->save();

    $auth = new ContattoAuth();
    $auth->idContatto = $contatto->idContatto;
    $auth->secretJWT = $secret;
    $auth->user = $utente;
    $auth->sfida= $sfida;
    $auth->inizioSfida = time();
    $auth->save();
    return $contatto; 
}
//-------------
protected function impostaDBAbilita()
{
    $arr =['Leggere', 'Creare', 'Aggiornare', 'Eliminare'];
    foreach ($arr as $item) {
         ContattoAbilita::create([
            'nome' => $item,
            'sku' => strtolower($item) 
        ]);
    }
}
//-------------
protected function impostaDBRuolo()
{
    $arr =['Amministratore', 'Utente', 'Ospite'];
    foreach ($arr as $item) {
         ContattoRuolo::create([
            'nome' => $item,
            'deleted_at' => null
        ]);
    }
}
//------------------
 protected function impostaDBRuoloAbilita()
{
 $idRuolo= 1;
$arrAbilita = [1, 2, 3, 4];
ContattoRuolo::sincronizzaRuoloAbilita($idRuolo, $arrAbilita);
$idRuolo= 2;
$arrAbilita = [1];
ContattoRuolo::sincronizzaRuoloAbilita($idRuolo, $arrAbilita);
}
//-----
protected function impostaGate()
{
ContattoRuolo::all()->each(
   function (ContattoRuolo $ruolo) {
    Gate::define($ruolo->nome, function (Contatto $contatto) use ($ruolo) {
           // echo ($ruolo . '-');
        return $contatto->ruoli->contains('nome', $ruolo->nome);
    });
}
);
 // gate basati su multipli ruoli
ContattoAbilita::all()->each(function (ContattoAbilita $abilita) {
    // echo ($abilita . '-');
    Gate::define($abilita->sku, function (Contatto $contatto) use ($abilita) {
        $check = false;
        foreach ($contatto->ruoli as $item) {
            if ($item->abilita->contains('sku', $abilita->sku)) {
               $check = true;
               break;
            }
        }
        return $check;
});
});
}
//-------------
protected function impostaToken($contatto)
{
    $sessione = ContattoSessione::factory()->create()->first();
    $sessione->idContatto = $contatto->idContatto;
    $auth = ContattoAuth::where('idContatto', $contatto->idContatto)->first();
   $token = AppHelper::creaTokenSessione($contatto->idContatto,$auth->secretJWT);
   $sessione->token = $token;
   $sessione->save();
   $sessione = ContattoSessione::where('idContatto', $contatto->idContatto)->first();
   $this->assertEquals($token, $sessione->token);
    return $token;
}

protected static function pulisciArray($arrKey)
{
    $key = array_search('episodi', $arrKey);
    if ($key !== false) array_splice($arrKey, $key, 1);
    $key = array_search('deleted_at', $arrKey);
    if ($key !== false) array_splice($arrKey, $key, 1);
    $key = array_search('created_at', $arrKey);
    if ($key !== false) array_splice($arrKey, $key, 1);
    $key = array_search('updated_at', $arrKey);
    if ($key !== false) array_splice($arrKey, $key, 1);
    return $arrKey;   
    }
//-----------
protected function ritornaStrutturaJsonMultiplaSerie($admin = 0)
{

    return ['*' => $this->ritornaStrutturaJsonSingolaSerie($admin)];
}
//-----------
protected function ritornaStrutturaJsonSingolaSerie($admin = 0)
{
    if ($admin == 1) {
        $arr = [ 'idSerieTv', 'idCategoria', 'nome', 'descrizione', 'regista', 'numeroStagioni', 'annoInizio', 'annoFine', 'created_by', 'updated_by'];
    } else {
        $arr = [ 'idSerieTv', 'idCategoria', 'nome', 'descrizione', 'regista', 'numeroStagioni', 'annoInizio', 'annoFine', 'episodi'];
    }

    return $arr;
}

protected function ritornaUrlSerie($id = null)
{
    $url = '/api/v1/serieTV';
    if ($id !== null) {
        $url .= '/' . $id;
    }
    return $url;
}

protected function impostaDBRuoli()
{
    // Questo metodo può essere implementato se necessario per i test
    // Attualmente lasciato vuoto in quanto non essenziale per la compilazione
}
}















           