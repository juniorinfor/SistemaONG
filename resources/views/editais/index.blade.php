@extends('layouts.app')

@section('page-title', 'Radar de Editais')

@push('styles')
<style>
.edital-card {
    background: #fff;
    border: 1px solid var(--cinza-borda);
    border-radius: 10px;
    padding: 16px 20px;
    margin-bottom: 12px;
    display: flex;
    gap: 16px;
    align-items: flex-start;
    transition: box-shadow .15s;
    overflow: hidden;
    min-width: 0;
}
.edital-card:hover { box-shadow: 0 3px 12px rgba(0,0,0,.08); }
.edital-prazo-badge {
    flex-shrink: 0;
    width: 60px;
    text-align: center;
    border-radius: 8px;
    padding: 8px 4px;
    font-size: 11px;
    font-weight: 700;
    line-height: 1.2;
}
.prazo-ok     { background: #e8f8f5; color: #00897b; }
.prazo-breve  { background: #fff8e6; color: #e65100; }
.prazo-urgente{ background: #fce4e4; color: #c62828; }
.prazo-encerrado { background: #f5f5f5; color: #9e9e9e; }
.prazo-sem_prazo { background: #e3f2fd; color: #1565c0; }
.edital-body { flex: 1; min-width: 0; }
.edital-titulo {
    font-size: 14px; font-weight: 600; color: var(--texto);
    margin-bottom: 4px; line-height: 1.4;
    overflow: hidden; text-overflow: ellipsis;
    display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical;
}
.edital-resumo {
    font-size: 12px; color: var(--cinza-light);
    margin-bottom: 8px; line-height: 1.5;
    display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden;
}
.edital-tags { display: flex; flex-wrap: wrap; gap: 6px; align-items: center; }
.tag {
    font-size: 11px; padding: 2px 9px; border-radius: 20px;
    border: 1px solid var(--cinza-borda); color: var(--cinza-light);
}
.tag-area    { background: #e3f2fd; border-color: #bbdefb; color: #1565c0; }
.tag-fonte   { background: #f3e5f5; border-color: #e1bee7; color: #6a1b9a; }
.tag-valor   { background: #e8f5e9; border-color: #c8e6c9; color: #2e7d32; }
.compat-bar  { display: flex; align-items: center; gap: 6px; }
.compat-pct  { font-size: 11px; font-weight: 700; }
.compat-pct.high { color: #00897b; }
.compat-pct.mid  { color: #e65100; }
.compat-pct.low  { color: #c62828; }
.mini-bar    { flex: 1; max-width: 60px; height: 4px; background: #eee; border-radius: 4px; overflow: hidden; }
.mini-bar-fill { height: 100%; border-radius: 4px; }
.edital-actions { display: flex; flex-direction: column; gap: 6px; align-items: flex-end; flex-shrink: 0; }
.filter-row {
    display: flex; gap: 10px; flex-wrap: wrap;
    margin-bottom: 20px; align-items: center;
    max-width: 100%; overflow: hidden;
}
.filter-row input, .filter-row select {
    border: 1px solid var(--cinza-borda); border-radius: 8px;
    padding: 8px 12px; font-size: 13px;
    font-family: 'Roboto', sans-serif; background: #fff; color: var(--texto);
    max-width: 100%;
}
.filter-row input { flex: 1; min-width: 160px; }
.filter-row input:focus, .filter-row select:focus {
    outline: none; border-color: var(--teal);
}
.sync-bar {
    display: flex; align-items: center; justify-content: space-between;
    margin-bottom: 20px; gap: 12px; flex-wrap: wrap;
}
.sync-info { font-size: 12px; color: var(--cinza-light); }
.fonte-labels { 'transferegov':'Gov Federal', 'iati':'Internacional', 'dou':'Diário Oficial', 'manual':'Manual' }
</style>
@endpush

@section('content')

<div class="sync-bar">
    <div>
        <h1 style="font-size:20px;font-weight:700;color:var(--texto);margin:0;">Radar de Editais</h1>
        @if($lastSync)
            <span class="sync-info">Última atualização: {{ \Carbon\Carbon::parse($lastSync)->format('d/m/Y H:i') }}</span>
        @else
            <span class="sync-info">Nunca sincronizado</span>
        @endif
    </div>
    <div style="display:flex;gap:8px;">
        <a href="{{ route('editais.create') }}" class="btn btn-ghost btn-sm">+ Cadastrar manualmente</a>
        <a href="{{ route('editais.sync') }}"
           onclick="this.innerHTML='Buscando...'; this.style.opacity='.7'; this.style.pointerEvents='none';"
           class="btn btn-primary btn-sm">
           ↻ Atualizar agora
        </a>
    </div>
</div>

{{-- Filtros --}}
<form method="GET" action="{{ route('editais.index') }}" class="filter-row">
    <input type="text" name="q" placeholder="Buscar por título..." value="{{ request('q') }}">
    <select name="status">
        <option value="">Abertos</option>
        <option value="encerrados" {{ request('status') === 'encerrados' ? 'selected' : '' }}>Encerrados</option>
    </select>
    <select name="area">
        <option value="">Todas as áreas</option>
        @foreach($areas as $area)
            <option value="{{ $area }}" {{ request('area') === $area ? 'selected' : '' }}>
                {{ ucfirst($area) }}
            </option>
        @endforeach
    </select>
    <select name="fonte">
        <option value="">Todas as fontes</option>
        <option value="transferegov"  {{ request('fonte') === 'transferegov'  ? 'selected' : '' }}>Gov Federal</option>
        <option value="iati"          {{ request('fonte') === 'iati'          ? 'selected' : '' }}>Internacional</option>
        <option value="querido_diario"{{ request('fonte') === 'querido_diario'? 'selected' : '' }}>Diário Oficial</option>
        <option value="dados_gov"     {{ request('fonte') === 'dados_gov'     ? 'selected' : '' }}>Dados.gov.br</option>
        <option value="undp"          {{ request('fonte') === 'undp'          ? 'selected' : '' }}>UNDP / ONU</option>
        <option value="eu_grants"     {{ request('fonte') === 'eu_grants'     ? 'selected' : '' }}>EU Grants</option>
        <option value="manual"        {{ request('fonte') === 'manual'        ? 'selected' : '' }}>Manual</option>
    </select>
    <button class="btn btn-ghost btn-sm" type="submit">Filtrar</button>
    <a href="{{ route('editais.index') }}" class="btn btn-ghost btn-sm">Limpar</a>
</form>

@if(session('success'))
    <div class="alert alert-success" style="margin-bottom:16px;">{{ session('success') }}</div>
@endif

{{-- Lista --}}
@forelse($editais as $edital)
@php
    $ps = $edital->prazo_status;
    $prazoLabel = match($ps) {
        'urgente'   => ($edital->prazo_inscricao ? $edital->prazo_inscricao->diffInDays(now()) . 'd' : '!'),
        'breve'     => ($edital->prazo_inscricao ? $edital->prazo_inscricao->diffInDays(now()) . 'd' : '~'),
        'ok'        => $edital->prazo_inscricao?->format('d/m'),
        'encerrado' => 'Enc.',
        default     => '—',
    };
    $prazoSub = match($ps) {
        'urgente'   => 'URGENTE',
        'breve'     => 'Em breve',
        'ok'        => $edital->prazo_inscricao?->format('Y'),
        'encerrado' => 'Encerrado',
        default     => 'Sem prazo',
    };
    $score = $edital->compatibility_score;
    $fonteLabel = match($edital->fonte) {
        'transferegov'  => 'Gov Federal',
        'iati'          => 'Internacional',
        'querido_diario'=> 'Diário Oficial',
        'dados_gov'     => 'Dados.gov.br',
        'dou'           => 'Diário Oficial',
        'undp'          => 'UNDP / ONU',
        'eu_grants'     => 'EU Grants',
        default         => 'Manual',
    };
@endphp
<div class="edital-card">
    {{-- Prazo --}}
    <div class="edital-prazo-badge prazo-{{ $ps }}">
        <div style="font-size:15px;">{{ $prazoLabel }}</div>
        <div>{{ $prazoSub }}</div>
    </div>

    {{-- Corpo --}}
    <div class="edital-body">
        <div class="edital-titulo" title="{{ $edital->titulo }}">{{ $edital->titulo }}</div>
        @if($edital->resumo)
            <div class="edital-resumo">{{ $edital->resumo }}</div>
        @endif
        <div class="edital-tags">
            @if($edital->area)
                <span class="tag tag-area">{{ ucfirst($edital->area) }}</span>
            @endif
            <span class="tag tag-fonte">{{ $fonteLabel }}</span>
            @if($edital->valor_formatado)
                <span class="tag tag-valor">{{ $edital->valor_formatado }}</span>
            @endif
            @if($score !== null)
                @php $cls = $score >= 70 ? 'high' : ($score >= 40 ? 'mid' : 'low'); @endphp
                <div class="compat-bar">
                    <div class="mini-bar">
                        <div class="mini-bar-fill"
                             style="width:{{ $score }}%;background:{{ $score>=70?'#00BAA3':($score>=40?'#FFAC00':'#e53935') }};"></div>
                    </div>
                    <span class="compat-pct {{ $cls }}">{{ $score }}% compat.</span>
                </div>
            @endif
        </div>
    </div>

    {{-- Ações --}}
    <div class="edital-actions">
        <a href="{{ route('editais.show', $edital) }}" class="btn btn-ghost btn-sm">Ver detalhes</a>
        @if($edital->link_oficial)
            <a href="{{ $edital->link_oficial }}" target="_blank" class="btn btn-ghost btn-sm" style="font-size:11px;">↗ Site oficial</a>
        @endif
    </div>
</div>
@empty
<div class="card">
    <div class="card-body" style="text-align:center;padding:48px;color:var(--cinza-light);">
        <div style="font-size:32px;margin-bottom:12px;">🔍</div>
        <p style="font-size:15px;font-weight:500;margin-bottom:6px;">Nenhum edital encontrado</p>
        <p style="font-size:13px;">
            Clique em <strong>Atualizar agora</strong> para buscar editais disponíveis,
            ou <a href="{{ route('editais.create') }}" style="color:var(--teal);">cadastre manualmente</a>.
        </p>
    </div>
</div>
@endforelse

{{ $editais->links() }}

@endsection
