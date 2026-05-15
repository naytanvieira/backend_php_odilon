<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Drg;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class DrgController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $perPage = min((int) $request->integer('per_page', 20), 100);

        $query = Drg::query()
            ->select([
                'id',
                'identificador_do_paciente',
                'codigo_do_paciente',
                'numero_de_atendimento',
                'numero_do_registro',
                'codigo',
                'nome_da_instituicao',
                'nome_do_hospital',
                'data_de_internacao',
                'data_da_alta',
                'condicao_da_alta',
                'codigo_do_drg_brasil_refinado',
                'descricao_do_drg_brasil_refinado',
                'codigo_do_mdc',
                'descricao_do_mdc',
                'tipo_de_drg',
                'permanencia_real',
                'permanencia_prevista_na_alta',
                'peso_do_drg_brasil_refinado',
                'cid_principal',
                'descricao_do_cid_principal',
                'sexo',
                'idade_em_anos',
                'created_at',
                'updated_at',
            ])
            ->withCount([
                'cidsSecundarios',
                'medicos',
                'procedimentos',
                'condicoesAdquiridas',
                'ctis',
                'suportesVentilatorios',
            ]);

        $this->aplicarFiltros($query, $request);

        $items = $query
            ->orderByDesc('data_de_internacao')
            ->orderByDesc('id')
            ->paginate($perPage);

        return response()->json($items);
    }

    public function show(Drg $drg): JsonResponse
    {
        $drg->load([
            'cidsSecundarios',
            'medicos',
            'procedimentos.medicos',
            'condicoesAdquiridas',
            'suportesVentilatorios.condicoes',
            'sondasVesicais.condicoes',
            'cateteresCentrais.condicoes',
            'dispositivosTerapeuticos',
            'analises',
            'falhasEstruturaProcesso',
            'recemNascidos',
            'ctis',
            'altasAdministrativas',
        ]);

        return response()->json([
            'data' => $drg,
        ]);
    }

    public function filtros(): JsonResponse
    {
        return response()->json([
            'hospitais' => Drg::query()
                ->whereNotNull('nome_do_hospital')
                ->where('nome_do_hospital', '<>', '')
                ->distinct()
                ->orderBy('nome_do_hospital')
                ->pluck('nome_do_hospital'),
            'instituicoes' => Drg::query()
                ->whereNotNull('nome_da_instituicao')
                ->where('nome_da_instituicao', '<>', '')
                ->distinct()
                ->orderBy('nome_da_instituicao')
                ->pluck('nome_da_instituicao'),
            'tipos_drg' => Drg::query()
                ->whereNotNull('tipo_de_drg')
                ->where('tipo_de_drg', '<>', '')
                ->distinct()
                ->orderBy('tipo_de_drg')
                ->pluck('tipo_de_drg'),
            'mdcs' => Drg::query()
                ->whereNotNull('descricao_do_mdc')
                ->where('descricao_do_mdc', '<>', '')
                ->distinct()
                ->orderBy('descricao_do_mdc')
                ->pluck('descricao_do_mdc'),
        ]);
    }

    private function aplicarFiltros($query, Request $request): void
    {
        if ($search = trim((string) $request->query('search', ''))) {
            $query->where(function ($q) use ($search) {
                $q->where('identificador_do_paciente', 'like', "%{$search}%")
                    ->orWhere('codigo_do_paciente', 'like', "%{$search}%")
                    ->orWhere('numero_de_atendimento', 'like', "%{$search}%")
                    ->orWhere('numero_do_registro', 'like', "%{$search}%")
                    ->orWhere('codigo', 'like', "%{$search}%")
                    ->orWhere('codigo_do_drg_brasil_refinado', 'like', "%{$search}%")
                    ->orWhere('descricao_do_drg_brasil_refinado', 'like', "%{$search}%")
                    ->orWhere('cid_principal', 'like', "%{$search}%")
                    ->orWhere('descricao_do_cid_principal', 'like', "%{$search}%");
            });
        }

        if ($hospital = $request->query('hospital')) {
            $query->where('nome_do_hospital', $hospital);
        }

        if ($instituicao = $request->query('instituicao')) {
            $query->where('nome_da_instituicao', $instituicao);
        }

        if ($tipoDrg = $request->query('tipo_drg')) {
            $query->where('tipo_de_drg', $tipoDrg);
        }

        if ($mdc = $request->query('mdc')) {
            $query->where('descricao_do_mdc', $mdc);
        }

        if ($dataInicio = $request->query('data_inicio')) {
            $query->where('data_de_internacao', '>=', $dataInicio);
        }

        if ($dataFim = $request->query('data_fim')) {
            $query->where('data_de_internacao', '<=', $dataFim);
        }

        if ($request->boolean('somente_ativas')) {
            $query->where(function ($q) {
                $q->whereNull('data_da_alta')->orWhere('data_da_alta', '');
            });
        }

        if ($request->boolean('com_condicao_adquirida')) {
            $query->whereHas('condicoesAdquiridas');
        }

        if ($request->boolean('com_cti')) {
            $query->whereHas('ctis');
        }
    }
}
