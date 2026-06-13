@extends('layouts.app')

@section('page-title', 'Cadastrar Pessoa')
@section('page-subtitle', 'Nova entrada')

@section('content')

<div style="max-width:640px;">

<div class="card">
    <div class="card-header">
        <span class="card-title">Dados pessoais</span>
        <a href="{{ route('people.index') }}" class="btn btn-ghost btn-sm">Cancelar</a>
    </div>
    <div class="card-body">
        <form method="POST" action="{{ route('people.store') }}">
            @csrf

            <div class="form-group">
                <label class="form-label">Nome completo <span style="color:#e53935;">*</span></label>
                <input type="text" name="name" class="form-control @error('name') is-invalid @enderror"
                       value="{{ old('name') }}" required>
                @error('name') <div class="field-error">{{ $message }}</div> @enderror
            </div>

            <div style="display:grid;grid-template-columns:1fr 1fr;gap:16px;">
                <div class="form-group">
                    <label class="form-label">CPF</label>
                    <input type="text" name="cpf" class="form-control" value="{{ old('cpf') }}" placeholder="000.000.000-00">
                </div>
                <div class="form-group">
                    <label class="form-label">RG</label>
                    <input type="text" name="rg" class="form-control" value="{{ old('rg') }}">
                </div>
            </div>

            <div style="display:grid;grid-template-columns:1fr 1fr;gap:16px;">
                <div class="form-group">
                    <label class="form-label">Tipo <span style="color:#e53935;">*</span></label>
                    <select name="type" class="form-control @error('type') is-invalid @enderror" required>
                        <option value="">Selecione...</option>
                        <option value="diretoria"   {{ old('type')=='diretoria'   ? 'selected':'' }}>Diretoria</option>
                        <option value="voluntario"  {{ old('type')=='voluntario'  ? 'selected':'' }}>Voluntário(a)</option>
                        <option value="colaborador" {{ old('type')=='colaborador' ? 'selected':'' }}>Colaborador(a)</option>
                    </select>
                    @error('type') <div class="field-error">{{ $message }}</div> @enderror
                </div>
                <div class="form-group">
                    <label class="form-label">Cargo / Função</label>
                    <input type="text" name="role" class="form-control" value="{{ old('role') }}" placeholder="Ex.: Presidente">
                </div>
            </div>

            <div style="display:grid;grid-template-columns:1fr 1fr;gap:16px;">
                <div class="form-group">
                    <label class="form-label">Início do mandato</label>
                    <input type="date" name="mandate_start" class="form-control" value="{{ old('mandate_start') }}">
                </div>
                <div class="form-group">
                    <label class="form-label">Fim do mandato</label>
                    <input type="date" name="mandate_end" class="form-control" value="{{ old('mandate_end') }}">
                    <div style="font-size:11px;color:var(--cinza-light);margin-top:4px;">Deixe em branco se ainda em vigor</div>
                </div>
            </div>

            <div style="display:grid;grid-template-columns:1fr 1fr;gap:16px;">
                <div class="form-group">
                    <label class="form-label">E-mail</label>
                    <input type="email" name="email" class="form-control" value="{{ old('email') }}">
                </div>
                <div class="form-group">
                    <label class="form-label">Telefone</label>
                    <input type="text" name="phone" class="form-control" value="{{ old('phone') }}" placeholder="(81) 99999-9999">
                </div>
            </div>

            <div class="form-group">
                <label style="display:flex;align-items:center;gap:8px;cursor:pointer;font-size:13px;color:var(--cinza);">
                    <input type="checkbox" name="works_with_children" value="1" {{ old('works_with_children') ? 'checked':'' }}
                           style="width:16px;height:16px;accent-color:var(--teal);cursor:pointer;">
                    Trabalha diretamente com crianças / adolescentes
                    <span style="font-size:11px;color:var(--cinza-light);">(requer docs adicionais)</span>
                </label>
            </div>

            <div style="display:flex;gap:10px;margin-top:8px;">
                <button type="submit" class="btn btn-primary">Cadastrar</button>
                <a href="{{ route('people.index') }}" class="btn btn-ghost">Cancelar</a>
            </div>
        </form>
    </div>
</div>

</div>

@endsection
