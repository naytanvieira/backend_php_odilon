<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Drg;
use App\Models\DrgCondicaoAdquirida;
use App\Models\DrgCti;
use App\Models\DrgProcedimento;
use App\Models\DrgSuporteVentilatorio;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DrgDashboardController extends Controller
{
    private array $truthy = ['sim', 's', '1', 'true', 'verdadeiro', 'yes'];

    public function resumo(Request $request): JsonResponse
    {
        $base = $this->baseQuery($request);
        $total = (clone $base)->count();

        $altas = (clone $base)
            ->whereNotNull('data_da_alta')
            ->where('data_da_alta', '<>', '')
            ->count();

        $ativas = (clone $base)
            ->where(function ($q) {
                $q->whereNull('data_da_alta')->orWhere('data_da_alta', '');
            })
            ->count();

        $drgIds = (clone $base)->pluck('id');

        $comCondicaoAdquirida = $drgIds->isEmpty()
            ? 0
            : DrgCondicaoAdquirida::whereIn('drg_id', $drgIds)->distinct('drg_id')->count('drg_id');

        $comCti = $drgIds->isEmpty()
            ? 0
            : DrgCti::whereIn('drg_id', $drgIds)->distinct('drg_id')->count('drg_id');

        $comSuporteVentilatorio = $drgIds->isEmpty()
            ? 0
            : DrgSuporteVentilatorio::whereIn('drg_id', $drgIds)->distinct('drg_id')->count('drg_id');

        $comVentilacaoMarcada = $this->contarTruthy((clone $base), 'ventilacao_mecanica');
        $comVentilacao = max($comSuporteVentilatorio, $comVentilacaoMarcada);

        $readmissao = $this->contarTruthy((clone $base), 'internacao_e_responsavel_por_readmissao_em_30_dias');

        $permanenciaReal = $this->mediaNumerica((clone $base), 'permanencia_real');
        $permanenciaPrevista = $this->mediaNumerica((clone $base), 'permanencia_prevista_na_alta');
        $pesoMedioDrg = $this->mediaNumerica((clone $base), 'peso_do_drg_brasil_refinado');

        $data = [
            'total_internacoes' => $total,
            'altas' => $altas,
            'internacoes_ativas' => $ativas,
            'permanencia_media_real' => $permanenciaReal,
            'permanencia_media_prevista' => $permanenciaPrevista,
            'taxa_readmissao_30_dias' => $this->percentual($readmissao, $total),
            'taxa_condicao_adquirida' => $this->percentual($comCondicaoAdquirida, $total),
            'taxa_cti' => $this->percentual($comCti, $total),
            'taxa_ventilacao' => $this->percentual($comVentilacao, $total),
            'peso_medio_drg' => round($pesoMedioDrg, 2),
            'drgs_top' => $this->drgsTop($request),
            'tipo_drg' => $this->agruparDrg($request, 'tipo_de_drg', 'name', 10),
            'mdc_top' => $this->agruparDrg($request, 'descricao_do_mdc', 'name', 8),
            'altas_por_condicao' => $this->agruparDrg($request, 'condicao_da_alta', 'name', 8),
            'permanencia_mensal' => $this->permanenciaMensal($request),
            'faixa_etaria' => $this->faixaEtaria($request),
            'sexo' => $this->agruparDrg($request, 'sexo', 'name', 6),
            'procedencia' => $this->agruparDrg($request, 'procedencia_do_paciente', 'name', 8),
            'hospitais' => $this->hospitais($request),
            'condicoes_adquiridas_top' => $this->condicoesAdquiridasTop($drgIds),
            'procedimentos_top' => $this->procedimentosTop($drgIds),
            'cti_por_tipo' => $this->ctiPorTipo($drgIds),
            'alertas' => $this->alertas(
                $permanenciaReal,
                $permanenciaPrevista,
                $this->percentual($comCondicaoAdquirida, $total),
                $this->percentual($readmissao, $total)
            ),
        ];

        return response()->json([
            'data' => $data,
        ]);
    }

    private function baseQuery(Request $request)
    {
        $query = Drg::query();

        if ($request->filled('data_inicio')) {
            $query->where('data_de_internacao', '>=', $request->query('data_inicio'));
        }

        if ($request->filled('data_fim')) {
            $query->where('data_de_internacao', '<=', $request->query('data_fim'));
        }

        if ($request->filled('hospital')) {
            $query->where('nome_do_hospital', $request->query('hospital'));
        }

        if ($request->filled('instituicao')) {
            $query->where('nome_da_instituicao', $request->query('instituicao'));
        }

        if ($request->filled('tipo_drg')) {
            $query->where('tipo_de_drg', $request->query('tipo_drg'));
        }

        if ($request->filled('mdc')) {
            $query->where('descricao_do_mdc', $request->query('mdc'));
        }

        /**
         * Novo filtro por UF.
         *
         * Exemplo de uso:
         * /api/drg-dashboard/resumo?uf=MG
         */
        if ($request->filled('uf')) {
            $uf = strtoupper(trim($request->query('uf')));

            $query->whereRaw(
                "UPPER(TRIM({$this->colunaSql('uf')})) = ?",
                [$uf]
            );
        }

        /**
         * Novo filtro por município.
         *
         * Exemplo de uso:
         * /api/drg-dashboard/resumo?municipio=Belo Horizonte
         */
        if ($request->filled('municipio')) {
            $municipio = mb_strtolower(trim($request->query('municipio')), 'UTF-8');

            $query->whereRaw(
                "LOWER(TRIM({$this->colunaSql('municipio')})) = ?",
                [$municipio]
            );
        }

        return $query;
    }

    private function percentual(int|float $parte, int|float $total): float
    {
        if ($total <= 0) {
            return 0.0;
        }

        return round(($parte / $total) * 100, 1);
    }

    private function colunaSql(string $column): string
    {
        return '`' . str_replace('`', '``', $column) . '`';
    }

    private function numeroSql(string $column): string
    {
        $columnSql = $this->colunaSql($column);

        return "CAST(REPLACE(NULLIF({$columnSql}, ''), ',', '.') AS DECIMAL(12,4))";
    }

    private function labelSql(string $column): string
    {
        $columnSql = $this->colunaSql($column);

        return "COALESCE(NULLIF({$columnSql}, ''), 'Não informado')";
    }

    private function mediaNumerica($query, string $column): float
    {
        return round((float) $query->selectRaw("AVG({$this->numeroSql($column)}) as media")->value('media'), 2);
    }

    private function contarTruthy($query, string $column): int
    {
        return $query
            ->whereIn(DB::raw("LOWER(TRIM({$this->colunaSql($column)}))"), $this->truthy)
            ->count();
    }

    private function agruparDrg(Request $request, string $column, string $alias = 'name', int $limit = 10): array
    {
        $label = $this->labelSql($column);

        return $this->baseQuery($request)
            ->selectRaw("{$label} as {$alias}, COUNT(*) as total")
            ->groupByRaw($label)
            ->orderByDesc('total')
            ->limit($limit)
            ->get()
            ->map(fn ($row) => [
                $alias => $row->{$alias},
                'total' => (int) $row->total,
            ])
            ->values()
            ->toArray();
    }

    private function drgsTop(Request $request): array
    {
        $codigo = $this->labelSql('codigo_do_drg_brasil_refinado');
        $descricao = $this->labelSql('descricao_do_drg_brasil_refinado');

        return $this->baseQuery($request)
            ->selectRaw("{$codigo} as codigo")
            ->selectRaw("{$descricao} as descricao")
            ->selectRaw('COUNT(*) as total')
            ->selectRaw("AVG({$this->numeroSql('permanencia_real')}) as permanencia_real")
            ->selectRaw("AVG({$this->numeroSql('permanencia_prevista_na_alta')}) as permanencia_prevista")
            ->groupByRaw("{$codigo}, {$descricao}")
            ->orderByDesc('total')
            ->limit(10)
            ->get()
            ->map(fn ($row) => [
                'codigo' => $row->codigo,
                'descricao' => $row->descricao,
                'total' => (int) $row->total,
                'permanencia_real' => round((float) $row->permanencia_real, 1),
                'permanencia_prevista' => round((float) $row->permanencia_prevista, 1),
            ])
            ->toArray();
    }

    private function permanenciaMensal(Request $request): array
    {
        $dataInternacao = $this->colunaSql('data_de_internacao');

        $dataConvertida = "COALESCE(
            STR_TO_DATE(LEFT({$dataInternacao}, 10), '%Y-%m-%d'),
            STR_TO_DATE(LEFT({$dataInternacao}, 10), '%d/%m/%Y')
        )";

        $mes = "DATE_FORMAT({$dataConvertida}, '%Y-%m')";

        return $this->baseQuery($request)
            ->whereNotNull('data_de_internacao')
            ->where('data_de_internacao', '<>', '')
            ->whereRaw("{$dataConvertida} IS NOT NULL")
            ->selectRaw("{$mes} as mes")
            ->selectRaw("AVG({$this->numeroSql('permanencia_real')}) as permanencia_real_media")
            ->selectRaw("AVG({$this->numeroSql('permanencia_prevista_na_alta')}) as permanencia_prevista_media")
            ->groupByRaw($mes)
            ->orderByRaw("{$mes} desc")
            ->limit(12)
            ->get()
            ->map(fn ($row) => [
                'mes' => $row->mes,
                'real' => round((float) $row->permanencia_real_media, 1),
                'prevista' => round((float) $row->permanencia_prevista_media, 1),
                'diferenca' => round(
                    ((float) $row->permanencia_real_media) - ((float) $row->permanencia_prevista_media),
                    1
                ),
            ])
            ->values()
            ->toArray();
    }

    private function faixaEtaria(Request $request): array
    {
        $faixa = "CASE
                WHEN idade_em_anos IS NULL THEN 'Não informado'
                WHEN idade_em_anos < 18 THEN '0-17'
                WHEN idade_em_anos BETWEEN 18 AND 39 THEN '18-39'
                WHEN idade_em_anos BETWEEN 40 AND 59 THEN '40-59'
                WHEN idade_em_anos BETWEEN 60 AND 79 THEN '60-79'
                ELSE '80+'
            END";

        return $this->baseQuery($request)
            ->selectRaw("{$faixa} as faixa")
            ->selectRaw('COUNT(*) as total')
            ->groupByRaw($faixa)
            ->orderByRaw("FIELD(faixa, '0-17', '18-39', '40-59', '60-79', '80+', 'Não informado')")
            ->get()
            ->map(fn ($row) => [
                'faixa' => $row->faixa,
                'total' => (int) $row->total,
            ])
            ->toArray();
    }

    private function hospitais(Request $request): array
    {
        $name = "COALESCE(NULLIF(nome_do_hospital, ''), NULLIF(nome_da_instituicao, ''), 'Não informado')";

        return $this->baseQuery($request)
            ->selectRaw("{$name} as name")
            ->selectRaw('COUNT(*) as internacoes')
            ->selectRaw("AVG({$this->numeroSql('permanencia_real')}) as permanencia_real")
            ->selectRaw("SUM(CASE WHEN EXISTS (
                SELECT 1 FROM drg_condicoes_adquiridas dca WHERE dca.drg_id = drg.id
            ) THEN 1 ELSE 0 END) as com_ca")
            ->groupByRaw($name)
            ->orderByDesc('internacoes')
            ->limit(10)
            ->get()
            ->map(fn ($row) => [
                'name' => $row->name,
                'internacoes' => (int) $row->internacoes,
                'permanencia_real' => round((float) $row->permanencia_real, 1),
                'taxa_ca' => $this->percentual((int) $row->com_ca, (int) $row->internacoes),
            ])
            ->toArray();
    }

    private function condicoesAdquiridasTop($drgIds): array
    {
        if ($drgIds->isEmpty()) {
            return [];
        }

        $codigo = $this->labelSql('codigo');
        $descricao = $this->labelSql('descricao');

        return DrgCondicaoAdquirida::query()
            ->whereIn('drg_id', $drgIds)
            ->selectRaw("{$codigo} as codigo")
            ->selectRaw("{$descricao} as descricao")
            ->selectRaw('COUNT(*) as total')
            ->groupByRaw("{$codigo}, {$descricao}")
            ->orderByDesc('total')
            ->limit(10)
            ->get()
            ->map(fn ($row) => [
                'codigo' => $row->codigo,
                'descricao' => $row->descricao,
                'total' => (int) $row->total,
            ])
            ->toArray();
    }

    private function procedimentosTop($drgIds): array
    {
        if ($drgIds->isEmpty()) {
            return [];
        }

        $codigo = $this->labelSql('codigo');
        $nome = $this->labelSql('nome');

        return DrgProcedimento::query()
            ->whereIn('drg_id', $drgIds)
            ->selectRaw("{$codigo} as codigo")
            ->selectRaw("{$nome} as nome")
            ->selectRaw('COUNT(*) as total')
            ->groupByRaw("{$codigo}, {$nome}")
            ->orderByDesc('total')
            ->limit(10)
            ->get()
            ->map(fn ($row) => [
                'codigo' => $row->codigo,
                'nome' => $row->nome,
                'total' => (int) $row->total,
            ])
            ->toArray();
    }

    private function ctiPorTipo($drgIds): array
    {
        if ($drgIds->isEmpty()) {
            return [];
        }

        $name = $this->labelSql('tipo_de_cti');

        return DrgCti::query()
            ->whereIn('drg_id', $drgIds)
            ->selectRaw("{$name} as name")
            ->selectRaw('COUNT(*) as total')
            ->groupByRaw($name)
            ->orderByDesc('total')
            ->limit(10)
            ->get()
            ->map(fn ($row) => [
                'name' => $row->name,
                'total' => (int) $row->total,
            ])
            ->toArray();
    }

    private function alertas(float $real, float $prevista, float $taxaCa, float $taxaReadmissao): array
    {
        $alertas = [];
        $gap = round($real - $prevista, 1);

        if ($gap > 0) {
            $alertas[] = [
                'titulo' => 'Permanência acima do previsto',
                'descricao' => "A permanência real está {$gap} dia(s) acima da prevista no consolidado.",
                'criticidade' => $gap >= 2 ? 'alta' : 'media',
            ];
        }

        if ($taxaCa > 0) {
            $alertas[] = [
                'titulo' => 'Condições adquiridas registradas',
                'descricao' => "{$taxaCa}% das internações possuem pelo menos uma condição adquirida registrada.",
                'criticidade' => $taxaCa >= 10 ? 'alta' : 'media',
            ];
        }

        if ($taxaReadmissao > 0) {
            $alertas[] = [
                'titulo' => 'Readmissão em 30 dias',
                'descricao' => "Taxa atual de readmissão: {$taxaReadmissao}%.",
                'criticidade' => $taxaReadmissao >= 8 ? 'alta' : 'media',
            ];
        }

        if (empty($alertas)) {
            $alertas[] = [
                'titulo' => 'Operação estável',
                'descricao' => 'Nenhum alerta crítico detectado para os filtros atuais.',
                'criticidade' => 'baixa',
            ];
        }

        return $alertas;
    }
}