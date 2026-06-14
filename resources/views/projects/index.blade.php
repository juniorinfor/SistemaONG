@extends('layouts.app')

@section('page-title', 'Projetos')

@push('styles')
<style>
.project-card {
    background: #fff;
    border: 1px solid var(--cinza-borda);
    border-radius: 10px;
    padding: 18px 20px;
    margin-bottom: 10px;
    display: flex;
    gap: 16px;
    align-items: flex-start;
    transition: box-shadow .15s;
}
.project-card:hover { box-shadow: 0 3px 12px rgba(0,0,0,.08); }
.project-status-bar {
    width: 4px;
    border-radius: 4px;
    align-self: stretch;
    flex-shrink: 0;
}
.project-body { flex: 1; min-width: 0; }
.project-title {
    font-size: 14px;
    font-weight: 600;
    color: var(--texto);
    margin-bottom: 4px;
    line-height: 1.4;
}
.project-meta {
    font-size: 12px;
    color: var(--cinza-light);
    margin-bottom: 8px;
}
.project-tags { display: flex; flex-wrap: wrap; gap: 6px; align-items: center; }
.ptag {
    font-size: 11px;
    padding: 2px 9px;
    border-radius: 20px;
    border: 1px solid var(--cinza-borda);
    color: var(--cinza-light);
}
.ptag-area  { background: #e3f2fd; border-color: #bbdefb; color: #1565c0; }
.ptag-valor { background: #e8f5e9; border-color: #c8e6c9; color: #2e7d32; }
.project-actions { display: flex; flex-direction: column; gap: 6px; align-items: flex-end; flex-shrink: 0; }
.summary-row { display: flex; gap: 10px; flex-wrap: wrap; margin-bottom: 20px; }
.summary-chip {
    display: flex; align-items: center; gap: 6px;
    padding: 8px 16px; border-radius: 20px;
    font-size: 13px; font-weight: 500;
}
.chip-total  { background: #f5f5f5; color: var(--cinza); }
.chip-ok     { background: #e8f8f5; color: #00897b; }
.chip-exec   { background: #f3e5f5; color: #6a1b9a; }
.chip-done   { background: #e8f5e9; color: #2e7d32; }
.filter-row {
    display: flex; gap: 10px; flex-wrap: wrap;
    margin-bottom: 20px; align-items: center;
}
.filter-row input, .filter-row select {
    border: 1px solid var(--cinza-borda); border-radius: 8px;
    padding: 8px 12px; font-size: 13px;
    font-family: 'Roboto', sans-serif; background: #fff; color: var(--texto);
}
.filter-row input { flex: 1; min-width: 160px; }
.filter-row input:focus, .filter-row select:focus { outline: none; border-color: var(--teal); }
</style>
@endpush

@section('content')

<div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:20px;flex-wrap:wrap;gap:10px;">
    <div>
        <h1 style="font-size:20px;font-weight:700;color:var(--texto);margin:0;">Projetos</h1>
        <p style="font-size:13px;color:var(--cinza-light);margin:2px 0 0;">Gestão de projetos e captação de recursos</p>
    </div>
    <a href="{{ route('projects.create') }}" class="btn btn-primary btn-sm">+ Novo Projeto</a>
</div>

{{-- Resumo --}}
<div class="summary-row">
    <div class="summary-chip chip-total">📁 {{ $counts['total'] }} projetos</div>
    @if($counts['aprovados'])
    <div class="summary-chip chip-ok">✔ {{ $counts['aprovados'] }} aprovados</div>
    @endif
    @if($counts['execucao'])
    <div class="summary-chip chip-exec">▶ {{ $counts['execucao'] }} em execução</div>
    @endif
    @if($counts['concluidos'])
    <div class="summary-chip chip-done">★ {{ $counts['concluidos'] }} concluídos</div>
    @endif
</div>

{{-- Filtros --}}
<form method="GET" action="{{ route('projects.index') }}" class="filter-row">
    <input type="text" name="q" placeholder="Buscar por título..." value="{{ request('q') }}">
    <select name="status">
        <option value="">Todos os status</option>
        @foreach([
            'rascunho'      => 'Rascunho',
            'em_elaboracao' => 'Em elaboração',
            'submetido'     => 'Submetido',
            'aprovado'      => 'Aprovado',
            'reprovado'     => 'Reprovado',
            'em_execucao'   => 'Em execução',
            'concluido'     => 'Concluído',
            'cancelado'     => 'Cancelado',
        ] as $val => $lbl)
        <option value="{{ $val }}" {{ request('status') === $val ? 'selected' : '' }}>{{ $lbl }}</option>
        @endforeach
    </select>
    @if($areas->count())
    <select name="area">
        <option value="">Todas as áreas</option>
        @foreach($areas as $area)
        <option value="{{ $area }}" {{ request('area') === $area ? 'selected' : '' }}>{{ ucfirst($area) }}</option>
        @endforeach
    </select>
    @endif
    <button type="submit" class="btn btn-ghost btn-sm">Filtrar</button>
    <a href="{{ route('projects.index') }}" class="btn btn-ghost btn-sm">Limpar</a>
</form>

@if(session('success'))
<div class="alert alert-success" style="margin-bottom:16px;">{{ session('success') }}</div>
@endif

@forelse($projects as $project)
<div class="project-card">
    <div class="project-status-bar" style="background:{{ $project->statusColor }};"></div>
    <div class="project-body">
        <div class="project-title">{{ $project->title }}</div>
        <div class="project-meta">
            @if($project->edital)
                Vinculado: {{ Str::limit($project->edital->titulo, 60) }} ·
            @endif
            Criado em {{ $project->created_at->format('d/m/Y') }}
        </div>
        <div class="project-tags">
            <span style="font-size:11px;padding:2px 9px;border-radius:20px;background:{{ $project->statusBg }};color:{{ $project->statusColor }};font-weight:600;border:1px solid {{ $project->statusColor }}30;">
                {{ $project->statusLabel }}
            </span>
            @if($project->area)
                <span class="ptag ptag-area">{{ ucfirst($project->area) }}</span>
            @endif
            @if($project->valorPleiteadoFormatado)
                <span class="ptag ptag-valor">{{ $project->valorPleiteadoFormatado }}</span>
            @endif
            @if($project->end_date)
                <span class="ptag">Prazo: {{ $project->end_date->format('d/m/Y') }}</span>
            @endif
        </div>
    </div>
    <div class="project-actions">
        <a href="{{ route('projects.show', $project) }}" class="btn btn-ghost btn-sm">Ver</a>
        <a href="{{ route('projects.edit', $project) }}" class="btn btn-ghost btn-sm" style="font-size:11px;">Editar</a>
    </div>
</div>
@empty
<div class="card">
    <div class="card-body" style="text-align:center;padding:48px;color:var(--cinza-light);">
        <div style="font-size:32px;margin-bottom:12px;">📁</div>
        <p style="font-size:15px;font-weight:500;margin-bottom:6px;">Nenhum projeto cadastrado</p>
        <p style="font-size:13px;">
            <a href="{{ route('projects.create') }}" style="color:var(--teal);">Crie o primeiro projeto</a>
            ou vincule um edital do Radar.
        </p>
    </div>
</div>
@endforelse

{{ $projects->links() }}

@endsection
