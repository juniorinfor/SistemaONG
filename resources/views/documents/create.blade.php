@extends('layouts.app')

@section('page-title', 'Enviar Documento')
@section('page-subtitle', 'Novo upload')

@section('content')

<div style="max-width:720px;">

<div class="card">
    <div class="card-header">
        <span class="card-title">Dados do documento</span>
        <a href="{{ route('documents.index') }}" class="btn btn-ghost btn-sm">Cancelar</a>
    </div>
    <div class="card-body">

        <form method="POST" action="{{ route('documents.store') }}" enctype="multipart/form-data">
            @csrf

            {{-- Tipo de documento --}}
            <div class="form-group">
                <label class="form-label">Tipo de documento <span style="color:#e53935;">*</span></label>
                <select name="document_type_id" id="type-select" class="form-control @error('document_type_id') is-invalid @enderror" required>
                    <option value="">Selecione...</option>
                    @foreach($documentTypes as $category => $types)
                        @php
                            $catLabel = match($category) {
                                'juridico'  => 'Jurídico',
                                'federal'   => 'Federal',
                                'estadual'  => 'Estadual',
                                'municipal' => 'Municipal',
                                'contabil'  => 'Contábil',
                                'titulacao' => 'Titulações',
                                'pessoal'   => 'Pessoal',
                                default     => ucfirst($category),
                            };
                        @endphp
                        <optgroup label="{{ $catLabel }}">
                            @foreach($types as $type)
                                <option value="{{ $type->id }}"
                                    data-instructions="{{ e($type->instructions) }}"
                                    data-url="{{ $type->official_url }}"
                                    data-validity="{{ $type->validity_days }}"
                                    {{ (old('document_type_id', $selectedType?->id) == $type->id) ? 'selected' : '' }}>
                                    {{ $type->name }}
                                </option>
                            @endforeach
                        </optgroup>
                    @endforeach
                </select>
                @error('document_type_id')
                    <div class="field-error">{{ $message }}</div>
                @enderror
            </div>

            {{-- Painel de instruções (aparece ao selecionar tipo) --}}
            <div id="type-info" style="display:none;background:#f0faf7;border:1px solid #b2dfdb;border-radius:10px;padding:16px 18px;margin-bottom:20px;">
                <div style="display:flex;justify-content:space-between;align-items:flex-start;gap:12px;">
                    <div style="flex:1;min-width:0;">
                        <div style="font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:.06em;color:#00897b;margin-bottom:6px;">
                            Sobre este documento
                        </div>
                        <div id="type-instructions" style="font-size:13px;color:var(--texto);line-height:1.6;white-space:pre-wrap;"></div>
                    </div>
                </div>
                <div id="type-url-wrap" style="display:none;margin-top:12px;padding-top:10px;border-top:1px solid #b2dfdb;">
                    <a id="type-url" href="#" target="_blank"
                       style="font-size:12px;color:#00897b;font-weight:600;text-decoration:none;display:inline-flex;align-items:center;gap:5px;">
                        ↗ Acessar site oficial para obter o documento
                    </a>
                </div>
            </div>

            {{-- Pessoa (opcional) --}}
            @if($people->count())
            <div class="form-group">
                <label class="form-label">Pessoa (se aplicável)</label>
                <select name="person_id" class="form-control">
                    <option value="">Nenhuma (documento institucional)</option>
                    @foreach($people as $person)
                        <option value="{{ $person->id }}" {{ old('person_id') == $person->id ? 'selected' : '' }}>
                            {{ $person->name }}
                            @if($person->role) — {{ $person->role }} @endif
                        </option>
                    @endforeach
                </select>
            </div>
            @endif

            {{-- Arquivo --}}
            <div class="form-group">
                <label class="form-label">Arquivo <span style="color:#e53935;">*</span></label>
                <div style="border:2px dashed var(--borda);border-radius:10px;padding:28px;text-align:center;cursor:pointer;transition:border-color .15s;"
                     id="drop-zone"
                     onclick="document.getElementById('file-input').click()">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" style="width:36px;height:36px;color:var(--azul);margin:0 auto 10px;display:block;">
                        <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/>
                        <polyline points="17 8 12 3 7 8"/>
                        <line x1="12" y1="3" x2="12" y2="15"/>
                    </svg>
                    <p style="font-size:13px;color:var(--cinza);">Arraste o arquivo ou <span style="color:var(--teal);font-weight:600;">clique para selecionar</span></p>
                    <p style="font-size:11px;color:var(--cinza-light);margin-top:4px;">PDF, JPG ou PNG · máx. 10 MB</p>
                    <p id="file-name" style="font-size:12px;color:var(--teal);margin-top:8px;display:none;"></p>
                </div>
                <input type="file" id="file-input" name="file" accept=".pdf,.jpg,.jpeg,.png" style="display:none;" required>
                @error('file')
                    <div class="field-error">{{ $message }}</div>
                @enderror
            </div>

            {{-- Datas --}}
            <div style="display:grid;grid-template-columns:1fr 1fr;gap:16px;">
                <div class="form-group">
                    <label class="form-label">Data de emissão</label>
                    <input type="date" name="issued_at" class="form-control" value="{{ old('issued_at') }}">
                </div>
                <div class="form-group">
                    <label class="form-label">Data de vencimento</label>
                    <input type="date" name="expires_at" class="form-control" value="{{ old('expires_at') }}">
                    <p style="font-size:11px;color:var(--cinza-light);margin-top:4px;">Deixe em branco se não vence</p>
                </div>
            </div>

            {{-- Protocolo --}}
            <div class="form-group">
                <label class="form-label">Número de protocolo</label>
                <input type="text" name="protocol_number" class="form-control" placeholder="Ex.: 123456/2024" value="{{ old('protocol_number') }}">
            </div>

            {{-- Observações --}}
            <div class="form-group">
                <label class="form-label">Observações</label>
                <textarea name="notes" class="form-control" rows="3" placeholder="Informações adicionais sobre este documento...">{{ old('notes') }}</textarea>
            </div>

            {{-- Público --}}
            <div class="form-group" style="display:flex;align-items:center;gap:10px;">
                <label style="display:flex;align-items:center;gap:8px;cursor:pointer;font-size:13px;color:var(--cinza);">
                    <input type="checkbox" name="is_public" value="1" {{ old('is_public') ? 'checked' : '' }}
                           style="width:16px;height:16px;accent-color:var(--teal);cursor:pointer;">
                    Exibir no portal de transparência pública
                </label>
            </div>

            <div style="display:flex;gap:10px;margin-top:8px;">
                <button type="submit" class="btn btn-primary">Enviar documento</button>
                <a href="{{ route('documents.index') }}" class="btn btn-ghost">Cancelar</a>
            </div>
        </form>

    </div>
</div>

</div>

<script>
// Upload drag-and-drop
const input = document.getElementById('file-input');
const zone  = document.getElementById('drop-zone');
const label = document.getElementById('file-name');

input.addEventListener('change', () => {
    if (input.files[0]) {
        label.textContent = input.files[0].name;
        label.style.display = 'block';
        zone.style.borderColor = 'var(--teal)';
    }
});
zone.addEventListener('dragover', e => { e.preventDefault(); zone.style.borderColor = 'var(--teal)'; });
zone.addEventListener('dragleave', () => { zone.style.borderColor = 'var(--borda)'; });
zone.addEventListener('drop', e => {
    e.preventDefault();
    zone.style.borderColor = 'var(--teal)';
    if (e.dataTransfer.files[0]) {
        input.files = e.dataTransfer.files;
        label.textContent = e.dataTransfer.files[0].name;
        label.style.display = 'block';
    }
});

// Painel de instruções do tipo de documento
const typeSelect    = document.getElementById('type-select');
const typeInfo      = document.getElementById('type-info');
const typeInstr     = document.getElementById('type-instructions');
const typeUrlWrap   = document.getElementById('type-url-wrap');
const typeUrl       = document.getElementById('type-url');
const issuedInput   = document.querySelector('input[name="issued_at"]');
const expiresInput  = document.querySelector('input[name="expires_at"]');

function updateTypeInfo() {
    const opt = typeSelect.options[typeSelect.selectedIndex];
    if (!opt || !opt.value) { typeInfo.style.display = 'none'; return; }

    const instructions = opt.dataset.instructions || '';
    const url          = opt.dataset.url || '';
    const validity     = parseInt(opt.dataset.validity) || 0;

    typeInstr.textContent = instructions || 'Sem instruções cadastradas para este tipo.';
    typeInfo.style.display = 'block';

    if (url) {
        typeUrl.href = url;
        typeUrlWrap.style.display = 'block';
    } else {
        typeUrlWrap.style.display = 'none';
    }

    // Sugere vencimento automaticamente se issued_at preenchido e validity_days existe
    if (validity > 0 && issuedInput.value && !expiresInput.value) {
        const issued  = new Date(issuedInput.value);
        issued.setDate(issued.getDate() + validity);
        expiresInput.value = issued.toISOString().split('T')[0];
    }
    if (validity === 0) {
        expiresInput.value = '';
        expiresInput.placeholder = 'Sem vencimento';
    }
}

typeSelect.addEventListener('change', updateTypeInfo);
issuedInput.addEventListener('change', () => {
    const opt      = typeSelect.options[typeSelect.selectedIndex];
    const validity = opt ? parseInt(opt.dataset.validity) || 0 : 0;
    if (validity > 0 && issuedInput.value) {
        const issued = new Date(issuedInput.value);
        issued.setDate(issued.getDate() + validity);
        expiresInput.value = issued.toISOString().split('T')[0];
    }
});

// Inicializa se tipo já selecionado (ex: vindo do catálogo)
if (typeSelect.value) updateTypeInfo();
</script>

@endsection
