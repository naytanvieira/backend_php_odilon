<?php

namespace App\Http\Controllers;

use App\Actions\RecalcularSaldoAction;
use App\Http\Controllers\Controller;
use App\Models\PontoRegistro;
use App\Models\PontoSaldo;
use Carbon\Carbon;
use Illuminate\Http\Request;

class PontoRegistroController extends Controller
{
    /* =========================
       LISTAR REGISTROS
    ========================= */
    public function index()
    {
        $registros = PontoRegistro::with([
            'tipoPonto',
            'tipoJustificativa',
        ])
            ->where('user_id', auth()->id())
            ->orderBy('registrado_em', 'desc')
            ->get();

        return response()->json($registros);
    }

    /* =========================
       REGISTRAR PONTO
    ========================= */
    public function store(Request $request)
    {
        date_default_timezone_set('America/Sao_Paulo');
        $request->validate([
            'tipo_ponto_id' => [
                'required',
                'exists:tipos_ponto,id',
            ],

            'tipo_justificativa_id' => [
                'nullable',
                'exists:tipos_justificativa,id',
            ],
        ]);

        $registro = PontoRegistro::create([
            'user_id' => auth()->id(),

            'tipo_ponto_id' => $request->tipo_ponto_id,

            'tipo_justificativa_id' =>
            $request->tipo_justificativa_id,

            'registrado_em' => now(),
        ]);

        app(RecalcularSaldoAction::class)
            ->execute(auth()->id());

        return response()->json([
            'message' => 'Ponto registrado com sucesso.',
            'data' => $registro,
        ], 201);
    }


    public function buscaDia()
    {
        $hoje = Carbon::today();

        $registros = PontoRegistro::with([
            'tipoPonto',
            'tipoJustificativa',
        ])
            ->where('user_id', auth()->id())

            ->whereDate(
                'registrado_em',
                $hoje
            )

            ->orderBy('registrado_em', 'asc')

            ->get()

            ->map(function ($registro) {

                return [
                    'id' => $registro->id,

                    'tipo_ponto_id' => $registro->tipo_ponto_id,

                    'tipo' => [
                        'id' => $registro->tipoPonto?->id,

                        'name' => $registro->tipoPonto?->name,
                    ],

                    'justificativa' => $registro->tipoJustificativa
                        ? [
                            'id' => $registro->tipoJustificativa->id,

                            'name' => $registro->tipoJustificativa->name,
                        ]
                        : null,

                    'hora' => Carbon::parse(
                        $registro->registrado_em
                    )->format('H:i'),

                    'data' => Carbon::parse(
                        $registro->registrado_em
                    )->format('d/m/Y'),

                    'registrado_em' => $registro->registrado_em,
                ];
            });

        return response()->json([
            'data' => $registros,
        ]);
    }

    public function historicoSemanal()
    {
        $inicioSemana = now()->startOfWeek();
        $fimSemana = now()->endOfWeek();

        $registros = PontoRegistro::with('tipoPonto')
            ->where('user_id', auth()->id())
            ->whereBetween('registrado_em', [
                $inicioSemana,
                $fimSemana
            ])
            ->orderBy('registrado_em')
            ->get()
            ->groupBy(function ($item) {
                return \Carbon\Carbon::parse(
                    $item->registrado_em
                )->format('Y-m-d');
            });

        $diasSemana = [];

        for ($i = 0; $i < 5; $i++) {

            $data = now()
                ->startOfWeek()
                ->addDays($i);

            $key = $data->format('Y-m-d');

            $items = $registros[$key] ?? collect();

            $diasSemana[] = [
                'day' => ucfirst(
                    $data->translatedFormat('D')
                ),

                'is_today' => $data->isToday(),

                'records' => $items->map(function ($registro) {

                    return [
                        'label' => \Carbon\Carbon::parse(
                            $registro->registrado_em
                        )->format('H:i'),

                        'type' => match ($registro->tipo_ponto_id) {
                            1 => 'entrada',
                            2 => 'almoco',
                            3 => 'retorno',
                            4 => 'saida',
                            default => 'vazio'
                        }
                    ];
                })->values(),
            ];
        }

        return response()->json([
            'data' => $diasSemana
        ]);
    }

    public function saldo()
    {
        $saldo = PontoSaldo::where(
            'user_id',
            auth()->id()
        )

            ->where('mes', now()->month)

            ->where('ano', now()->year)

            ->first();

        return response()->json([
            'data' => $saldo
        ]);
    }
}
