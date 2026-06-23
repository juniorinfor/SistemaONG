@extends('layouts.app')
@section('page-title', 'Ações')
@section('page-subtitle', 'Execução de atividades')

@section('content')

<div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:20px;flex-wrap:wrap;gap:12px;">
    <div></div>
    <a href="{{ route('acoes.create') }}" class="btn btn-primary">+ Nova ação</a>
</div>

{{-- Filtros --}}
<form method="GET" style="background:#fff;border:1px solid var(--cinza-borda);border-radius:10px;padding:14px 16px;margin-bottom:20px;">
    <div style="display:flex;gap:8px;flex-wrap:wrap;align-items:center;">
        <input type="text" name="q" placeholder="Buscar por título..." value="{{ request('q') }}"
               style="flex:1;min-width:180px;border:1px solid var(--cinza-borda);border-radius:8px;padding:7px 11px;font-size:13px;font-family:'Poppins',sans-serif;">
        <select name="status" style="border:1px solid var(--cinza-borda);border-radius:8px;padding:7px 11px;font-size:13px;font-family:'Poppins',sans-serif;">
            <option value="">Todos os status</option>
            <option value="planejada"    {{ request('status') === 'planejada'    ? 'selected' : '' }}>Planejada</option>
            <option value="em_andamento" {{ request('status') === 'em_andamento' ? 'selected' : '' }}>Em andamento</option>
            <option value="concluida"    {{ request('status') === 'concluida'    ? 'selected' : '' }}>Concluída</option>
            <option value="cancelada"    {{ request('status') === 'cancelada'    ? 'selected' : '' }}>Cancelada</option>
        </select>
        <select name="tipo" style="border:1px solid var(--cinza-borda);border-radius:8px;padding:7px 11px;font-size:13px;font-family:'Poppins',sans-serif;">
            <option value="">Todos os tipos</option>
            <option value="oficina"               {{ request('tipo') === 'oficina'               ? 'selected' : '' }}>Oficina</option>
            <option value="palestra"              {{ request('tipo') === 'palestra'              ? 'selected' : '' }}>Palestra</option>
            <option value="atendimento_individual"{{ request('tipo') === 'atendimento_individual'? 'selected' : '' }}>Atendimento Individual</option>
            <option value="grupo"                 {{ request('tipo') === 'grupo'                 ? 'selected' : '' }}>Grupo</option>
            <option value="capacitacao"           {{ request('tipo') === 'capacitacao'           ? 'selected' : '' }}>Capacitação</option>
            <option value="evento"                {{ request('tipo') === 'evento'                ? 'selected' : '' }}>Evento</option>
            <option value="visita_domiciliar"     {{ request('tipo') === 'visita_domiciliar'     ? 'selected' : '' }}>Visita Domiciliar</option>
            <option value="reuniao"               {{ request('tipo') === 'reuniao'               ? 'selected' : '' }}>Reunião</option>
            <option value="outro"                 {{ request('tipo') === 'outro'                 ? 'selected' : '' }}>Outro</option>
        </select>
        <select name="project_id" style="border:1px solid var(--cinza-borda);border-radius:8px;padding:7px 11px;font-size:13px;font-family:'Poppins',sans-serif;">
            <option value="">Todos os projetos</option>
            <option value="nenhum" {{ request('project_id') === 'nenhum' ? 'selected' : '' }}>Sem projeto</option>
            @foreach($projects as $p)
                <option value="{{ $p->id }}" {{ request('project_id') == $p->id ? 'selected' : '' }}>{{ $p->title }}</option>
            @endforeach
        </select>
        <button class="btn btn-primary btn-sm" type="submit">Filtrar</button>
        <a href="{{ route('acoes.index') }}" class="btn btn-ghost btn-sm">Limpar</a>
    </div>
</form>

<div style="display:flex;flex-direction:column;gap:12px;">
    @forelse($acoes as $acao)
    @php $totalSessoes = $acao->sessoes->count(); @endphp
    <div class="card" style="overflow:visible;">
        <div style="padding:16px 20px;display:flex;align-items:flex-start;gap:16px;">

            {{-- Ícone tipo --}}
            <div style="width:44px;height:44px;background:{{ $acao->status_bg }};border-radius:10px;display:flex;align-items:center;justify-content:center;font-size:20px;flex-shrink:0;">
                {{ match($acao->tipo) { 'oficina'=>'🛠', 'palestra'=>'🎤', 'atendimento_individual'=>'👤', 'grupo'=>'👥', 'capacitacao'=>'📚', 'evento'=>'🎉', 'visita_domiciliar'=>'🏠', 'reuniao'=>'💬', default=>'📋' } }}
            </div>

            {{-- Corpo --}}
            <div style="flex:1;min-width:0;">
                <div style="display:flex;align-items:center;gap:8px;margin-bottom:4px;flex-wrap:wrap;">
                    <a href="{{ route('acoes.show', $acao) }}" style="font-size:14px;font-weight:700;color:var(--texto);text-decoration:none;">{{ $acao->titulo }}</a>
                    <span style="font-size:11px;font-weight:600;padding:2px 9px;border-radius:20px;background:{{ $acao->status_bg }};color:{{ $acao->status_color }};">{{ $acao->status_label }}</span>
                    <span style="font-size:11px;padding:2px 8px;border-radius:20px;background:#f3e5f5;color:#6a1b9a;">{{ $acao->tipo_label }}</span>
                </div>

                @if($acao->descricao)
                    <p style="font-size:12.5px;color:var(--cinza-light);margin-bottom:6px;line-height:1.5;display:-webkit-box;-webkit-line-clamp:2;-webkit-box-orient:vertical;overflow:hidden;">{{ $acao->descricao }}</p>
                @endif

                <div style="display:flex;gap:16px;flex-wrap:wrap;">
                    @if($acao->local)
                        <span style="font-size:12px;color:var(--cinza-light);">📍 {{ $acao->local }}</span>
                    @endif
                    @if($acao->responsavel_nome)
                        <span style="font-size:12px;color:var(--cinza-light);">👤 {{ $acao->responsavel_nome }}</span>
                    @endif
                    @if($acao->project)
                        <span style="font-size:12px;color:var(--teal);">📁 {{ $acao->project->title }}</span>
                    @endif
                    <span style="font-size:12px;color:var(--cinza-light);">📅 {{ $totalSessoes }} {{ $totalSessoes === 1 ? 'sessão' : 'sessões' }}</span>
                </div>
            </div>

            {{-- Ações --}}
            <div style="display:flex;flex-direction:column;gap:6px;flex-shrink:0;">
                <a href="{{ route('acoes.show', $acao) }}" class="btn btn-ghost btn-sm">Ver</a>
                <a href="{{ route('acoes.relatorio', $acao) }}" class="btn btn-ghost btn-sm" style="font-size:11px;">Relatório</a>
            </div>
        </div>
    </div>
    @empty
    <div class="card">
        <div class="card-body" style="text-align:center;padding:48px;color:var(--cinza-light);">
            <div style="font-size:32px;margin-bottom:12px;">📋</div>
            <p style="font-size:15px;font-weight:500;margin-bottom:6px;">Nenhuma ação cadastrada</p>
            <a href="{{ route('acoes.create') }}" class="btn btn-primary" style="margin-top:12px;">Criar primeira ação</a>
        </div>
    </div>
    @endforelse
</div>

<div style="margin-top:16px;">{{ $acoes->links() }}</div>
@endsection
