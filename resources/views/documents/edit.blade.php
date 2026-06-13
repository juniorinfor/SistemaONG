@extends('layouts.app')

@section('page-title', 'Editar Documento')
@section('page-subtitle', $document->documentType->name)

@section('content')

<div style="max-width:600px;">

<div class="card">
    <div class="card-header">
        <span class="card-title">Metadados</span>
        <a href="{{ route('documents.show', $document) }}" class="btn btn-ghost btn-sm">Cancelar</a>
    </div>
    <div class="card-body">

        <div style="padding:12px 16px;background:var(--azul-light);border-radius:8px;margin-bottom:20px;font-size:12.5px;color:var(--azul-dark);">
            Para substituir o arquivo, use <a href="{{ route('documents.create') }}?type_id={{ $document->document_type_id }}" style="color:var(--teal);font-weight:600;">Enviar nova versão</a>.
            Esta tela edita apenas os metadados da versão vigente.
        </div>

        <form method="POST" action="{{ route('documents.update', $document) }}">
            @csrf
            @method('PUT')

            <div style="display:grid;grid-template-columns:1fr 1fr;gap:16px;">
                <div class="form-group">
                    <label class="form-label">Data de emissão</label>
                    <input type="date" name="issued_at" class="form-control"
                           value="{{ old('issued_at', $document->issued_at?->format('Y-m-d')) }}">
                </div>
                <div class="form-group">
                    <label class="form-label">Data de vencimento</label>
                    <input type="date" name="expires_at" class="form-control"
                           value="{{ old('expires_at', $document->expires_at?->format('Y-m-d')) }}">
                    <p style="font-size:11px;color:var(--cinza-light);margin-top:4px;">Deixe em branco se não vence</p>
                </div>
            </div>

            <div class="form-group">
                <label class="form-label">Número de protocolo</label>
                <input type="text" name="protocol_number" class="form-control"
                       placeholder="Ex.: 123456/2024"
                       value="{{ old('protocol_number', $document->protocol_number) }}">
            </div>

            <div class="form-group">
                <label class="form-label">Observações</label>
                <textarea name="notes" class="form-control" rows="3"
                          placeholder="Informações adicionais...">{{ old('notes', $document->notes) }}</textarea>
            </div>

            <div class="form-group" style="display:flex;align-items:center;gap:10px;">
                <label style="display:flex;align-items:center;gap:8px;cursor:pointer;font-size:13px;color:var(--cinza);">
                    <input type="checkbox" name="is_public" value="1"
                           {{ old('is_public', $document->is_public) ? 'checked' : '' }}
                           style="width:16px;height:16px;accent-color:var(--teal);cursor:pointer;">
                    Exibir no portal de transparência pública
                </label>
            </div>

            <div style="display:flex;gap:10px;margin-top:8px;">
                <button type="submit" class="btn btn-primary">Salvar alterações</button>
                <a href="{{ route('documents.show', $document) }}" class="btn btn-ghost">Cancelar</a>
            </div>
        </form>

    </div>
</div>

</div>

@endsection
