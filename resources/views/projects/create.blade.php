@extends('layouts.app')

@section('page-title', 'Novo Projeto')
@section('page-subtitle', 'Cadastrar projeto')

@section('content')

<div style="display:grid;grid-template-columns:1fr 300px;gap:20px;align-items:flex-start;max-width:1060px;">

{{-- Formulário --}}
<div class="card">
    <div class="card-header">
        <span class="card-title">Dados do projeto</span>
        <a href="{{ route('projects.index') }}" class="btn btn-ghost btn-sm">Cancelar</a>
    </div>
    <div class="card-body">
        <form method="POST" action="{{ route('projects.store') }}">
            @csrf

            <div class="form-group">
                <label class="form-label">Título do projeto <span style="color:#e53935;">*</span></label>
                <input type="text" name="title" class="form-control @error('title') is-invalid @enderror"
                       value="{{ old('title') }}" placeholder="Ex.: Oficinas de Geração de Renda 2026" required>
                @error('title') <div class="field-error">{{ $message }}</div> @enderror
            </div>

            <div style="display:grid;grid-template-columns:1fr 1fr;gap:16px;">
                <div class="form-group">
                    <label class="form-label">Área temática</label>
                    <input type="text" name="area" id="area-input" class="form-control @error('area') is-invalid @enderror"
                           value="{{ old('area') }}" list="areas-list"
                           placeholder="Ex.: assistência social">
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
                        <option value="{{ $val }}" {{ old('status', 'rascunho') === $val ? 'selected' : '' }}>{{ $lbl }}</option>
                        @endforeach
                    </select>
                    @error('status') <div class="field-error">{{ $message }}</div> @enderror
                </div>
            </div>

            <div class="form-group">
                <label class="form-label">Edital vinculado</label>
                <select name="edital_id" class="form-control @error('edital_id') is-invalid @enderror">
                    <option value="">Nenhum (projeto independente)</option>
                    @foreach($editais as $edital)
                    <option value="{{ $edital->id }}"
                        {{ old('edital_id', $selectedEdital?->id) == $edital->id ? 'selected' : '' }}>
                        {{ Str::limit($edital->titulo, 80) }}
                        @if($edital->prazo_inscricao) — Prazo: {{ $edital->prazo_inscricao->format('d/m/Y') }} @endif
                    </option>
                    @endforeach
                </select>
                @error('edital_id') <div class="field-error">{{ $message }}</div> @enderror
            </div>

            <div class="form-group">
                <label class="form-label">Descrição / Objeto</label>
                <textarea name="description" class="form-control @error('description') is-invalid @enderror"
                          rows="4" placeholder="Descreva o objetivo e o público-alvo do projeto...">{{ old('description') }}</textarea>
                @error('description') <div class="field-error">{{ $message }}</div> @enderror
            </div>

            {{-- Valores --}}
            <div style="display:grid;grid-template-columns:1fr 1fr;gap:16px;">
                <div class="form-group">
                    <label class="form-label">Valor pleiteado (R$)</label>
                    <input type="number" name="valor_pleiteado" step="0.01" min="0"
                           class="form-control @error('valor_pleiteado') is-invalid @enderror"
                           value="{{ old('valor_pleiteado') }}" placeholder="0,00">
                    @error('valor_pleiteado') <div class="field-error">{{ $message }}</div> @enderror
                </div>
                <div class="form-group">
                    <label class="form-label">Valor aprovado (R$)</label>
                    <input type="number" name="valor_aprovado" step="0.01" min="0"
                           class="form-control @error('valor_aprovado') is-invalid @enderror"
                           value="{{ old('valor_aprovado') }}" placeholder="0,00">
                    @error('valor_aprovado') <div class="field-error">{{ $message }}</div> @enderror
                </div>
            </div>

            {{-- Datas --}}
            <div style="display:grid;grid-template-columns:1fr 1fr;gap:16px;">
                <div class="form-group">
                    <label class="form-label">Data de submissão</label>
                    <input type="date" name="submitted_at" class="form-control" value="{{ old('submitted_at') }}">
                </div>
                <div class="form-group">
                    <label class="form-label">Data de aprovação</label>
                    <input type="date" name="approved_at" class="form-control" value="{{ old('approved_at') }}">
                </div>
            </div>
            <div style="display:grid;grid-template-columns:1fr 1fr;gap:16px;">
                <div class="form-group">
                    <label class="form-label">Início da execução</label>
                    <input type="date" name="start_date" class="form-control" value="{{ old('start_date') }}">
                </div>
                <div class="form-group">
                    <label class="form-label">Término previsto</label>
                    <input type="date" name="end_date" class="form-control" value="{{ old('end_date') }}">
                </div>
            </div>

            <div class="form-group">
                <label class="form-label">Observações</label>
                <textarea name="notes" class="form-control" rows="3"
                          placeholder="Informações adicionais, contatos do financiador, condicionantes...">{{ old('notes') }}</textarea>
            </div>

            <div style="display:flex;gap:10px;margin-top:8px;">
                <button type="submit" class="btn btn-primary">Salvar projeto</button>
                <a href="{{ route('projects.index') }}" class="btn btn-ghost">Cancelar</a>
            </div>
        </form>
    </div>
</div>

{{-- Painel lateral --}}
<div style="position:sticky;top:24px;">
    <div style="background:#f0faf7;border:1px solid #b2dfdb;border-radius:12px;padding:20px;">
        <div style="font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:.06em;color:#00897b;margin-bottom:12px;">
            Fluxo de um projeto
        </div>
        @php
        $steps = [
            ['Rascunho',      '#9e9e9e', 'Ideia inicial, sem compromisso.'],
            ['Em elaboração', '#1565c0', 'Construindo plano de trabalho.'],
            ['Submetido',     '#e65100', 'Enviado ao financiador.'],
            ['Aprovado',      '#00897b', 'Recurso garantido.'],
            ['Em execução',   '#6a1b9a', 'Atividades em andamento.'],
            ['Concluído',     '#2e7d32', 'Relatório final entregue.'],
        ];
        @endphp
        @foreach($steps as $i => [$lbl, $color, $desc])
        <div style="display:flex;gap:10px;margin-bottom:12px;align-items:flex-start;">
            <div style="width:22px;height:22px;border-radius:50%;background:{{ $color }};color:#fff;font-size:11px;font-weight:700;display:flex;align-items:center;justify-content:center;flex-shrink:0;margin-top:1px;">{{ $i+1 }}</div>
            <div>
                <div style="font-size:12px;font-weight:600;color:var(--texto);">{{ $lbl }}</div>
                <div style="font-size:11px;color:var(--cinza-light);">{{ $desc }}</div>
            </div>
        </div>
        @endforeach
    </div>
</div>

</div>
@endsection
