<?php

namespace App\Http\Controllers\Api\V1;

use App\Helpers\AppHelper;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Controllers\Api\V1\AccediController;

/**
 * Controller di supporto per testare l'autenticazione dal client.
 * Restituisce il payload decodificato del JWT e alcune informazioni utili.
 */
class TestAuthController extends Controller
{
    /**
     * GET /api/v1/test-auth
     * Richiede header Authorization: Bearer <token>
     */
    public function index(Request $request)
    {
        $token = $request->bearerToken();
        if (!$token) {
            abort(403, 'TK_0001');
        }

        // Usa la funzione di verifica token centralizzata
        $payload = AccediController::verificaToken($token);
        if ($payload === null) {
            abort(403, 'TK_0006');
        }

        // Costruisci dati utili per il client
        $data = [
            'payload' => $payload,
        ];

        return AppHelper::rispostaCustom($data);
    }
}
