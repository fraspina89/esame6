<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Contatto;
use App\Models\ContattoCredito;
use App\Http\Resources\Api\V1\ContattoResource;
use App\Http\Resources\Api\V1\ContattoCollection;
use App\Http\Requests\V1\ContattoStoreRequest;
use App\Http\Requests\V1\ContattoUpdateRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use App\Models\ContattoAuth;
use App\Models\ContattoRuolo;
use App\Models\ContattoPassword;
use App\Models\ContattoPasswordHistory;
use App\Models\Configurazione;
use Illuminate\Support\Facades\DB;
use App\Helpers\AppHelper;
use App\Models\ContattoSessione;
use Illuminate\Support\Facades\Auth;
use App\Models\ContattoIndirizzo;
use App\Models\ContattoRecapito;


/**
 * Controller per la gestione dei contatti
 *
 * Contiene azioni per la creazione, aggiornamento, cancellazione,
 * e operazioni di autenticazione correlate ai contatti.
 */
class ContattoController extends Controller
{
    /**
     * Restituisce l'elenco dei contatti.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $query = Contatto::query();

        // Filtri opzionali
        if ($request->has('nome')) {
            $query->where('nome', 'LIKE', '%' . $request->input('nome') . '%');
        }

        if ($request->has('cognome')) {
            $query->where('cognome', 'LIKE', '%' . $request->input('cognome') . '%');
        }

        if ($request->has('idContattoStato')) {
            $query->where('idContattoStato', $request->input('idContattoStato'));
        }

        if ($request->has('archiviato')) {
            $query->where('archiviato', $request->input('archiviato'));
        }

        // Include relazioni se richieste
        if ($request->has('include')) {
            $includes = explode(',', (string)$request->input('include'));
            $availableIncludes = ['recapiti', 'indirizzi', 'crediti', 'ruoli'];
            $validIncludes = array_intersect($includes, $availableIncludes);
            
            if (!empty($validIncludes)) {
                $query->with($validIncludes);
            }
        }

        // Paginazione
        $perPage = $request->get('per_page', 15);
        $contatti = $query->paginate($perPage);

        return new ContattoCollection($contatti);
    }

    /**
     * Non utilizzato (endpoint API).
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Registrazione pubblica di un nuovo contatto (signup)
     *
     * @param Request $request
     * @return \Illuminate\Http\Response
     */
    public function registra(Request $request)
    {
        $data = $request->validate([
            'nome' => 'nullable|string|max:255',
            'cognome' => 'nullable|string|max:255',
            'user' => 'required|email|max:255',
            'password' => 'required|string|min:6',
            'ruolo' => 'nullable|string',
            'sesso' => 'nullable|integer|between:0,255',
            'cittaNascita' => 'nullable|string|max:45',
            'codiceFiscale' => 'nullable|string|max:20',
            'partitaIva' => 'nullable|string|max:20',
            'cittadinanza' => 'nullable|string|max:45',
            'idNazioneNascita' => 'nullable|integer',
            'provinciaNascita' => 'nullable|string|max:45',
            'dataNascita' => 'nullable|date',
            'archiviato' => 'nullable|integer|between:0,255',
            'telefono' => 'nullable|string|max:30',
            'via' => 'nullable|string|max:255',
            'civico' => 'nullable|string|max:20',
            'cittaResidenza' => 'nullable|string|max:100',
            'capResidenza' => 'nullable|string|max:10'
        ]);

        // se l'email è già presente in contattiAuth, evitamo duplicate key
        if (isset($data['user']) && ContattoAuth::where('user', $data['user'])->exists()) {
            return response()->json([
                'message' => 'User già registrato',
                'error' => 'duplicate_user'
            ], 409);
        }

        // Uso una transazione DB per assicurare coerenza tra creazione
        // contatto, assegnazione ruoli e creazione delle credenziali.
        return DB::transaction(function () use ($data, $request) {
            $contattoData = [
                'idContattoStato' => 1,
                'nome' => $data['nome'] ?? null,
                'cognome' => $data['cognome'] ?? null,
                'sesso' => $data['sesso'] ?? null,
                'cittaNascita' => $data['cittaNascita'] ?? null,
                'archiviato' => 0,
                'created_by' => 1,
                'updated_by' => 1
            ];

            // Permettiamo al client di inviare i campi presenti in $fillable del modello
            // ma blocchiamo quelli sensibili (created_by, updated_by, idContattoStato)
            $model = new Contatto();
            $fillable = $model->getFillable();
            $blocked = ['created_by', 'updated_by', 'idContattoStato'];
            $allowed = array_values(array_diff($fillable, $blocked));

            $defaults = [
                'idContattoStato' => 1,
                'archiviato' => 0,
                'created_by' => 1,
                'updated_by' => 1
            ];

            $input = $request->only($allowed);
            $contattoData = array_merge($defaults, $input);

            // Creo il record principale nella tabella `contatti`
            $contatto = Contatto::create($contattoData);

            // assegno il ruolo specificato (se valido) altrimenti Utente
            $ruoloName = $data['ruolo'] ?? null;
            $ruoloObj = null;
            if ($ruoloName) {
                $ruoloObj = ContattoRuolo::where('nome', $ruoloName)->first();
            }

            // Assegno il ruolo specificato (se valido). Se non è passato
            // o non esiste, assegno il ruolo di default 'Utente' se presente
            if ($ruoloObj) {
                $contatto->ruoli()->attach($ruoloObj->idContattoRuolo);
            } else {
                // default: Utente (se presente)
                $utenteRuolo = ContattoRuolo::where('nome', 'Utente')->first();
                if ($utenteRuolo) {
                    $contatto->ruoli()->attach($utenteRuolo->idContattoRuolo);
                    $ruoloObj = $utenteRuolo;
                }
            }

            // create auth and password records
            $contatto->load('ruoli');
            $isOspite = ($contatto->ruoli->first() && strtolower($contatto->ruoli->first()->nome) === 'ospite');

            $secret = $isOspite ? null : hash('sha512', trim(Str::random(200)));
            $auth = ContattoAuth::create([
                'idContatto' => $contatto->idContatto,
                'user' => hash('sha512', trim($data['user'])),
                'secretJWT' => $secret,
                'inizioSfida' => now(),
                'obbligoCambio' => 0
            ]);

            // Memorizzo la password (SHA512) e genero un valore `sale` casuale
            // per eventuali verifiche o compatibilità con flussi esistenti.
            $sale = Str::random(16);
            ContattoPassword::create([
                'idContatto' => $contatto->idContatto,
                'psw' => hash('sha512', $data['password']),
                'sale' => $sale
            ]);

            // Salva il numero di telefono nella tabella recapiti (se fornito)
            if ($request->filled('telefono')) {
                ContattoRecapito::create([
                    'idContatto' => $contatto->idContatto,
                    'idTipoRecapito' => 3, // Cellulare
                    'valore' => $request->input('telefono'),
                    'preferito' => true
                ]);
            }

            // Salva l'indirizzo di residenza nella tabella indirizzi (se fornito)
            $via = $request->input('via');
            $cittaRes = $request->input('cittaResidenza');
            $capRes = $request->input('capResidenza');
            if ($via || $cittaRes || $capRes) {
                // Recupera l'idNazione di default (Italia) per soddisfare il vincolo NOT NULL
                $idNazioneDefault = \App\Models\Nazione::where('iso', 'IT')->value('idNazione') ?? 1;
                ContattoIndirizzo::create([
                    'idContatto' => $contatto->idContatto,
                    'idTipologiaIndirizzo' => 1, // Residenza
                    'idNazione' => $idNazioneDefault,
                    'indirizzo' => $via,
                    'civico' => $request->input('civico'),
                    'comune' => $cittaRes,
                    'cap' => $capRes
                ]);
            }

            // Non creare automaticamente token/sessione alla registrazione.
            // L'utente dovrà eseguire il flusso di login (/accedi) per ottenere il JWT.
            $responseData = new ContattoResource($contatto->fresh());
            return response()->json([
                'message' => 'Registrazione completata',
                'data' => $responseData
            ], 201);
        });
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  ContattoStoreRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(ContattoStoreRequest $request)
    {
        $validatedData = $request->validated();

        // Non ci sono più password da hashare secondo la nuova struttura
        $contatto = Contatto::create($validatedData);

        // assegna ruolo se passato, altrimenti assegna il ruolo di default 'Utente' se presente
        if (isset($validatedData['idContattoRuolo'])) {
            $contatto->ruoli()->attach((int)$validatedData['idContattoRuolo']);
        } else {
            $utenteRuolo = ContattoRuolo::where('nome', 'Utente')->first();
            if ($utenteRuolo) {
                $contatto->ruoli()->attach($utenteRuolo->idContattoRuolo);
            }
        }

        $contatto->load('ruoli');

        return response()->json([
            'message' => 'Contatto creato con successo',
            'data' => new ContattoResource($contatto)
        ], 201);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request, $id)
    {
        $query = Contatto::query();

        // Include relazioni se richieste
        if ($request->has('include')) {
            $includes = explode(',', (string)$request->input('include'));
            $availableIncludes = ['recapiti', 'indirizzi', 'crediti', 'ruoli'];
            $validIncludes = array_intersect($includes, $availableIncludes);
            
            if (!empty($validIncludes)) {
                $query->with($validIncludes);
            }
        }

        $contatto = $query->findOrFail($id);

        return new ContattoResource($contatto);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  ContattoUpdateRequest  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(ContattoUpdateRequest $request, $id)
    {
        $contatto = Contatto::findOrFail($id);
        $roles = $request->attributes->get('contattiRuoli', []);
        $isAdmin = in_array('Amministratore', $roles);
        // owner or admin required
        if (! $isAdmin && Auth::id() !== $contatto->idContatto) {
            abort(403, 'PE_0007');
        }
        $validatedData = $request->validated();

        // Non ci sono più password da hashare secondo la nuova struttura
        $contatto->update($validatedData);



        return response()->json([
            'message' => 'Contatto aggiornato con successo',
            'data' => new ContattoResource($contatto->fresh())
        ]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $contatto = Contatto::findOrFail($id);
        $roles = request()->attributes->get('contattiRuoli', []);
        $isAdmin = in_array('Amministratore', $roles);
        if (! $isAdmin && Auth::id() !== $contatto->idContatto) {
            abort(403, 'PE_0008');
        }
        $contatto->delete(); // Utilizzerà SoftDeletes se configurato nel model

        return response()->json([
            'message' => 'Contatto eliminato con successo'
        ], 200);
    }

    /**
     * Update the credit for a specific contact.
     *
     * @param  int  $idContatto
     * @param  float  $valore
     * @return \Illuminate\Http\Response
     */
    public function updateCredito($idContatto, $valore)
    {
        // Valida che il valore sia numerico
        if (!is_numeric($valore)) {
            return response()->json([
                'success' => false,
                'message' => 'Il valore del credito deve essere numerico'
            ], 400);
        }

        $contatto = Contatto::findOrFail($idContatto);
        $roles = request()->attributes->get('contattiRuoli', []);
        $isAdmin = in_array('Amministratore', $roles);
        if (! $isAdmin && \Illuminate\Support\Facades\Auth::id() !== (int)$idContatto) {
            return response()->json([
                'success' => false,
                'message' => 'Non autorizzato'
            ], 403);
        }
        
        // Verifica che il contatto esista
        if (!$contatto) {
            return response()->json([
                'success' => false,
                'message' => 'Contatto non trovato'
            ], 404);
        }

        // Cerca o crea il record credito
        $credito = $contatto->crediti;
        
        if (!$credito) {
            // Crea un nuovo record credito se non esiste
            $credito = new \App\Models\ContattoCredito([
                'idContatto' => $idContatto,
                'saldo' => $valore,
                'limite' => 0.00,
                'attivo' => true
            ]);
            $credito->save();
        } else {
            // Aggiorna il saldo esistente
            $credito->impostaSaldo($valore);
        }

        // Carica la relazione per la risposta
        $credito->load('contatto');

        return response()->json([
            'success' => true,
            'data' => [
                'idCredito' => $credito->idCredito,
                'idContatto' => $credito->idContatto,
                'saldoPrecedente' => $credito->getOriginal('saldo'),
                'saldoNuovo' => $credito->saldo,
                'saldoFormattato' => $credito->saldo_formattato,
                'limite' => $credito->limite,
                'limiteFormattato' => $credito->limite_formattato,
                'disponibilitaTotale' => $credito->disponibilita_totale,
                'attivo' => $credito->attivo,
                'isInDebito' => $credito->isInDebito(),
                'hasSaldoPositivo' => $credito->hasSaldoPositivo(),
            ],
            'message' => 'Credito aggiornato con successo'
        ], 200);
    }

    /**
     * Change password for contact (owner or admin).
     *
     * @param Request $request
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function changePassword(Request $request, $id)
    {
        $roles = $request->attributes->get('contattiRuoli', []);
        $isAdmin = in_array('Amministratore', $roles);
        if (! $isAdmin && Auth::id() !== (int)$id) {
            abort(403, 'PE_0007');
        }

        // Validazione: per il cambio password è richiesta la password corrente
        // e la nuova password (min 6 caratteri). Gli amministratori possono
        // comunque eseguire questa rotta previa autorizzazione (controllo ruoli).
        $data = $request->validate([
            'current_password' => 'required|string',
            'new_password' => 'required|string|min:6'
        ]);

        $current = ContattoPassword::where('idContatto', $id)->orderBy('idContattoPassword', 'desc')->firstOrFail();

        // Ora `psw` è lo SHA512 della password in chiaro. Controllo che la
        // `current_password` fornita corrisponda all'hash salvato; in caso
        // contrario ritorno l'errore `ERR_CURRENT_PASSWORD`.
        if (hash('sha512', $data['current_password']) !== $current->psw) {
            abort(403, 'ERR_CURRENT_PASSWORD');
        }

        $N = (int)(Configurazione::leggiValore('password_non_riuso_n') ?? 5);
        $history = ContattoPasswordHistory::where('idContatto', $id)->orderBy('created_at', 'desc')->limit($N)->get();

        $checks = $history->pluck('psw')->toArray();
        array_unshift($checks, $current->psw);

        foreach ($checks as $oldHash) {
            if (hash('sha512', $data['new_password']) === $oldHash) {
                return response()->json(['message' => 'Nuova password già utilizzata recentemente'], 422);
            }
        }

        // save current into history
        ContattoPasswordHistory::create([
            'idContatto' => $id,
            'psw' => $current->psw,
            'sale' => $current->sale
        ]);

        // update current password
        $current->psw = hash('sha512', $data['new_password']);
        $current->sale = Str::random(16);
        $current->save();

        ContattoAuth::where('idContatto', $id)->update(['obbligoCambio' => 0]);

        // Invalida tutte le sessioni esistenti per questo contatto (forza il logout su tutti i device)
        ContattoSessione::eliminaSessione($id);

        return response()->json(['message' => 'Password aggiornata con successo'], 200);
    }

    /**
     * Change email (user) for contact (owner or admin).
     *
     * @param Request $request
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function changeEmail(Request $request, $id)
    {
        $roles = $request->attributes->get('contattiRuoli', []);
        $isAdmin = in_array('Amministratore', $roles);
        if (! $isAdmin && Auth::id() !== (int)$id) {
            abort(403, 'PE_0007');
        }

        $data = $request->validate([
            'email' => 'required|email|max:255'
        ]);

        $newHash = hash('sha512', trim($data['email']));

        $existing = ContattoAuth::where('user', $newHash)->first();
        if ($existing && (int)$existing->idContatto !== (int)$id) {
            return response()->json(['message' => 'Email già in uso'], 409);
        }

        $auth = ContattoAuth::where('idContatto', $id)->firstOrFail();
        $auth->user = $newHash;

        $auth->save();

        // Invalida tutte le sessioni del contatto per richiedere nuovo login
        ContattoSessione::eliminaSessione($id);

        return response()->json(['message' => 'Email aggiornata, effettua il login con la nuova email'], 200);
    }
}
