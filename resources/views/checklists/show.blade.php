@extends('layouts.app')

@section('page-title', $checklist->name)
@section('page-subtitle', 'Detalhes do checklist')

@section('content')

@php
    $color     = $pct >= 80 ? 'var(--teal)' : ($pct >= 50 ? 'var(--amarelo)' : '#e53935');
    $fillClass = $pct < 50 ? 'danger' : ($pct < 80 ? 'warn' : '');
@endphp

{{-- Header resumo --}}
<div class="card mb-4">
    <div class="card-body" style="display:flex;align-items:center;gap:24px;flex-wrap:wrap;">
        <div style="flex:1;min-width:200px;">
            <div style="font-family:'Roboto',sans-serif;font-size:20px;font-weight:700;color:var(--texto);">{{ $checklist->name }}</div>
            @if($checklist->description)
                <div style="font-size:13px;color:var(--cinza-light);margin-top:4px;">{{ $checklist->description }}</div>
            @endif
            @if($checklist->legal_basis)
                <div style="font-size:12px;color:var(--azul-dark);margin-top:6px;background:var(--azul-light);padding:4px 10px;border-radius:6px;display:inline-block;">{{ $checklist->legal_basis }}</div>
            @endif
        </div>
        <div style="text-align:center;min-width:100px;">
            <div style="font-size:48px;font-weight:700;font-family:'Roboto',sans-serif;color:{{ $color }};line-height:1;">{{ $pct }}%</div>
            <div style="font-size:12px;color:var(--cinza-light);margin-top:4px;">prontidão</div>
            <div class="progress-bar-wrap" style="margin-top:8px;height:8px;">
                <div class="progress-bar-fill {{ $fillClass }}" style="width:{{ $pct }}%;"></div>
            </div>
        </div>
    </div>
</div>

{{-- Tabela de itens --}}
<div class="card">
    <div class="card-header">
        <span class="card-title">Itens do Checklist ({{ $checklist->items->count() }})</span>
    </div>
    <div class="table-wrapper">
        <table class="data-table">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Documento</th>
                    <th>Obrigatório</th>
                    <th>Status</th>
                    <th>Vencimento</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                @foreach($checklist->items as $item)
                    @php
                        $doc = $currentDocs->get($item->document_type_id);
                        if ($doc) {
                            $statusClass = match($doc->status) {
                                'valido'         => 'badge-valido',
                                'vence_em_breve' => 'badge-em-breve',
                                'vencido'        => 'badge-vencido',
                                default          => 'badge-faltante',
                            };
                            $statusLabel = match($doc->status) {
                                'valido'         => 'Válido',
                                'vence_em_breve' => 'Vencendo',
                                'vencido'        => 'Vencido',
                                default          => 'Sem validade',
                            };
                        }
                    @endphp
                    <tr>
                        <td style="color:var(--cinza-light);font-size:12px;">{{ $item->sort_order }}</td>
                        <td>
                            <div style="font-weight:500;">{{ $item->documentType->name }}</div>
                            @if($item->documentType->official_url)
                                <a href="{{ $item->documentType->official_url }}" target="_blank" rel="noopener"
                                   style="font-size:11px;color:var(--azul-dark);">site oficial</a>
                            @endif
                        </td>
                        <td>
                            @if($item->is_required)
                                <span style="font-size:11px;font-weight:600;color:#c62828;">Obrigatório</span>
                            @else
                                <span style="font-size:11px;color:var(--cinza-light);">Opcional</span>
                            @endif
                        </td>
                        <td>
                            @if($doc)
                                <span class="badge {{ $statusClass }}">
                                    <span class="badge-dot"></span>{{ $statusLabel }}
                                </span>
                            @else
                                <span class="badge badge-faltante">
                                    <span class="badge-dot"></span>Faltando
                                </span>
                            @endif
                        </td>
                        <td style="font-size:12px;color:var(--cinza-light);">
                            @if($doc && $doc->expires_at)
                                {{ $doc->expires_at->format('d/m/Y') }}
                            @else
                                —
                            @endif
                        </td>
                        <td>
                            @if($doc)
                                <div style="display:flex;gap:6px;">
                                    <a href="{{ route('documents.show', $doc) }}" class="btn btn-ghost btn-sm">Ver</a>
                                    <a href="{{ route('documents.create') }}?type_id={{ $item->document_type_id }}" class="btn btn-ghost btn-sm">Renovar</a>
                                </div>
                            @else
                                <a href="{{ route('documents.create') }}?type_id={{ $item->document_type_id }}" class="btn btn-primary btn-sm">Enviar</a>
                            @endif
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>

@endsection
