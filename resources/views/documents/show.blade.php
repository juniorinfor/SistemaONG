@extends('layouts.app')

@section('page-title', $document->documentType->name)
@section('page-subtitle', 'Detalhes do documento')

@section('content')

@php
    $statusClass = match($document->status) {
        'valido'         => 'badge-valido',
        'vence_em_breve' => 'badge-em-breve',
        'vence_urgente'  => 'badge-urgente',
        'vence_critico'  => 'badge-critico',
        'vencido'        => 'badge-vencido',
        default          => 'badge-faltante',
    };
    $sLabel = $document->statusLabel;
@endphp

<div style="display:grid;grid-template-columns:1fr 340px;gap:20px;max-width:1000px;">

{{-- Coluna principal --}}
<div>

    {{-- Card informações --}}
    <div class="card mb-4">
        <div class="card-header">
            <span class="card-title">Informações</span>
            <div style="display:flex;gap:8px;">
                <a href="{{ route('documents.download', $document) }}" class="btn btn-primary btn-sm">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="width:13px;height:13px;"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/></svg>
                    Baixar
                </a>
                <a href="{{ route('documents.edit', $document) }}" class="btn btn-ghost btn-sm">Editar</a>
            </div>
        </div>
        <div class="card-body">

            <div style="display:grid;grid-template-columns:1fr 1fr;gap:0;">
                @php
                    $fields = [
                        ['Tipo',      $document->documentType->name],
                        ['Status',    null],
                        ['Pessoa',    $document->person?->name ?? '—'],
                        ['Arquivo',   $document->original_filename],
                        ['Emitido',   $document->issued_at?->format('d/m/Y') ?? '—'],
                        ['Vence',     $document->expires_at?->format('d/m/Y') ?? 'Sem vencimento'],
                        ['Protocolo', $document->protocol_number ?? '—'],
                        ['Público',   $document->is_public ? 'Sim (portal público)' : 'Não'],
                        ['Enviado',   $document->created_at->format('d/m/Y H:i') . ' por ' . ($document->uploader?->name ?? '—')],
                    ];
                @endphp

                @foreach($fields as $i => [$label, $value])
                <div style="padding:14px 0;{{ $i > 0 ? 'border-top:1px solid var(--borda);' : '' }}grid-column:{{ $label === 'Enviado' ? '1 / span 2' : 'auto' }};">
                    <div style="font-size:11px;font-weight:600;text-transform:uppercase;letter-spacing:.05em;color:var(--cinza-light);margin-bottom:4px;">{{ $label }}</div>
                    @if($label === 'Status')
                        <span class="badge {{ $statusClass }}"><span class="badge-dot"></span>{{ $sLabel }}</span>
                    @else
                        <div style="font-size:13px;color:var(--texto);">{{ $value }}</div>
                    @endif
                </div>
                @endforeach
            </div>

            @if($document->notes)
                <div style="margin-top:16px;padding-top:16px;border-top:1px solid var(--borda);">
                    <div style="font-size:11px;font-weight:600;text-transform:uppercase;letter-spacing:.05em;color:var(--cinza-light);margin-bottom:6px;">Observações</div>
                    <div style="font-size:13px;color:var(--texto);line-height:1.6;">{{ $document->notes }}</div>
                </div>
            @endif

        </div>
    </div>

    {{-- Histórico de versões --}}
    @if($history->count() > 1)
    <div class="card">
        <div class="card-header">
            <span class="card-title">Histórico de versões</span>
        </div>
        <div class="table-wrapper">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Enviado em</th>
                        <th>Arquivo</th>
                        <th>Vencimento</th>
                        <th>Versão</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($history as $version)
                    <tr style="{{ $version->is_current ? 'background:var(--teal-light);' : 'opacity:.65;' }}">
                        <td style="font-size:12px;">{{ $version->created_at->format('d/m/Y H:i') }}</td>
                        <td style="font-size:12px;">{{ $version->original_filename }}</td>
                        <td style="font-size:12px;">{{ $version->expires_at?->format('d/m/Y') ?? '—' }}</td>
                        <td>
                            @if($version->is_current)
                                <span style="font-size:11px;font-weight:600;color:var(--teal);">VIGENTE</span>
                            @else
                                <span style="font-size:11px;color:var(--cinza-light);">anterior</span>
                            @endif
                        </td>
                        <td>
                            <a href="{{ route('documents.download', $version) }}" class="btn btn-ghost btn-sm" title="Baixar">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="width:13px;height:13px;"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/></svg>
                            </a>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    @endif

</div>

{{-- Coluna lateral --}}
<div>

    {{-- Sobre o tipo --}}
    <div class="card mb-4">
        <div class="card-header">
            <span class="card-title">Sobre este documento</span>
        </div>
        <div class="card-body">
            @if($document->documentType->instructions)
                <div style="font-size:12.5px;color:var(--cinza);line-height:1.65;">
                    {{ $document->documentType->instructions }}
                </div>
            @else
                <p style="font-size:12px;color:var(--cinza-light);">Sem instruções cadastradas.</p>
            @endif

            @if($document->documentType->official_url)
                <a href="{{ $document->documentType->official_url }}" target="_blank" rel="noopener"
                   class="btn btn-blue btn-sm" style="margin-top:14px;display:inline-flex;align-items:center;gap:6px;">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="width:12px;height:12px;"><path d="M18 13v6a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h6"/><polyline points="15 3 21 3 21 9"/><line x1="10" y1="14" x2="21" y2="3"/></svg>
                    Site oficial
                </a>
            @endif

            @if($document->documentType->validity_days)
                <div style="margin-top:12px;padding:10px 12px;background:var(--azul-light);border-radius:8px;font-size:12px;color:var(--azul-dark);">
                    Validade padrão: <strong>{{ $document->documentType->validity_days }} dias</strong>
                </div>
            @endif
        </div>
    </div>

    {{-- Ações --}}
    <div class="card">
        <div class="card-body" style="display:flex;flex-direction:column;gap:10px;">
            <a href="{{ route('documents.create') }}?type_id={{ $document->document_type_id }}" class="btn btn-primary btn-sm" style="text-align:center;">
                Enviar nova versão
            </a>
            <a href="{{ route('documents.edit', $document) }}" class="btn btn-ghost btn-sm" style="text-align:center;">
                Editar metadados
            </a>
            <form method="POST" action="{{ route('documents.destroy', $document) }}"
                  onsubmit="return confirm('Remover este documento? Esta ação não pode ser desfeita.')">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn btn-ghost btn-sm" style="width:100%;color:#e53935;border-color:transparent;">
                    Remover documento
                </button>
            </form>
        </div>
    </div>

</div>

</div>

@endsection
