@extends('layouts.app')

@section('page-title', 'Analisar Edital')
@section('page-subtitle', 'Envie o edital e a IA identifica o que você já tem')

@section('content')

<div style="display:grid;grid-template-columns:1fr 320px;gap:20px;align-items:flex-start;max-width:1060px;">

{{-- Coluna principal: upload --}}
<div class="card">
    <div class="card-header">
        <span class="card-title">Enviar edital para análise</span>
        <a href="{{ route('editais.index') }}" class="btn btn-ghost btn-sm">Cancelar</a>
    </div>
    <div class="card-body">

        @if(session('error'))
            <div class="alert alert-danger" style="margin-bottom:16px;">{{ session('error') }}</div>
        @endif

        <form method="POST" action="{{ route('editais.analisar.store') }}" enctype="multipart/form-data" id="analisar-form">
            @csrf

            <div class="form-group">
                <label class="form-label">Arquivo do edital <span style="color:#e53935;">*</span></label>
                <div style="border:2px dashed var(--borda);border-radius:10px;padding:32px;text-align:center;cursor:pointer;transition:border-color .15s;"
                     id="drop-zone"
                     onclick="document.getElementById('file-input').click()">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" style="width:40px;height:40px;color:var(--azul);margin:0 auto 12px;display:block;">
                        <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/>
                        <polyline points="17 8 12 3 7 8"/>
                        <line x1="12" y1="3" x2="12" y2="15"/>
                    </svg>
                    <p style="font-size:14px;color:var(--cinza);">Arraste o edital ou <span style="color:var(--teal);font-weight:600;">clique para selecionar</span></p>
                    <p style="font-size:11px;color:var(--cinza-light);margin-top:6px;">PDF ou imagem (JPG, PNG) · máx. 20 MB</p>
                    <p id="file-name" style="font-size:13px;color:var(--teal);margin-top:10px;display:none;font-weight:600;"></p>
                </div>
                <input type="file" id="file-input" name="arquivo" accept=".pdf,.jpg,.jpeg,.png" style="display:none;" required>
                @error('arquivo')
                    <div class="field-error">{{ $message }}</div>
                @enderror
            </div>

            <div style="display:flex;gap:10px;margin-top:8px;">
                <button type="submit" class="btn btn-primary" id="submit-btn">
                    🔍 Analisar com IA
                </button>
                <a href="{{ route('editais.index') }}" class="btn btn-ghost">Cancelar</a>
            </div>
        </form>

    </div>
</div>

{{-- Coluna lateral: o que a IA faz --}}
<div style="position:sticky;top:24px;">
    <div style="background:#f0faf7;border:1px solid #b2dfdb;border-radius:12px;padding:20px;">
        <div style="font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:.06em;color:#00897b;margin-bottom:14px;">
            O que a IA faz
        </div>
        @php
        $passos = [
            ['1', 'Lê o edital', 'Extrai título, valor, prazos, área e os documentos exigidos.'],
            ['2', 'Cruza com seu acervo', 'Compara as exigências com os documentos que você já cadastrou.'],
            ['3', 'Calcula compatibilidade', 'Mostra o % de prontidão e a lista do que você tem e do que falta.'],
        ];
        @endphp
        @foreach($passos as [$n, $titulo, $desc])
        <div style="display:flex;gap:10px;margin-bottom:14px;align-items:flex-start;">
            <div style="width:24px;height:24px;border-radius:50%;background:#00897b;color:#fff;font-size:12px;font-weight:700;display:flex;align-items:center;justify-content:center;flex-shrink:0;">{{ $n }}</div>
            <div>
                <div style="font-size:13px;font-weight:600;color:var(--texto);">{{ $titulo }}</div>
                <div style="font-size:12px;color:var(--cinza-light);line-height:1.5;">{{ $desc }}</div>
            </div>
        </div>
        @endforeach
        <div style="margin-top:6px;padding-top:12px;border-top:1px solid #b2dfdb;font-size:11px;color:var(--cinza-light);">
            A análise leva alguns segundos. O edital fica salvo no Radar com o arquivo anexado.
        </div>
    </div>
</div>

</div>

<script>
const input = document.getElementById('file-input');
const zone  = document.getElementById('drop-zone');
const label = document.getElementById('file-name');
const form  = document.getElementById('analisar-form');
const btn   = document.getElementById('submit-btn');

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
form.addEventListener('submit', () => {
    btn.innerHTML = '⏳ Analisando edital...';
    btn.style.opacity = '.7';
    btn.style.pointerEvents = 'none';
});
</script>

@endsection
