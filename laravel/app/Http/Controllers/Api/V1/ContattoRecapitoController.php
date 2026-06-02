<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\ContattoRecapitoStoreRequest;
use App\Http\Requests\Api\V1\ContattoRecapitoUpdateRequest;
use App\Http\Resources\Api\V1\ContattoRecapitoResource;
use App\Http\Resources\Api\V1\ContattoRecapitoCollection;
use App\Models\Contatto;
use App\Models\ContattoRecapito;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Gate;

/**
 * Controller per i recapiti dei contatti
 *
 * Gestisce i recapiti (telefono, email, ecc.) associati ai contatti.
 */
class ContattoRecapitoController extends Controller
{
    /**
     * Restituisce l'elenco dei recapiti di un contatto.
     *
     * @param Request $request
     * @param int $idContatto
     * @return JsonResponse
     */
    public function index(Request $request, $idContatto): JsonResponse
    {
        // Verifica che il contatto esista
        $contatto = Contatto::findOrFail($idContatto);
        
        // Autorizzazione: solo owner o admin
        $roles = $request->attributes->get('contattiRuoli', []);
        $isAdmin = in_array('Amministratore', $roles);
        if (! $isAdmin && \Illuminate\Support\Facades\Auth::id() !== $contatto->idContatto) {
            abort(403);
        }

        // Query base
        $query = $contatto->recapiti();

        // Filtri opzionali
        if ($request->has('tipo') && $request->tipo) {
            $query->byTipo($request->tipo);
        }

        if ($request->has('preferiti') && $request->preferiti == '1') {
            $query->preferiti();
        }

        if ($request->has('email') && $request->email == '1') {
            $query->email();
        }

        if ($request->has('telefoni') && $request->telefoni == '1') {
            $query->telefoni();
        }

        $recapiti = $query->with(['tipoRecapito'])->get();

        return response()->json([
            'success' => true,
            'data' => new ContattoRecapitoCollection($recapiti),
            'message' => "Recapiti del contatto {$contatto->nome} {$contatto->cognome} recuperati con successo"
        ]);
    }

    /**
     * Crea un nuovo recapito per un contatto.
     *
     * @param ContattoRecapitoStoreRequest $request
     * @param int $idContatto
     * @return JsonResponse
     */
    public function store(ContattoRecapitoStoreRequest $request, $idContatto): JsonResponse
    {
        // Verifica che il contatto esista
        $contatto = Contatto::findOrFail($idContatto);
        
        // Autorizzazione: solo owner o admin
        $roles = $request->attributes->get('contattiRuoli', []);
        $isAdmin = in_array('Amministratore', $roles);
        if (! $isAdmin && \Illuminate\Support\Facades\Auth::id() !== $contatto->idContatto) {
            abort(403);
        }

        $data = $request->validated();
        $data['idContatto'] = $idContatto;

        // Se è preferito, rimuovi la preferenza dagli altri dello stesso tipo
        if ($data['preferito']) {
            ContattoRecapito::where('idContatto', $idContatto)
                           ->where('idTipoRecapito', $data['idTipoRecapito'])
                           ->update(['preferito' => false]);
        }

        $recapito = ContattoRecapito::create($data);
        $recapito->load(['contatto', 'tipoRecapito']);

        return response()->json([
            'success' => true,
            'data' => new ContattoRecapitoResource($recapito),
            'message' => 'Recapito creato con successo'
        ], 201);
    }

    /**
     * Restituisce un recapito specifico di un contatto.
     *
     * @param int $idContatto
     * @param int $idRecapito
     * @return JsonResponse
     */
    public function show($idContatto, $idRecapito): JsonResponse
    {
        // Verifica che il contatto esista
        $contatto = Contatto::findOrFail($idContatto);
        
        // Autorizzazione: solo owner o admin
        $roles = request()->attributes->get('contattiRuoli', []);
        $isAdmin = in_array('Amministratore', $roles);
        if (! $isAdmin && \Illuminate\Support\Facades\Auth::id() !== $contatto->idContatto) {
            abort(403);
        }

        $recapito = $contatto->recapiti()
                           ->where('idRecapito', $idRecapito)
                           ->with(['tipoRecapito'])
                           ->firstOrFail();

        return response()->json([
            'success' => true,
            'data' => new ContattoRecapitoResource($recapito),
            'message' => 'Recapito recuperato con successo'
        ]);
    }

    /**
     * Aggiorna un recapito esistente di un contatto.
     *
     * @param ContattoRecapitoUpdateRequest $request
     * @param int $idContatto
     * @param int $idRecapito
     * @return JsonResponse
     */
    public function update(ContattoRecapitoUpdateRequest $request, $idContatto, $idRecapito): JsonResponse
    {
        // Verifica che il contatto esista
        $contatto = Contatto::findOrFail($idContatto);
        
        // Autorizzazione: solo owner o admin
        $roles = $request->attributes->get('contattiRuoli', []);
        $isAdmin = in_array('Amministratore', $roles);
        if (! $isAdmin && \Illuminate\Support\Facades\Auth::id() !== $contatto->idContatto) {
            abort(403);
        }

        $recapito = $contatto->recapiti()
                           ->where('idRecapito', $idRecapito)
                           ->firstOrFail();

        $data = $request->validated();

        // Se è preferito, rimuovi la preferenza dagli altri dello stesso tipo
        if (isset($data['preferito']) && $data['preferito']) {
            ContattoRecapito::where('idContatto', $idContatto)
                           ->where('idTipoRecapito', $data['idTipoRecapito'] ?? $recapito->idTipoRecapito)
                           ->where('idRecapito', '!=', $idRecapito)
                           ->update(['preferito' => false]);
        }

        $recapito->update($data);
        $recapito->load(['contatto', 'tipoRecapito']);

        return response()->json([
            'success' => true,
            'data' => new ContattoRecapitoResource($recapito),
            'message' => 'Recapito aggiornato con successo'
        ]);
    }

    /**
     * Elimina un recapito di un contatto.
     *
     * @param int $idContatto
     * @param int $idRecapito
     * @return JsonResponse
     */
    public function destroy($idContatto, $idRecapito): JsonResponse
    {
        // Verifica che il contatto esista
        $contatto = Contatto::findOrFail($idContatto);
        
        // Autorizzazione: solo owner o admin
        $roles = request()->attributes->get('contattiRuoli', []);
        $isAdmin = in_array('Amministratore', $roles);
        if (! $isAdmin && \Illuminate\Support\Facades\Auth::id() !== $contatto->idContatto) {
            abort(403);
        }

        $recapito = $contatto->recapiti()
                           ->where('idRecapito', $idRecapito)
                           ->firstOrFail();

        $recapito->delete();

        return response()->json([
            'success' => true,
            'message' => 'Recapito eliminato con successo'
        ]);
    }

    /**
     * Restore a soft-deleted resource.
     *
     * @param int $idContatto
     * @param int $idRecapito
     * @return JsonResponse
     */
    public function restore($idContatto, $idRecapito): JsonResponse
    {
        // Verifica che il contatto esista
        $contatto = Contatto::findOrFail($idContatto);
        
        // Autorizzazione: solo owner o admin
        $roles = request()->attributes->get('contattiRuoli', []);
        $isAdmin = in_array('Amministratore', $roles);
        if (! $isAdmin && \Illuminate\Support\Facades\Auth::id() !== $contatto->idContatto) {
            abort(403);
        }

        $recapito = $contatto->recapiti()
                           ->onlyTrashed()
                           ->where('idRecapito', $idRecapito)
                           ->firstOrFail();

        $recapito->restore();
        $recapito->load(['contatto', 'tipoRecapito']);

        return response()->json([
            'success' => true,
            'data' => new ContattoRecapitoResource($recapito),
            'message' => 'Recapito ripristinato con successo'
        ]);
    }

    /**
     * Get trashed resources.
     *
     * @param int $idContatto
     * @return JsonResponse
     */
    public function trashed($idContatto): JsonResponse
    {
        // Verifica che il contatto esista
        $contatto = Contatto::findOrFail($idContatto);
        
        // Autorizzazione
        if (!Gate::allows('read_contatti', $contatto)) {
            abort(403);
        }

        $recapitiEliminati = $contatto->recapiti()
                                   ->onlyTrashed()
                                   ->with(['tipoRecapito'])
                                   ->get();

        return response()->json([
            'success' => true,
            'data' => new ContattoRecapitoCollection($recapitiEliminati),
            'message' => 'Recapiti eliminati recuperati con successo'
        ]);
    }
}