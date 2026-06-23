@extends('layouts.app')
@section('page-title', 'Relatório de Execução')

@push('styles')
<style>
@media print {
    .sidebar, .topbar, .no-print { display:none !important; }
    .main-wrapper { margin-left:0 !important; }
    .page-content { padding:0 !important; }
    .rel-page { padding:20mm 20mm; }
}
.rel-section { margin-bottom:24px; }
.rel-section-title {
    font-size:11px; font-weight:700; text-transform:uppercase; letter-spacing:.08em;
    color:var(--cinza-light); border-bottom:2px solid var(--cinza-borda);
    padding-bottom:5px; margin-bottom:12px;
}
.stat-box {
    text-align:center; background:#fff; border:1px solid var(--cinza-borda);
    border-radius:10px; padding:14px 10px;
}
.stat-box .num  { font-size:28px; font-weight:700; color:var(--teal); font-family:'Roboto',sans-serif; }
.stat-box .lbl  { font-size:11px; color:var(--cinza-light); }
.dem-row { display:flex; justify-content:space-between; align-items:center; padding:5px 0; font-size:13px; border-bottom:1px solid #f3f3f3; }
</style>
@endpush

@section('content')

<div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:20px;" class="no-print">
    <a href="{{ route('acoes.show', $acao) }}" style="font-size:12px;color:var(--cinza-light);text-decoration:none;">← {{ $acao->titulo }}</a>
    <button onclick="window.print()" class="btn btn-primary">🖨 Imprimir / Salvar PDF</button>
</div>

<div class="rel-page" style="max-width:860px;background:#fff;border:1px solid var(--cinza-borda);border-radius:12px;padding:32px;">

    {{-- Cabeçalho --}}
    <div style="text-align:center;margin-bottom:28px;padding-bottom:20px;border-bottom:2px solid var(--cinza-borda);">
        <div style="font-size:13px;color:var(--cinza-light);margin-bottom:4px;text-transform:uppercase;letter-spacing:.06em;">Relatório de Execução de Ação Social</div>
        <div style="font-size:22px;font-weight:700;color:var(--texto);margin-bottom:4px;">{{ $acao->titulo }}</div>
        <div style="font-size:13px;color:var(--cinza-light);">
            {{ $institution->name ?? 'Associação Promessa' }} · Gerado em {{ now()->format('d/m/Y') }}
        </div>
    </div>

    {{-- Estatísticas --}}
    <div class="rel-section">
        <div class="rel-section-title">Resumo quantitativo</div>
        <div style="display:grid;grid-template-columns:repeat(4,1fr);gap:12px;">
            <div class="stat-box">
                <div class="num">{{ $acao->total_sessoes }}</div>
                <div class="lbl">Sessões realizadas</div>
            </div>
            <div class="stat-box">
                <div class="num">{{ count($beneficiariosMap) }}</div>
                <div class="lbl">Beneficiários únicos</div>
            </div>
            <div class="stat-box">
                <div class="num">{{ $acao->total_presencas }}</div>
                <div class="lbl">Presenças totais</div>
            </div>
            <div class="stat-box">
                <div class="num">{{ $acao->carga_horaria_total ?? '—' }}{{ $acao->carga_horaria_total ? 'h' : '' }}</div>
                <div class="lbl">Carga horária total</div>
            </div>
        </div>
    </div>

    {{-- Identificação da ação --}}
    <div class="rel-section">
        <div class="rel-section-title">Identificação da ação</div>
        <div style="display:grid;grid-template-columns:1fr 1fr;gap:8px 24px;font-size:13.5px;">
            <div><strong>Tipo:</strong> {{ $acao->tipo_label }}</div>
            <div><strong>Status:</strong> {{ $acao->status_label }}</div>
            <div><strong>Local:</strong> {{ $acao->local ?? '—' }}</div>
            <div><strong>Responsável:</strong> {{ $acao->responsavel_nome ?? '—' }}{{ $acao->responsavel_cargo ? ' (' . $acao->responsavel_cargo . ')' : '' }}</div>
            @if($acao->project)
            <div style="grid-column:span 2;"><strong>Projeto vinculado:</strong> {{ $acao->project->title }}</div>
            @endif
        </div>
    </div>

    @if($acao->objetivos)
    <div class="rel-section">
        <div class="rel-section-title">Objetivos</div>
        <div style="font-size:13.5px;line-height:1.7;white-space:pre-line;">{{ $acao->objetivos }}</div>
    </div>
    @endif

    @if($acao->metodologia)
    <div class="rel-section">
        <div class="rel-section-title">Metodologia</div>
        <div style="font-size:13.5px;line-height:1.7;white-space:pre-line;">{{ $acao->metodologia }}</div>
    </div>
    @endif

    {{-- Sessões --}}
    <div class="rel-section">
        <div class="rel-section-title">Registro de sessões</div>
        <table style="width:100%;border-collapse:collapse;font-size:13px;">
            <thead>
                <tr style="background:var(--cinza-bg);">
                    <th style="padding:8px 10px;text-align:left;border:1px solid var(--cinza-borda);">Nº</th>
                    <th style="padding:8px 10px;text-align:left;border:1px solid var(--cinza-borda);">Data</th>
                    <th style="padding:8px 10px;text-align:left;border:1px solid var(--cinza-borda);">Horário</th>
                    <th style="padding:8px 10px;text-align:left;border:1px solid var(--cinza-borda);">Local</th>
                    <th style="padding:8px 10px;text-align:left;border:1px solid var(--cinza-borda);">Facilitador</th>
                    <th style="padding:8px 10px;text-align:center;border:1px solid var(--cinza-borda);">Presentes</th>
                </tr>
            </thead>
            <tbody>
                @foreach($acao->sessoes as $i => $sessao)
                @php $presentes = $sessao->beneficiarios->filter(fn($b) => $b->pivot->presente)->count(); @endphp
                <tr>
                    <td style="padding:7px 10px;border:1px solid var(--cinza-borda);">{{ $i+1 }}</td>
                    <td style="padding:7px 10px;border:1px solid var(--cinza-borda);">{{ $sessao->data_execucao->format('d/m/Y') }}</td>
                    <td style="padding:7px 10px;border:1px solid var(--cinza-borda);">
                        {{ $sessao->hora_inicio ?? '—' }}{{ $sessao->hora_fim ? ' – ' . $sessao->hora_fim : '' }}
                    </td>
                    <td style="padding:7px 10px;border:1px solid var(--cinza-borda);">{{ $sessao->local_override ?? $acao->local ?? '—' }}</td>
                    <td style="padding:7px 10px;border:1px solid var(--cinza-borda);">{{ $sessao->facilitador_override ?? $acao->responsavel_nome ?? '—' }}</td>
                    <td style="padding:7px 10px;border:1px solid var(--cinza-borda);text-align:center;font-weight:600;">{{ $presentes }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    {{-- Perfil dos beneficiários --}}
    @if(count($beneficiariosMap) > 0)
    <div class="rel-section">
        <div class="rel-section-title">Perfil dos beneficiários atendidos</div>
        <div style="display:grid;grid-template-columns:1fr 1fr;gap:20px;">
            <div>
                <div style="font-weight:600;font-size:12px;margin-bottom:8px;color:var(--cinza);">Por gênero</div>
                @foreach(['masculino'=>'Masculino','feminino'=>'Feminino','nao_binario'=>'Não-binário','prefiro_nao_informar'=>'Não informado'] as $k => $l)
                    @if(isset($generos[$k]))
                    <div class="dem-row"><span>{{ $l }}</span><strong>{{ $generos[$k] }}</strong></div>
                    @endif
                @endforeach
            </div>
            <div>
                <div style="font-weight:600;font-size:12px;margin-bottom:8px;color:var(--cinza);">Por raça/cor (autodeclaração)</div>
                @foreach(['branca'=>'Branca','preta'=>'Preta','parda'=>'Parda','amarela'=>'Amarela','indigena'=>'Indígena','nao_informado'=>'Não informado'] as $k => $l)
                    @if(isset($racas[$k]))
                    <div class="dem-row"><span>{{ $l }}</span><strong>{{ $racas[$k] }}</strong></div>
                    @endif
                @endforeach
            </div>
        </div>
        @if($menores > 0)
        <div style="margin-top:12px;font-size:13px;background:#fff8e1;border-left:3px solid var(--amarelo);padding:8px 12px;border-radius:0 6px 6px 0;">
            <strong>{{ $menores }}</strong> beneficiário(s) são menores de 18 anos ({{ round($menores / count($beneficiariosMap) * 100) }}% do total).
        </div>
        @endif
    </div>

    {{-- Lista nominal de beneficiários --}}
    <div class="rel-section">
        <div class="rel-section-title">Lista de beneficiários ({{ count($beneficiariosMap) }})</div>
        <table style="width:100%;border-collapse:collapse;font-size:12.5px;">
            <thead>
                <tr style="background:var(--cinza-bg);">
                    <th style="padding:7px 10px;text-align:left;border:1px solid var(--cinza-borda);">Nome</th>
                    <th style="padding:7px 10px;text-align:left;border:1px solid var(--cinza-borda);">CPF / Responsável</th>
                    <th style="padding:7px 10px;text-align:center;border:1px solid var(--cinza-borda);">Presenças</th>
                    <th style="padding:7px 10px;text-align:center;border:1px solid var(--cinza-borda);">Freq. %</th>
                </tr>
            </thead>
            <tbody>
                @foreach($beneficiariosMap as $r)
                @php
                    $b = $r['beneficiario'];
                    $pct = $acao->total_sessoes > 0 ? round($r['presencas'] / $acao->total_sessoes * 100) : 0;
                @endphp
                <tr>
                    <td style="padding:6px 10px;border:1px solid var(--cinza-borda);">
                        {{ $b->nome }}
                        @if($b->is_menor)<span style="font-size:10px;color:#e65100;"> (menor)</span>@endif
                    </td>
                    <td style="padding:6px 10px;border:1px solid var(--cinza-borda);font-size:12px;color:var(--cinza-light);">
                        {{ $b->cpf ?? '—' }}
                        @if($b->nome_responsavel)<br>Resp.: {{ $b->nome_responsavel }}@endif
                    </td>
                    <td style="padding:6px 10px;border:1px solid var(--cinza-borda);text-align:center;font-weight:600;">{{ $r['presencas'] }}</td>
                    <td style="padding:6px 10px;border:1px solid var(--cinza-borda);text-align:center;color:{{ $pct >= 75 ? '#2e7d32' : ($pct >= 50 ? '#e65100' : '#c62828') }};">{{ $pct }}%</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @endif

    {{-- Assinaturas --}}
    <div class="rel-section" style="margin-top:40px;">
        <div class="rel-section-title">Responsável pela execução</div>
        <div style="display:grid;grid-template-columns:1fr 1fr;gap:40px;margin-top:24px;">
            <div>
                <div style="border-top:1px solid #999;padding-top:6px;text-align:center;">
                    <div style="font-size:13px;">{{ $acao->responsavel_nome ?? 'Responsável' }}</div>
                    <div style="font-size:11.5px;color:var(--cinza-light);">{{ $acao->responsavel_cargo ?? 'Cargo' }}</div>
                </div>
            </div>
            <div>
                <div style="border-top:1px solid #999;padding-top:6px;text-align:center;">
                    <div style="font-size:13px;">Coordenação</div>
                    <div style="font-size:11.5px;color:var(--cinza-light);">Associação Promessa</div>
                </div>
            </div>
        </div>
    </div>

</div>
@endsection
