@extends('layouts.app')

@section('page-title', 'Configurações')

@push('styles')
<style>
.settings-section {
    margin-bottom: 28px;
}
.settings-section-title {
    font-size: 11px;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: .08em;
    color: var(--teal);
    margin-bottom: 14px;
    padding-bottom: 8px;
    border-bottom: 2px solid var(--teal);
    display: flex;
    align-items: center;
    gap: 8px;
}
.form-grid-2 {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 16px;
}
@media (max-width: 600px) {
    .form-grid-2 { grid-template-columns: 1fr; }
}
.cnpj-badge {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    font-size: 12px;
    background: #e8f8f5;
    color: #00897b;
    border: 1px solid #b2dfdb;
    border-radius: 20px;
    padding: 3px 12px;
    font-weight: 600;
    margin-top: 6px;
}
</style>
@endpush

@section('content')

@if(session('success'))
<div class="alert alert-success" style="margin-bottom:20px;">
    ✔ {{ session('success') }}
</div>
@endif

<div style="max-width:780px;">

<form method="POST" action="{{ route('settings.update') }}">
    @csrf
    @method('PATCH')

    {{-- Dados principais --}}
    <div class="card settings-section">
        <div class="card-header">
            <span class="card-title">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="width:16px;height:16px;vertical-align:-2px;margin-right:6px;"><rect x="2" y="7" width="20" height="14" rx="2"/><path d="M16 3H8a2 2 0 0 0-2 2v2h12V5a2 2 0 0 0-2-2z"/></svg>
                Dados da Instituição
            </span>
        </div>
        <div class="card-body">

            <div class="form-group">
                <label class="form-label">Nome da organização <span style="color:#e53935;">*</span></label>
                <input type="text" name="name" class="form-control @error('name') is-invalid @enderror"
                       value="{{ old('name', $institution->name) }}" required>
                @error('name') <div class="field-error">{{ $message }}</div> @enderror
            </div>

            <div class="form-grid-2">
                <div class="form-group">
                    <label class="form-label">CNPJ <span style="color:#e53935;">*</span></label>
                    <input type="text" name="cnpj" class="form-control @error('cnpj') is-invalid @enderror"
                           value="{{ old('cnpj', $institution->cnpj) }}"
                           placeholder="00.000.000/0000-00" maxlength="18" required>
                    @error('cnpj') <div class="field-error">{{ $message }}</div> @enderror
                </div>
                <div class="form-group">
                    <label class="form-label">E-mail institucional</label>
                    <input type="email" name="email" class="form-control @error('email') is-invalid @enderror"
                           value="{{ old('email', $institution->email) }}"
                           placeholder="contato@suaong.org.br">
                    @error('email') <div class="field-error">{{ $message }}</div> @enderror
                </div>
            </div>

            <div class="form-grid-2">
                <div class="form-group">
                    <label class="form-label">Telefone</label>
                    <input type="text" name="phone" class="form-control @error('phone') is-invalid @enderror"
                           value="{{ old('phone', $institution->phone) }}"
                           placeholder="(81) 99999-9999">
                    @error('phone') <div class="field-error">{{ $message }}</div> @enderror
                </div>
                <div class="form-group">
                    <label class="form-label">Site</label>
                    <input type="url" name="website" class="form-control @error('website') is-invalid @enderror"
                           value="{{ old('website', $institution->website) }}"
                           placeholder="https://suaong.org.br">
                    @error('website') <div class="field-error">{{ $message }}</div> @enderror
                </div>
            </div>

        </div>
    </div>

    {{-- Endereço --}}
    <div class="card settings-section">
        <div class="card-header">
            <span class="card-title">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="width:16px;height:16px;vertical-align:-2px;margin-right:6px;"><path d="M12 2C8.13 2 5 5.13 5 9c0 5.25 7 13 7 13s7-7.75 7-13c0-3.87-3.13-7-7-7z"/><circle cx="12" cy="9" r="2.5"/></svg>
                Endereço
            </span>
        </div>
        <div class="card-body">

            <div class="form-group">
                <label class="form-label">Logradouro completo</label>
                <input type="text" name="address" class="form-control @error('address') is-invalid @enderror"
                       value="{{ old('address', $institution->address) }}"
                       placeholder="Rua, número, bairro">
                @error('address') <div class="field-error">{{ $message }}</div> @enderror
            </div>

            <div class="form-grid-2">
                <div class="form-group">
                    <label class="form-label">Cidade</label>
                    <input type="text" name="city" class="form-control @error('city') is-invalid @enderror"
                           value="{{ old('city', $institution->city) }}">
                    @error('city') <div class="field-error">{{ $message }}</div> @enderror
                </div>
                <div class="form-group">
                    <label class="form-label">Estado (UF)</label>
                    <select name="state" class="form-control @error('state') is-invalid @enderror">
                        @php
                            $ufs = ['AC','AL','AM','AP','BA','CE','DF','ES','GO','MA','MG','MS','MT',
                                    'PA','PB','PE','PI','PR','RJ','RN','RO','RR','RS','SC','SE','SP','TO'];
                        @endphp
                        @foreach($ufs as $uf)
                            <option value="{{ $uf }}" {{ old('state', $institution->state) === $uf ? 'selected' : '' }}>
                                {{ $uf }}
                            </option>
                        @endforeach
                    </select>
                    @error('state') <div class="field-error">{{ $message }}</div> @enderror
                </div>
            </div>

        </div>
    </div>

    {{-- Missão --}}
    <div class="card settings-section">
        <div class="card-header">
            <span class="card-title">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="width:16px;height:16px;vertical-align:-2px;margin-right:6px;"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/></svg>
                Missão e Descrição
            </span>
        </div>
        <div class="card-body">

            <div class="form-group" style="margin-bottom:0;">
                <label class="form-label">Missão da organização</label>
                <textarea name="mission" class="form-control @error('mission') is-invalid @enderror"
                          rows="5"
                          placeholder="Descreva a missão, visão e valores da organização...">{{ old('mission', $institution->mission) }}</textarea>
                <p style="font-size:11px;color:var(--cinza-light);margin-top:4px;">
                    Exibida no portal de transparência pública.
                </p>
                @error('mission') <div class="field-error">{{ $message }}</div> @enderror
            </div>

        </div>
    </div>

    {{-- Ações --}}
    <div style="display:flex;gap:10px;align-items:center;margin-bottom:40px;">
        <button type="submit" class="btn btn-primary">Salvar configurações</button>
        <a href="{{ route('dashboard') }}" class="btn btn-ghost">Cancelar</a>
        @if($errors->any())
            <span style="font-size:12px;color:#e53935;">Corrija os erros acima antes de salvar.</span>
        @endif
    </div>

</form>

</div>
@endsection
