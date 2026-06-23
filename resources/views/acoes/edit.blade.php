@extends('layouts.app')
@section('page-title', 'Editar Ação')

@section('content')
<div style="max-width:860px;">

<div style="margin-bottom:16px;">
    <a href="{{ route('acoes.show', $acao) }}" style="font-size:12px;color:var(--cinza-light);text-decoration:none;">← {{ $acao->titulo }}</a>
</div>

<form method="POST" action="{{ route('acoes.update', $acao) }}">
@csrf @method('PUT')

<div class="card" style="margin-bottom:16px;">
    <div class="card-header"><span class="card-title">Identificação</span></div>
    <div class="card-body">
        <div class="form-group">
            <label class="form-label">Título <span style="color:#e53935;">*</span></label>
            <input type="text" name="titulo" class="form-control" value="{{ old('titulo', $acao->titulo) }}" required>
        </div>
        <div style="display:grid;grid-template-columns:1fr 1fr;gap:16px;">
            <div class="form-group">
                <label class="form-label">Tipo</label>
                <select name="tipo" class="form-control">
                    @foreach(['oficina'=>'Oficina','palestra'=>'Palestra','atendimento_individual'=>'Atendimento Individual','grupo'=>'Grupo','capacitacao'=>'Capacitação','evento'=>'Evento','visita_domiciliar'=>'Visita Domiciliar','reuniao'=>'Reunião','outro'=>'Outro'] as $v => $l)
                        <option value="{{ $v }}" {{ old('tipo', $acao->tipo) === $v ? 'selected' : '' }}>{{ $l }}</option>
                    @endforeach
                </select>
            </div>
            <div class="form-group">
                <label class="form-label">Status</label>
                <select name="status" class="form-control">
                    @foreach(['planejada'=>'Planejada','em_andamento'=>'Em andamento','concluida'=>'Concluída','cancelada'=>'Cancelada'] as $v => $l)
                        <option value="{{ $v }}" {{ old('status', $acao->status) === $v ? 'selected' : '' }}>{{ $l }}</option>
                    @endforeach
                </select>
            </div>
        </div>
        <div style="display:grid;grid-template-columns:1fr 1fr;gap:16px;">
            <div class="form-group">
                <label class="form-label">Local</label>
                <input type="text" name="local" class="form-control" value="{{ old('local', $acao->local) }}">
            </div>
            <div class="form-group">
                <label class="form-label">Projeto vinculado</label>
                <select name="project_id" class="form-control">
                    <option value="">Nenhum</option>
                    @foreach($projects as $p)
                        <option value="{{ $p->id }}" {{ old('project_id', $acao->project_id) == $p->id ? 'selected' : '' }}>{{ $p->title }}</option>
                    @endforeach
                </select>
            </div>
        </div>
    </div>
</div>

<div class="card" style="margin-bottom:16px;">
    <div class="card-header"><span class="card-title">Responsável técnico</span></div>
    <div class="card-body">
        <div style="display:grid;grid-template-columns:1fr 1fr 120px;gap:16px;">
            <div class="form-group">
                <label class="form-label">Nome</label>
                <input type="text" name="responsavel_nome" class="form-control" value="{{ old('responsavel_nome', $acao->responsavel_nome) }}">
            </div>
            <div class="form-group">
                <label class="form-label">Cargo / formação</label>
                <input type="text" name="responsavel_cargo" class="form-control" value="{{ old('responsavel_cargo', $acao->responsavel_cargo) }}">
            </div>
            <div class="form-group">
                <label class="form-label">CH/sessão (h)</label>
                <input type="number" name="carga_horaria_sessao" class="form-control" step="0.5" min="0.5" max="24"
                       value="{{ old('carga_horaria_sessao', $acao->carga_horaria_sessao) }}">
            </div>
        </div>
    </div>
</div>

<div class="card" style="margin-bottom:16px;">
    <div class="card-header"><span class="card-title">Descrição técnica</span></div>
    <div class="card-body">
        <div class="form-group">
            <label class="form-label">Descrição</label>
            <textarea name="descricao" class="form-control" rows="3">{{ old('descricao', $acao->descricao) }}</textarea>
        </div>
        <div class="form-group">
            <label class="form-label">Objetivos</label>
            <textarea name="objetivos" class="form-control" rows="3">{{ old('objetivos', $acao->objetivos) }}</textarea>
        </div>
        <div class="form-group">
            <label class="form-label">Metodologia</label>
            <textarea name="metodologia" class="form-control" rows="3">{{ old('metodologia', $acao->metodologia) }}</textarea>
        </div>
        <div class="form-group" style="margin-bottom:0;">
            <label class="form-label">Observações</label>
            <textarea name="observacoes" class="form-control" rows="2">{{ old('observacoes', $acao->observacoes) }}</textarea>
        </div>
    </div>
</div>

<div style="display:flex;gap:10px;">
    <button type="submit" class="btn btn-primary">Salvar alterações</button>
    <a href="{{ route('acoes.show', $acao) }}" class="btn btn-ghost">Cancelar</a>
</div>
</form>
</div>
@endsection
