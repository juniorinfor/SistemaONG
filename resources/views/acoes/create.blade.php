@extends('layouts.app')
@section('page-title', 'Nova Ação')

@section('content')
<div style="max-width:860px;">

<div style="margin-bottom:16px;">
    <a href="{{ route('acoes.index') }}" style="font-size:12px;color:var(--cinza-light);text-decoration:none;">← Ações</a>
</div>

<form method="POST" action="{{ route('acoes.store') }}">
@csrf

<div class="card" style="margin-bottom:16px;">
    <div class="card-header"><span class="card-title">Identificação</span></div>
    <div class="card-body">
        <div class="form-group">
            <label class="form-label">Título da ação <span style="color:#e53935;">*</span></label>
            <input type="text" name="titulo" class="form-control @error('titulo') is-invalid @enderror"
                   value="{{ old('titulo') }}" required autofocus placeholder="Ex.: Oficina de Artesanato — Turma 2026">
            @error('titulo')<div class="field-error">{{ $message }}</div>@enderror
        </div>

        <div style="display:grid;grid-template-columns:1fr 1fr;gap:16px;">
            <div class="form-group">
                <label class="form-label">Tipo de ação <span style="color:#e53935;">*</span></label>
                <select name="tipo" class="form-control" required>
                    <option value="oficina"               {{ old('tipo') === 'oficina'               ? 'selected' : '' }}>Oficina</option>
                    <option value="palestra"              {{ old('tipo') === 'palestra'              ? 'selected' : '' }}>Palestra</option>
                    <option value="atendimento_individual"{{ old('tipo') === 'atendimento_individual'? 'selected' : '' }}>Atendimento Individual</option>
                    <option value="grupo"                 {{ old('tipo') === 'grupo'                 ? 'selected' : '' }}>Grupo</option>
                    <option value="capacitacao"           {{ old('tipo') === 'capacitacao'           ? 'selected' : '' }}>Capacitação</option>
                    <option value="evento"                {{ old('tipo') === 'evento'                ? 'selected' : '' }}>Evento</option>
                    <option value="visita_domiciliar"     {{ old('tipo') === 'visita_domiciliar'     ? 'selected' : '' }}>Visita Domiciliar</option>
                    <option value="reuniao"               {{ old('tipo') === 'reuniao'               ? 'selected' : '' }}>Reunião</option>
                    <option value="outro"                 {{ old('tipo','outro') === 'outro'         ? 'selected' : '' }}>Outro</option>
                </select>
            </div>
            <div class="form-group">
                <label class="form-label">Status <span style="color:#e53935;">*</span></label>
                <select name="status" class="form-control" required>
                    <option value="planejada"    {{ old('status','planejada') === 'planejada'    ? 'selected' : '' }}>Planejada</option>
                    <option value="em_andamento" {{ old('status') === 'em_andamento' ? 'selected' : '' }}>Em andamento</option>
                    <option value="concluida"    {{ old('status') === 'concluida'    ? 'selected' : '' }}>Concluída</option>
                    <option value="cancelada"    {{ old('status') === 'cancelada'    ? 'selected' : '' }}>Cancelada</option>
                </select>
            </div>
        </div>

        <div style="display:grid;grid-template-columns:1fr 1fr;gap:16px;">
            <div class="form-group">
                <label class="form-label">Local de realização</label>
                <input type="text" name="local" class="form-control" value="{{ old('local') }}" placeholder="Sede, escola, endereço...">
            </div>
            <div class="form-group">
                <label class="form-label">Projeto vinculado</label>
                <select name="project_id" class="form-control">
                    <option value="">Nenhum (ação independente)</option>
                    @foreach($projects as $p)
                        <option value="{{ $p->id }}" {{ old('project_id') == $p->id ? 'selected' : '' }}>{{ $p->title }}</option>
                    @endforeach
                </select>
                <p class="form-hint">Quando vinculada a um projeto, as sessões servem como comprovação de execução</p>
            </div>
        </div>
    </div>
</div>

<div class="card" style="margin-bottom:16px;">
    <div class="card-header"><span class="card-title">Responsável técnico</span></div>
    <div class="card-body">
        <div style="display:grid;grid-template-columns:1fr 1fr 120px;gap:16px;">
            <div class="form-group">
                <label class="form-label">Nome do responsável</label>
                <input type="text" name="responsavel_nome" class="form-control" value="{{ old('responsavel_nome') }}" placeholder="Nome completo">
            </div>
            <div class="form-group">
                <label class="form-label">Cargo / formação</label>
                <input type="text" name="responsavel_cargo" class="form-control" value="{{ old('responsavel_cargo') }}" placeholder="Ex.: Assistente Social, CRAS">
            </div>
            <div class="form-group">
                <label class="form-label">CH por sessão (h)</label>
                <input type="number" name="carga_horaria_sessao" class="form-control" step="0.5" min="0.5" max="24"
                       value="{{ old('carga_horaria_sessao') }}" placeholder="2.0">
                <p class="form-hint">Horas por encontro</p>
            </div>
        </div>
    </div>
</div>

<div class="card" style="margin-bottom:16px;">
    <div class="card-header"><span class="card-title">Descrição técnica</span></div>
    <div class="card-body">
        <div class="form-group">
            <label class="form-label">Descrição geral</label>
            <textarea name="descricao" class="form-control" rows="3" placeholder="O que é esta ação?">{{ old('descricao') }}</textarea>
        </div>
        <div class="form-group">
            <label class="form-label">Objetivos</label>
            <textarea name="objetivos" class="form-control" rows="3"
                      placeholder="O que se pretende alcançar? Vincule às metas do projeto quando aplicável.">{{ old('objetivos') }}</textarea>
        </div>
        <div class="form-group">
            <label class="form-label">Metodologia</label>
            <textarea name="metodologia" class="form-control" rows="3"
                      placeholder="Como as atividades serão conduzidas? Técnicas, abordagens, materiais...">{{ old('metodologia') }}</textarea>
        </div>
        <div class="form-group" style="margin-bottom:0;">
            <label class="form-label">Observações</label>
            <textarea name="observacoes" class="form-control" rows="2" placeholder="Informações adicionais...">{{ old('observacoes') }}</textarea>
        </div>
    </div>
</div>

<div style="display:flex;gap:10px;">
    <button type="submit" class="btn btn-primary">Cadastrar ação</button>
    <a href="{{ route('acoes.index') }}" class="btn btn-ghost">Cancelar</a>
</div>

</form>
</div>
@endsection
