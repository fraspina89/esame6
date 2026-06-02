<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\TipoIndirizzo;
use App\Http\Resources\Api\V1\TipoIndirizzoResource;
use App\Http\Resources\Api\V1\TipoIndirizzoCollection;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class TipoIndirizzoController extends Controller
{
    /**
     * Restituisce l'elenco dei tipi di indirizzo.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function index(Request $request): JsonResponse
    {
        $query = TipoIndirizzo::query();

        // Filtri
        if ($request->has('attivi') && $request->attivi == '1') {
            $query->attivi();
        }

        if ($request->has('search') && $request->search) {
            $query->where('nome', 'like', '%' . $request->search . '%');
        }

        $tipi = $query->orderBy('nome')->get();

        return response()->json([
            'success' => true,
            'data' => new TipoIndirizzoCollection($tipi),
            'message' => 'Tipi indirizzo recuperati con successo'
        ]);
    }

    /**
     * Restituisce un tipo di indirizzo specifico.
     *
     * @param int $idTipoIndirizzo
     * @return JsonResponse
     */
    public function show($idTipoIndirizzo): JsonResponse
    {
        $tipo = TipoIndirizzo::findOrFail($idTipoIndirizzo);

        return response()->json([
            'success' => true,
            'data' => new TipoIndirizzoResource($tipo),
            'message' => 'Tipo indirizzo recuperato con successo'
        ]);
    }
}