@extends('layouts.app')

@section('page-title', 'Editar Pessoa')
@section('page-subtitle', $person->name)

@section('content')

<div style="max-width:640px;">

<div class="card">
    <div class="card-header">
        <span class="card-title">Dados pessoais</span>
        <a href="{{ route('people.show', $person) }}" class="btn btn-ghost btn-sm">Cancelar</a>
    </div>
    <div class="card-body">
        <form method="POST" action="{{ route('people.update', $person) }}">
            @csrf
            @method('PUT')

            <div class="form-group">
                <label class="form-label">Nome completo <span style="color:#e53935;">*</span></label>
                <input type="text" name="name" class="form-control @error('name') is-invalid @enderror"
                       value="{{ old('name', $person->name) }}" required>
                @error('name') <div class="field-error">{{ $message }}</div> @enderror
            </div>

            <div style="display:grid;grid-template-columns:1fr 1fr;gap:16px;">
                <div class="form-group">
                    <label class="form-label">CPF</label>
                    <input type="text" name="cpf" class="form-control" value="{{ old('cpf', $person->cpf) }}" placeholder="000.000.000-00">
                </div>
                <div class="form-group">
                    <label class="form-label">RG</label>
                    <input type="text" name="rg" class="form-control" value="{{ old('rg', $person->rg) }}">
                </div>
            </div>

            <div style="display:grid;grid-template-columns:1fr 1fr;gap:16px;">
                <div class="form-group">
                    <label class="form-label">Tipo <span style="color:#e53935;">*</span></label>
                    <select name="type" class="form-control" required>
                        <option value="diretoria"   {{ old('type',$person->type)=='diretoria'   ? 'selected':'' }}>Diretoria</option>
                        <option value="voluntario"  {{ old('type',$person->type)=='voluntario'  ? 'selected':'' }}>Voluntário(a)</option>
                        <option value="colaborador" {{ old('type',$person->type)=='colaborador' ? 'selected':'' }}>Colaborador(a)</option>
                    </select>
                </div>
                <div class="form-group">
                    <label class="form-label">Cargo / Função</label>
                    <input type="text" name="role" class="form-control" value="{{ old('role', $person->role) }}">
                </div>
            </div>

            <div style="display:grid;grid-template-columns:1fr 1fr;gap:16px;">
                <div class="form-group">
                    <label class="form-label">Início do mandato</label>
                    <input type="date" name="mandate_start" class="form-control"
                           value="{{ old('mandate_start', $person->mandate_start?->format('Y-m-d')) }}">
                </div>
                <div class="form-group">
                    <label class="form-label">Fim do mandato</label>
                    <input type="date" name="mandate_end" class="form-control"
                           value="{{ old('mandate_end', $person->mandate_end?->format('Y-m-d')) }}">
                </div>
            </div>

            <div style="display:grid;grid-template-columns:1fr 1fr;gap:16px;">
                <div class="form-group">
                    <label class="form-label">E-mail</label>
                    <input type="email" name="email" class="form-control" value="{{ old('email', $person->email) }}">
                </div>
                <div class="form-group">
                    <label class="form-label">Telefone</label>
                    <input type="text" name="phone" class="form-control" value="{{ old('phone', $person->phone) }}">
                </div>
            </div>

            <div class="form-group">
                <label style="display:flex;align-items:center;gap:8px;cursor:pointer;font-size:13px;color:var(--cinza);">
                    <input type="checkbox" name="works_with_children" value="1"
                           {{ old('works_with_children', $person->works_with_children) ? 'checked':'' }}
                           style="width:16px;height:16px;accent-color:var(--teal);cursor:pointer;">
                    Trabalha diretamente com crianças / adolescentes
                </label>
            </div>

            <div class="form-group">
                <label style="display:flex;align-items:center;gap:8px;cursor:pointer;font-size:13px;color:var(--cinza);">
                    <input type="checkbox" name="is_active" value="1"
                           {{ old('is_active', $person->is_active) ? 'checked':'' }}
                           style="width:16px;height:16px;accent-color:var(--teal);cursor:pointer;">
                    Pessoa ativa
                </label>
            </div>

            <div style="display:flex;gap:10px;margin-top:8px;">
                <button type="submit" class="btn btn-primary">Salvar alterações</button>
                <a href="{{ route('people.show', $person) }}" class="btn btn-ghost">Cancelar</a>
            </div>
        </form>
    </div>
</div>

</div>

@endsection
