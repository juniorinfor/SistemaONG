@extends('layouts.app')

@section('page-title', 'Documentos')
@section('page-subtitle', 'Versões vigentes')

@section('content')

<div class="card mb-4">
    <div class="card-body" style="padding:16px 22px;">
        <form method="GET" style="display:flex;gap:10px;flex-wrap:wrap;align-items:flex-end;">
            <div style="flex:1;min-width:200px;">
                <label class="form-label">Buscar</label>
                <input type="text" name="search" class="form-control" placeholder="Nome do documento..." value="{{ request('search') }}">
            </div>
            <div style="min-width:160px;">
                <label class="form-label">Categoria</label>
                <select name="category" class="form-control">
                    <option value="">Todas</option>
                    @foreach($categories as $cat)
                        @php
                            $catLabel = match($cat) {
                                'juridico'  => 'Jurídico',
                                'federal'   => 'Federal',
                                'estadual'  => 'Estadual',
                                'municipal' => 'Municipal',
                                'contabil'  => 'Contábil',
                                'titulacao' => 'Titulações',
                                'pessoal'   => 'Pessoal',
                                default     => ucfirst($cat),
                            };
                        @endphp
                        <option value="{{ $cat }}" {{ request('category') == $cat ? 'selected' : '' }}>{{ $catLabel }}</option>
                    @endforeach
                </select>
            </div>
            <div style="min-width:160px;">
                <label class="form-label">Status</label>
                <select name="status" class="form-control">
                    <option value="">Todos</option>
                    <option value="valido"         {{ request('status')=='valido'         ? 'selected':'' }}>Válido</option>
                    <option value="vence_em_breve" {{ request('status')=='vence_em_breve' ? 'selected':'' }}>Vencendo em breve</option>
                    <option value="vencido"        {{ request('status')=='vencido'        ? 'selected':'' }}>Vencido</option>
                </select>
            </div>
            <div style="display:flex;gap:8px;">
                <button type="submit" class="btn btn-blue btn-sm">Filtrar</button>
                <a href="{{ route('documents.index') }}" class="btn btn-ghost btn-sm">Limpar</a>
            </div>
        </form>
    </div>
</div>

<div class="card">
    <div class="card-header">
        <span class="card-title">{{ $documents->total() }} documento(s)</span>
        <a href="{{ route('documents.create') }}" class="btn btn-primary btn-sm">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" style="width:14px;height:14px;"><path d="M12 5v14M5 12h14"/></svg>
            Enviar Documento
        </a>
    </div>
    <div class="table-wrapper">
        <table class="data-table">
            <thead>
                <tr>
                    <th>Documento</th>
                    <th>Categoria</th>
                    <th>Pessoa</th>
                    <th>Validade</th>
                    <th>Status</th>
                    <th style="width:110px;"></th>
                </tr>
            </thead>
            <tbody>
                @forelse($documents as $doc)
                    @php
                        $statusClass = match($doc->status) {
                            'valido'         => 'badge-valido',
                            'vence_em_breve' => 'badge-em-breve',
                            'vencido'        => 'badge-vencido',
                            default          => 'badge-faltante',
                        };
                        $sLabel = match($doc->status) {
                            'valido'         => 'Válido',
                            'vence_em_breve' => 'Vencendo',
                            'vencido'        => 'Vencido',
                            default          => 'Sem validade',
                        };
                        $catLabel = match($doc->documentType->category) {
                            'juridico'  => 'Jurídico',
                            'federal'   => 'Federal',
                            'estadual'  => 'Estadual',
                            'municipal' => 'Municipal',
                            'contabil'  => 'Contábil',
                            'titulacao' => 'Titulações',
                            'pessoal'   => 'Pessoal',
                            default     => ucfirst($doc->documentType->category),
                        };
                    @endphp
                    <tr>
                        <td>
                            <div style="font-weight:500;color:var(--texto);">{{ $doc->documentType->name }}</div>
                            @if($doc->protocol_number)
                                <div style="font-size:11px;color:var(--cinza-light);margin-top:2px;">Protocolo: {{ $doc->protocol_number }}</div>
                            @endif
                        </td>
                        <td>
                            <span style="background:var(--azul-light);color:var(--azul-dark);font-size:11px;font-weight:500;padding:2px 9px;border-radius:20px;">
                                {{ $catLabel }}
                            </span>
                        </td>
                        <td style="color:var(--cinza-light);font-size:13px;">{{ $doc->person?->name ?? '—' }}</td>
                        <td style="font-size:13px;">
                            @if($doc->expires_at)
                                {{ $doc->expires_at->format('d/m/Y') }}
                                @if($doc->status === 'vence_em_breve')
                                    <div style="font-size:11px;color:var(--amarelo);">em {{ now()->diffInDays($doc->expires_at) }}d</div>
                                @elseif($doc->status === 'vencido')
                                    <div style="font-size:11px;color:#e53935;">{{ $doc->expires_at->diffForHumans() }}</div>
                                @endif
                            @else
                                <span style="color:var(--cinza-light);">Sem vencimento</span>
                            @endif
                        </td>
                        <td>
                            <span class="badge {{ $statusClass }}">
                                <span class="badge-dot"></span>
                                {{ $sLabel }}
                            </span>
                        </td>
                        <td>
                            <div style="display:flex;gap:6px;">
                                <a href="{{ route('documents.show', $doc) }}" class="btn btn-ghost btn-sm">Ver</a>
                                <a href="{{ route('documents.download', $doc) }}" class="btn btn-ghost btn-sm" title="Baixar">
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="width:13px;height:13px;"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/></svg>
                                </a>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" style="text-align:center;padding:40px;color:var(--cinza-light);">
                            Nenhum documento encontrado.
                            <a href="{{ route('documents.create') }}" style="color:var(--teal);margin-left:6px;">Enviar o primeiro</a>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($documents->hasPages())
        <div style="padding:16px 22px;border-top:1px solid var(--borda);">
            {{ $documents->links() }}
        </div>
    @endif
</div>

@endsection
