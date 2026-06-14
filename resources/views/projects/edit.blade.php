@extends('layouts.app')

@section('page-title', 'Editar Projeto')
@section('page-subtitle', Str::limit($project->title, 50))

@section('content')

<div style="max-width:760px;">
<div class="card">
    <div class="card-header">
        <span class="card-title">Editar projeto</span>
        <a href="{{ route('projects.show', $project) }}" class="btn btn-ghost btn-sm">Cancelar</a>
    </div>
    <div class="card-body">
        <form method="POST" action="{{ route('projects.update', $project) }}">
            @csrf
            @method('PATCH')

            <div class="form-group">
                <label class="form-label">Título do projeto <span style="color:#e53935;">*</span></label>
                <input type="text" name="title" class="form-control @error('title') is-invalid @enderror"
                       value="{{ old('title', $project->title) }}" required>
                @error('title') <div class="field-error">{{ $message }}</div> @enderror
            </div>

            <div style="display:grid;grid-template-columns:1fr 1fr;gap:16px;">
                <div class="form-group">
                    <label class="form-label">Área temática</label>
                    <input type="text" name="area" class="form-control @error('area') is-invalid @enderror"
                           value="{{ old('area', $project->area) }}" list="areas-list">
                    <datalist id="areas-list">
                        <option value="Assistência Social">
                        <option value="Educação">
                        <option value="Saúde">
                        <option value="Cultura">
                        <option value="Esporte e Lazer">
                        <option value="Meio Ambiente">
                        <option value="Habitação">
                        <option value="Geração de Renda">
                        <option value="Direitos Humanos">
                        <option value="Juventude">
                        <option value="Idoso">
                        <option value="Criança e Adolescente">
                    </datalist>
                    @error('area') <div class="field-error">{{ $message }}</div> @enderror
                </div>
                <div class="form-group">
                    <label class="form-label">Status <span style="color:#e53935;">*</span></label>
                    <select name="status" class="form-control @error('status') is-invalid @enderror" required>
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
                        <option value="{{ $val }}" {{ old('status', $project->status) === $val ? 'selected' : '' }}>{{ $lbl }}</option>
                        @endforeach
                    </select>
                    @error('status') <div class="field-error">{{ $message }}</div> @enderror
                </div>
            </div>

            <div class="form-group">
                <label class="form-label">Edital vinculado</label>
                <select name="edital_id" class="form-control">
                    <option value="">Nenhum (projeto independente)</option>
                    @foreach($editais as $edital)
                    <option value="{{ $edital->id }}"
                        {{ old('edital_id', $project->edital_id) == $edital->id ? 'selected' : '' }}>
                        {{ Str::limit($edital->titulo, 80) }}
                    </option>
                    @endforeach
                </select>
            </div>

            <div class="form-group">
                <label class="form-label">Descrição / Objeto</label>
                <textarea name="description" class="form-control" rows="4">{{ old('description', $project->description) }}</textarea>
            </div>

            <div style="display:grid;grid-template-columns:1fr 1fr;gap:16px;">
                <div class="form-group">
                    <label class="form-label">Valor pleiteado (R$)</label>
                    <input type="number" name="valor_pleiteado" step="0.01" min="0"
                           class="form-control" value="{{ old('valor_pleiteado', $project->valor_pleiteado) }}">
                </div>
                <div class="form-group">
                    <label class="form-label">Valor aprovado (R$)</label>
                    <input type="number" name="valor_aprovado" step="0.01" min="0"
                           class="form-control" value="{{ old('valor_aprovado', $project->valor_aprovado) }}">
                </div>
            </div>

            <div style="display:grid;grid-template-columns:1fr 1fr;gap:16px;">
                <div class="form-group">
                    <label class="form-label">Data de submissão</label>
                    <input type="date" name="submitted_at" class="form-control"
                           value="{{ old('submitted_at', $project->submitted_at?->format('Y-m-d')) }}">
                </div>
                <div class="form-group">
                    <label class="form-label">Data de aprovação</label>
                    <input type="date" name="approved_at" class="form-control"
                           value="{{ old('approved_at', $project->approved_at?->format('Y-m-d')) }}">
                </div>
            </div>
            <div style="display:grid;grid-template-columns:1fr 1fr;gap:16px;">
                <div class="form-group">
                    <label class="form-label">Início da execução</label>
                    <input type="date" name="start_date" class="form-control"
                           value="{{ old('start_date', $project->start_date?->format('Y-m-d')) }}">
                </div>
                <div class="form-group">
                    <label class="form-label">Término previsto</label>
                    <input type="date" name="end_date" class="form-control"
                           value="{{ old('end_date', $project->end_date?->format('Y-m-d')) }}">
                </div>
            </div>

            <div class="form-group">
                <label class="form-label">Observações</label>
                <textarea name="notes" class="form-control" rows="3">{{ old('notes', $project->notes) }}</textarea>
            </div>

            <div style="display:flex;gap:10px;margin-top:8px;">
                <button type="submit" class="btn btn-primary">Salvar alterações</button>
                <a href="{{ route('projects.show', $project) }}" class="btn btn-ghost">Cancelar</a>
            </div>
        </form>
    </div>
</div>
</div>
@endsection
