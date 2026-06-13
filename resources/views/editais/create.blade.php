@extends('layouts.app')

@section('page-title', 'Cadastrar Edital')

@section('content')
<div style="max-width:760px;">
<div class="card">
    <div class="card-header">
        <span class="card-title">Novo Edital</span>
        <a href="{{ route('editais.index') }}" class="btn btn-ghost btn-sm">Cancelar</a>
    </div>
    <div class="card-body">
        <form method="POST" action="{{ route('editais.store') }}" enctype="multipart/form-data">
            @csrf

            <p style="font-size:13px;color:var(--cinza-light);margin-bottom:20px;">
                Cole o texto do edital no campo abaixo e a IA preencherá os campos automaticamente.
                Ou preencha manualmente.
            </p>

            {{-- Texto bruto (extração automática via IA) --}}
            <div class="form-group">
                <label class="form-label">Texto do edital (para extração automática via IA)</label>
                <textarea name="raw_text" class="form-control" rows="6"
                    placeholder="Cole aqui o texto completo ou trecho do edital. A IA preencherá título, resumo, critérios, prazo e valor automaticamente...">{{ old('raw_text') }}</textarea>
                <div style="font-size:11px;color:var(--cinza-light);margin-top:4px;">
                    Opcional. Se preenchido, os campos abaixo serão preenchidos automaticamente na gravação.
                </div>
            </div>

            <hr style="border:none;border-top:1px solid var(--cinza-borda);margin:20px 0;">

            {{-- Título --}}
            <div class="form-group">
                <label class="form-label">Título <span style="color:#e53935;">*</span></label>
                <input type="text" name="titulo" class="form-control @error('titulo') is-invalid @enderror"
                       value="{{ old('titulo') }}" placeholder="Nome completo do edital" required>
                @error('titulo')<div class="field-error">{{ $message }}</div>@enderror
            </div>

            <div style="display:grid;grid-template-columns:1fr 1fr;gap:16px;">
                {{-- Área --}}
                <div class="form-group">
                    <label class="form-label">Área temática</label>
                    <select name="area" class="form-control">
                        <option value="">Selecione...</option>
                        @foreach(['assistência social','educação','saúde','cultura','meio ambiente','criança e adolescente','mulher','habitação','esporte','outro'] as $area)
                            <option value="{{ $area }}" {{ old('area') === $area ? 'selected' : '' }}>{{ ucfirst($area) }}</option>
                        @endforeach
                    </select>
                </div>

                {{-- Link --}}
                <div class="form-group">
                    <label class="form-label">Link oficial</label>
                    <input type="url" name="link_oficial" class="form-control @error('link_oficial') is-invalid @enderror"
                           value="{{ old('link_oficial') }}" placeholder="https://...">
                    @error('link_oficial')<div class="field-error">{{ $message }}</div>@enderror
                </div>

                {{-- Valor mínimo --}}
                <div class="form-group">
                    <label class="form-label">Valor mínimo (R$)</label>
                    <input type="number" name="valor_min" class="form-control" step="0.01"
                           value="{{ old('valor_min') }}" placeholder="0,00">
                </div>

                {{-- Valor máximo --}}
                <div class="form-group">
                    <label class="form-label">Valor máximo (R$)</label>
                    <input type="number" name="valor_max" class="form-control" step="0.01"
                           value="{{ old('valor_max') }}" placeholder="0,00">
                </div>

                {{-- Prazo inscrição --}}
                <div class="form-group">
                    <label class="form-label">Prazo de inscrição</label>
                    <input type="date" name="prazo_inscricao" class="form-control"
                           value="{{ old('prazo_inscricao') }}">
                </div>

                {{-- Prazo execução --}}
                <div class="form-group">
                    <label class="form-label">Prazo de execução</label>
                    <input type="date" name="prazo_execucao" class="form-control"
                           value="{{ old('prazo_execucao') }}">
                </div>
            </div>

            {{-- Resumo --}}
            <div class="form-group">
                <label class="form-label">Resumo</label>
                <textarea name="resumo" class="form-control" rows="3"
                    placeholder="Descrição resumida do edital em português (preenchida automaticamente se houver texto bruto)">{{ old('resumo') }}</textarea>
            </div>

            {{-- Critérios --}}
            <div class="form-group">
                <label class="form-label">Critérios de habilitação / Documentos exigidos</label>
                <textarea name="criterios" class="form-control" rows="5"
                    placeholder="Liste os documentos e requisitos exigidos pelo edital. Um por linha. (Preenchido automaticamente se houver texto bruto)">{{ old('criterios') }}</textarea>
                <div style="font-size:11px;color:var(--cinza-light);margin-top:4px;">
                    Este campo é usado para verificar a compatibilidade com seus documentos via IA.
                </div>
            </div>

            {{-- Anexos (arquivos) --}}
            <div class="form-group">
                <label class="form-label">Anexos (upload de arquivos)</label>
                <input type="file" name="attachments[]" class="form-control" multiple
                       accept=".pdf,.doc,.docx,.xls,.xlsx,.jpg,.png">
                <div style="font-size:11px;color:var(--cinza-light);margin-top:4px;">
                    PDF, Word, Excel, imagens. Máx 20MB por arquivo.
                </div>
            </div>

            {{-- Anexos (links externos) --}}
            <div class="form-group">
                <label class="form-label">Links de anexos externos</label>
                <textarea name="attach_links" class="form-control" rows="3"
                    placeholder="Um por linha no formato: Nome do arquivo|https://link.com|tipo&#10;Ex: Edital completo|https://site.gov.br/edital.pdf|edital&#10;Modelo de proposta|https://site.gov.br/modelo.docx|modelo">{{ old('attach_links') }}</textarea>
                <div style="font-size:11px;color:var(--cinza-light);margin-top:4px;">
                    Tipos: edital, anexo, modelo, formulario
                </div>
            </div>

            <div style="display:flex;justify-content:flex-end;gap:10px;margin-top:8px;">
                <a href="{{ route('editais.index') }}" class="btn btn-ghost btn-sm">Cancelar</a>
                <button type="submit" class="btn btn-primary btn-sm">Salvar edital</button>
            </div>
        </form>
    </div>
</div>
</div>
@endsection
