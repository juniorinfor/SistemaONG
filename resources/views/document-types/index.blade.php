@extends('layouts.app')

@section('page-title', 'Catálogo de Documentos')

@push('styles')
<style>
.catalog-section { margin-bottom: 2rem; }
.catalog-section-title {
    font-size: 13px;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: .8px;
    color: var(--teal);
    padding: 8px 0 6px;
    border-bottom: 2px solid var(--teal);
    margin-bottom: 12px;
    display: flex;
    align-items: center;
    gap: 8px;
}
.catalog-section-title .count-badge {
    background: var(--teal);
    color: #fff;
    border-radius: 20px;
    font-size: 11px;
    padding: 1px 8px;
    font-weight: 600;
}
.doc-type-row {
    display: flex;
    align-items: center;
    gap: 12px;
    padding: 10px 12px;
    border-radius: 8px;
    border: 1px solid var(--cinza-borda);
    margin-bottom: 8px;
    background: #fff;
    transition: box-shadow .15s;
}
.doc-type-row:hover { box-shadow: 0 2px 8px rgba(0,0,0,.07); }
.doc-type-icon {
    width: 36px;
    height: 36px;
    border-radius: 8px;
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
    font-size: 16px;
}
.doc-type-icon.ok    { background: #e8f8f5; color: var(--teal); }
.doc-type-icon.miss  { background: #fff3e0; color: #f57c00; }
.doc-type-icon.exp   { background: #fce4e4; color: #c62828; }
.doc-type-name {
    flex: 1;
    font-size: 14px;
    font-weight: 500;
    color: var(--texto);
    line-height: 1.3;
}
.doc-type-meta {
    display: flex;
    align-items: center;
    gap: 8px;
    flex-shrink: 0;
}
.badge-validade {
    font-size: 11px;
    padding: 2px 8px;
    border-radius: 12px;
    border: 1px solid var(--cinza-borda);
    color: var(--cinza-light);
    white-space: nowrap;
}
.badge-per-person {
    font-size: 11px;
    padding: 2px 8px;
    border-radius: 12px;
    background: #e3f2fd;
    color: #1565c0;
    white-space: nowrap;
}
.badge-status-ok   { font-size:11px; padding:2px 9px; border-radius:12px; background:#e8f8f5; color:#00897b; font-weight:600; }
.badge-status-miss { font-size:11px; padding:2px 9px; border-radius:12px; background:#fff3e0; color:#e65100; font-weight:600; }
.badge-status-exp  { font-size:11px; padding:2px 9px; border-radius:12px; background:#fce4e4; color:#c62828; font-weight:600; }
.btn-enviar-sm {
    font-size: 12px;
    padding: 4px 12px;
    border-radius: 6px;
    background: var(--teal);
    color: #fff;
    border: none;
    cursor: pointer;
    text-decoration: none;
    white-space: nowrap;
}
.btn-enviar-sm:hover { opacity: .85; color: #fff; }
.btn-ver-sm {
    font-size: 12px;
    padding: 4px 12px;
    border-radius: 6px;
    background: transparent;
    color: var(--teal);
    border: 1px solid var(--teal);
    text-decoration: none;
    white-space: nowrap;
}
.btn-ver-sm:hover { background: var(--teal); color: #fff; }
.catalog-search-bar {
    display: flex;
    gap: 12px;
    margin-bottom: 24px;
    align-items: center;
}
.catalog-search-bar input {
    flex: 1;
    border: 1px solid var(--cinza-borda);
    border-radius: 8px;
    padding: 9px 14px;
    font-size: 14px;
    font-family: 'Roboto', sans-serif;
    outline: none;
}
.catalog-search-bar input:focus { border-color: var(--teal); }
.filter-btn {
    font-size: 13px;
    padding: 8px 14px;
    border-radius: 8px;
    border: 1px solid var(--cinza-borda);
    background: #fff;
    cursor: pointer;
    font-family: 'Roboto', sans-serif;
    color: var(--cinza-light);
}
.filter-btn.active {
    background: var(--teal);
    color: #fff;
    border-color: var(--teal);
}
.summary-bar {
    display: flex;
    gap: 12px;
    margin-bottom: 20px;
}
.summary-chip {
    display: flex;
    align-items: center;
    gap: 6px;
    padding: 6px 14px;
    border-radius: 20px;
    font-size: 13px;
    font-weight: 500;
}
.summary-chip.ok   { background: #e8f8f5; color: #00897b; }
.summary-chip.miss { background: #fff3e0; color: #e65100; }
.summary-chip.exp  { background: #fce4e4; color: #c62828; }
</style>
@endpush

@section('content')
@php
    $categoryLabels = [
        'juridico'  => ['Jurídico / Institucional', '📋'],
        'federal'   => ['Certidões Federais', '🏛️'],
        'estadual'  => ['Certidões Estaduais (PE)', '🏢'],
        'municipal' => ['Certidões Municipais', '🏙️'],
        'contabil'  => ['Contábil / Financeiro', '📊'],
        'titulacao' => ['Titulações e Registros', '🏅'],
        'pessoal'   => ['Documentos Pessoais', '👤'],
    ];

    $totalOk = 0; $totalMiss = 0; $totalExp = 0;
    foreach ($grouped as $cat => $types) {
        foreach ($types as $t) {
            $hasCurrent = $currentDocs->has($t->id);
            $currentDoc = $t->currentDocument->first();
            $isExp = $hasCurrent && $currentDoc && $currentDoc->expires_at && $currentDoc->expires_at->isPast();
            if (!$hasCurrent)         $totalMiss++;
            elseif ($isExp)           $totalExp++;
            else                      $totalOk++;
        }
    }
    $totalAll = $totalOk + $totalMiss + $totalExp;
@endphp

<div style="display:flex; align-items:center; justify-content:space-between; margin-bottom:16px;">
    <div>
        <h1 style="font-size:20px;font-weight:700;color:var(--texto);margin:0;">Catálogo de Documentos</h1>
        <p style="font-size:13px;color:var(--cinza-light);margin:2px 0 0;">{{ $totalAll }} tipos de documentos monitorados</p>
    </div>
    <a href="{{ route('documents.create') }}" class="btn btn-primary btn-sm">
        + Enviar Documento
    </a>
</div>

{{-- Resumo --}}
<div class="summary-bar">
    <div class="summary-chip ok">✔ {{ $totalOk }} enviados</div>
    <div class="summary-chip miss">⚠ {{ $totalMiss }} faltando</div>
    @if($totalExp > 0)
    <div class="summary-chip exp">✕ {{ $totalExp }} vencidos</div>
    @endif
</div>

{{-- Busca --}}
<div class="catalog-search-bar">
    <input type="text" id="catalogSearch" placeholder="Buscar por nome do documento..." autocomplete="off">
    <button class="filter-btn active" data-filter="all">Todos</button>
    <button class="filter-btn" data-filter="miss">Faltando</button>
    <button class="filter-btn" data-filter="ok">Enviados</button>
</div>

{{-- Lista por categoria --}}
@foreach($grouped as $cat => $types)
@php $label = $categoryLabels[$cat] ?? [ucfirst($cat), '📄']; @endphp
<div class="catalog-section" data-category="{{ $cat }}">
    <div class="catalog-section-title">
        <span>{{ $label[1] }} {{ $label[0] }}</span>
        <span class="count-badge">{{ $types->count() }}</span>
    </div>

    @foreach($types as $type)
    @php
        $hasCurrent = $currentDocs->has($type->id);
        $currentDoc = $type->currentDocument->first();
        $isExpired  = $hasCurrent && $currentDoc && $currentDoc->expires_at && $currentDoc->expires_at->isPast();
        $isSoon     = $hasCurrent && $currentDoc && $currentDoc->expires_at
                        && !$currentDoc->expires_at->isPast()
                        && $currentDoc->expires_at->diffInDays(now()) <= 30;

        $statusClass = !$hasCurrent ? 'miss' : ($isExpired ? 'exp' : 'ok');
        $statusLabel = !$hasCurrent ? 'Faltando' : ($isExpired ? 'Vencido' : ($isSoon ? 'Vence em breve' : 'OK'));
    @endphp
    <div class="doc-type-row"
         data-name="{{ strtolower($type->name) }}"
         data-status="{{ $statusClass }}">
        <div class="doc-type-icon {{ $statusClass }}">
            @if(!$hasCurrent) ⚠ @elseif($isExpired) ✕ @else ✔ @endif
        </div>
        <div class="doc-type-name">
            {{ $type->name }}
            @if($type->is_per_person)
                <span class="badge-per-person" title="Necessário por pessoa">Por pessoa</span>
            @endif
        </div>
        <div class="doc-type-meta">
            @if($type->validity_days)
                <span class="badge-validade">
                    Validade: {{ $type->validity_days >= 365
                        ? floor($type->validity_days/365).' ano(s)'
                        : $type->validity_days.' dias' }}
                </span>
            @else
                <span class="badge-validade">Sem vencimento</span>
            @endif
            <span class="badge-status-{{ $statusClass }}">{{ $statusLabel }}</span>
            @if($hasCurrent && $currentDoc)
                <a href="{{ route('documents.show', $currentDoc->id) }}" class="btn-ver-sm">Ver</a>
            @endif
            <a href="{{ route('documents.create', ['type_id' => $type->id]) }}" class="btn-enviar-sm">
                {{ $hasCurrent ? 'Renovar' : 'Enviar' }}
            </a>
        </div>
    </div>
    @endforeach
</div>
@endforeach
@endsection

@push('scripts')
<script>
const searchInput = document.getElementById('catalogSearch');
const filterBtns  = document.querySelectorAll('[data-filter]');
let activeFilter  = 'all';

function applyFilters() {
    const q = searchInput.value.toLowerCase().trim();
    document.querySelectorAll('.doc-type-row').forEach(row => {
        const matchName   = row.dataset.name.includes(q);
        const matchFilter = activeFilter === 'all' || row.dataset.status === activeFilter
                            || (activeFilter === 'miss' && (row.dataset.status === 'miss' || row.dataset.status === 'exp'));
        row.style.display = (matchName && matchFilter) ? '' : 'none';
    });
    // Oculta seção vazia
    document.querySelectorAll('.catalog-section').forEach(sec => {
        const visible = [...sec.querySelectorAll('.doc-type-row')].some(r => r.style.display !== 'none');
        sec.style.display = visible ? '' : 'none';
    });
}

searchInput.addEventListener('input', applyFilters);
filterBtns.forEach(btn => {
    btn.addEventListener('click', () => {
        filterBtns.forEach(b => b.classList.remove('active'));
        btn.classList.add('active');
        activeFilter = btn.dataset.filter;
        applyFilters();
    });
});
</script>
@endpush
