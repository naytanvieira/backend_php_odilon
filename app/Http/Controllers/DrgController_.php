<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class DrgController extends Controller
{
    private string $apiUrl   = 'https://api-autenticacao.grupoiagsaude.com.br/login';
    private string $userName = 'hm007750';
    private string $password = 'Li102030';
    private string $origin   = 'API_DRG';

    /**
     * Autentica na API IAG Saúde com credenciais fixas e retorna o token.
     */
    public function renovarToken(): JsonResponse
    {
        try {
            $response = Http::timeout(15)
                ->withHeaders([
                    'Accept'       => 'application/json',
                    'Content-Type' => 'application/json',
                ])
                ->post($this->apiUrl, [
                    'userName' => $this->userName,
                    'password' => $this->password,
                    'origin'   => $this->origin,
                ]);

            $statusCode   = $response->status();
            $responseBody = $response->json();

            if ($response->successful()) {
                $token = $responseBody['token']
                    ?? $responseBody['access_token']
                    ?? $responseBody['accessToken']
                    ?? null;

                if (!$token) {
                    Log::warning('IAG Auth: resposta 2xx sem token.', ['response' => $responseBody]);

                    return response()->json([
                        'success' => false,
                        'message' => 'Autenticação OK, mas nenhum token foi encontrado na resposta.',
                    ], 500);
                }

                return response()->json([
                    'success' => true,
                    'message' => 'Token renovado com sucesso.',
                    'token'   => $token,
                    'data'    => $responseBody,
                ], 200);
            }

            Log::error('IAG Auth: erro ao renovar token.', [
                'status'   => $statusCode,
                'response' => $responseBody,
            ]);

            return response()->json([
                'success' => false,
                'message' => $responseBody['message'] ?? $responseBody['error'] ?? 'Erro ao autenticar na API IAG Saúde.',
            ], $statusCode);

        } catch (\Illuminate\Http\Client\ConnectionException $e) {
            Log::error('IAG Auth: falha de conexão.', ['error' => $e->getMessage()]);

            return response()->json([
                'success' => false,
                'message' => 'Não foi possível conectar ao serviço de autenticação.',
            ], 503);

        } catch (\Throwable $e) {
            Log::error('IAG Auth: exceção inesperada.', ['error' => $e->getMessage()]);

            return response()->json([
                'success' => false,
                'message' => 'Erro interno ao renovar o token.',
                'erro' => $e,
            ], 500);
        }
    }
}