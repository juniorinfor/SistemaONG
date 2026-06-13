@extends('layouts.app')

@section('title', 'Dashboard')
@section('page-title', 'Dashboard')
@section('page-subtitle', 'Visão geral da regularidade documental')

@section('content')

{{-- Métricas rápidas --}}
<div class="grid-4 mb-6">
    <div class="card" style="border-top:3px solid var(--azul);">
        <div class="card-body" style="padding:18px 20px;">
            <div style="font-size:10.5px;font-weight:600;text-transform:uppercase;letter-spacing:.08em;color:var(--cinza-light);margin-bottom:8px;">Total</div>
            <div style="font-size:34px;font-weight:700;font-family:'Roboto',sans-serif;color:var(--texto);">{{ $total }}</div>
            <div style="font-size:11.5px;color:var(--cinza-light);margin-top:2px;">versões vigentes</div>
        </div>
    </div>

    <div class="card" style="border-top:3px solid var(--teal);">
        <div class="card-body" style="padding:18px 20px;">
            <div style="font-size:10.5px;font-weight:600;text-transform:uppercase;letter-spacing:.08em;color:var(--cinza-light);margin-bottom:8px;">Válidos</div>
            <div style="font-size:34px;font-weight:700;font-family:'Roboto',sans-serif;color:var(--teal-deep);">{{ $validos }}</div>
            <div style="font-size:11.5px;color:var(--cinza-light);margin-top:2px;">em dia</div>
        </div>
    </div>

    <div class="card" style="border-top:3px solid var(--amarelo);">
        <div class="card-body" style="padding:18px 20px;">
            <div style="font-size:10.5px;font-weight:600;text-transform:uppercase;letter-spacing:.08em;color:var(--cinza-light);margin-bottom:8px;">Vencendo</div>
            <div style="font-size:34px;font-weight:700;font-family:'Roboto',sans-serif;color:#8a6000;">{{ $breve }}</div>
            <div style="font-size:11.5px;color:var(--cinza-light);margin-top:2px;">próximos 30 dias</div>
        </div>
    </div>

    <div class="card" style="border-top:3px solid #e53935;">
        <div class="card-body" style="padding:18px 20px;">
            <div style="font-size:10.5px;font-weight:600;text-transform:uppercase;letter-spacing:.08em;color:var(--cinza-light);margin-bottom:8px;">Vencidos</div>
            <div style="font-size:34px;font-weight:700;font-family:'Roboto',sans-serif;color:#b71c1c;">{{ $vencidos }}</div>
            <div style="font-size:11.5px;color:var(--cinza-light);margin-top:2px;">requerem atenção</div>
        </div>
    </div>
</div>

<div class="grid-2 mb-6">

    {{-- Regularidade por checklist --}}
    <div class="card">
        <div class="card-header">
            <span class="card-title">Regularidade por Edital</span>
            <a href="{{ route('checklists.index') }}" class="btn btn-ghost btn-sm">Ver todos</a>
        </div>
        <div>
            @forelse($checklists as $cl)
                @php
                    $pct   = $cl['pct'];
                    $color = $pct >= 80 ? 'var(--teal)' : ($pct >= 50 ? 'var(--amarelo)' : '#e53935');
                    $fill  = $pct < 50 ? 'danger' : ($pct < 80 ? 'warn' : '');
                @endphp
                <div style="padding:14px 22px;border-bottom:1px solid var(--cinza-borda);">
                    <div style="display:flex;align-items:center;gap:10px;margin-bottom:8px;">
                        <span style="font-size:13px;font-weight:500;flex:1;color:var(--texto);">{{ $cl['name'] }}</span>
                        <span style="font-size:14px;font-weight:700;color:{{ $color }};">{{ $pct }}%</span>
                    </div>
                    <div class="progress-bar-wrap">
                        <div class="progress-bar-fill {{ $fill }}" style="width:{{ $pct }}%;"></div>
                    </div>
                    @if($cl['missing'] > 0)
                        <div style="font-size:11px;color:var(--cinza-light);margin-top:5px;">
                            {{ $cl['missing'] }} documento(s) obrigatório(s) faltando
                        </div>
                    @endif
                </div>
            @empty
                <div style="padding:24px;text-align:center;color:var(--cinza-light);font-size:13px;">
                    Nenhum checklist ativo.
                </div>
            @endforelse
        </div>
    </div>

    {{-- Documentos vencendo --}}
    <div class="card">
        <div class="card-header">
            <span class="card-title">Vencendo nos próximos 30 dias</span>
            <a href="{{ route('documents.index', ['status' => 'vence_em_breve']) }}" class="btn btn-ghost btn-sm">Ver todos</a>
        </div>
        <div class="table-wrapper">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Documento</th>
                        <th>Vence em</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($expiring as $doc)
                        <tr>
                            <td style="font-weight:500;">{{ $doc->documentType->name }}</td>
                            <td>
                                @php $days = (int) now()->diffInDays($doc->expires_at, false); @endphp
                                <span class="badge badge-em-breve">
                                    <span class="badge-dot"></span>
                                    {{ $days === 0 ? 'Hoje' : "em {$days}d" }}
                                </span>
                            </td>
                            <td><a href="{{ route('documents.show', $doc) }}" class="btn btn-ghost btn-sm">Ver</a></td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="3" style="text-align:center;color:var(--cinza-light);padding:24px;font-size:13px;">
                                Nenhum documento vencendo nos próximos 30 dias.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

{{-- Documentos vencidos --}}
@if($expired->count())
<div class="card mb-6">
    <div class="card-header">
        <span class="card-title" style="color:#b71c1c;">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="width:16px;height:16px;color:#e53935;margin-right:4px;vertical-align:middle;"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
            Documentos Vencidos ({{ $expired->count() }})
        </span>
        <a href="{{ route('documents.index', ['status' => 'vencido']) }}" class="btn btn-ghost btn-sm">Ver todos</a>
    </div>
    <div class="table-wrapper">
        <table class="data-table">
            <thead>
                <tr><th>Documento</th><th>Venceu em</th><th></th></tr>
            </thead>
            <tbody>
                @foreach($expired->take(5) as $doc)
                    <tr>
                        <td style="font-weight:500;">{{ $doc->documentType->name }}</td>
                        <td>
                            <span class="badge badge-vencido">
                                <span class="badge-dot"></span>
                                {{ $doc->expires_at->format('d/m/Y') }}
                            </span>
                        </td>
                        <td>
                            <a href="{{ route('documents.create') }}?type_id={{ $doc->document_type_id }}" class="btn btn-primary btn-sm">Renovar</a>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endif

{{-- Catálogo resumo --}}
<div class="card">
    <div class="card-header">
        <span class="card-title">Catálogo de Documentos</span>
        <a href="{{ route('document-types.index') }}" class="btn btn-ghost btn-sm">Gerenciar catálogo</a>
    </div>
    <div class="card-body">
        @foreach($catalog as $cat => $count)
            @php
                $label = match($cat) {
                    'juridico'  => 'Jurídico / Institucional',
                    'federal'   => 'Certidões Federais',
                    'estadual'  => 'Certidões Estaduais (PE)',
                    'municipal' => 'Certidões Municipais',
                    'contabil'  => 'Contábil / Financeiro',
                    'titulacao' => 'Titulações e Registros',
                    'pessoal'   => 'Documentos Pessoais',
                    default     => ucfirst($cat),
                };
            @endphp
            <span style="display:inline-flex;align-items:center;gap:6px;background:var(--azul-light);color:var(--azul-dark);font-size:12px;font-weight:500;padding:5px 12px;border-radius:20px;margin:4px 4px 4px 0;">
                {{ $label }}
                <span style="background:var(--azul);color:#fff;border-radius:50%;width:18px;height:18px;display:flex;align-items:center;justify-content:center;font-size:10px;">{{ $count }}</span>
            </span>
        @endforeach
    </div>
</div>

@endsection
