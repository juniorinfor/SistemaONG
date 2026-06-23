@extends('layouts.app')
@section('page-title', 'Lista de Presença')
@section('page-subtitle', $sessao->data_execucao->format('d/m/Y'))

@push('styles')
<style>
.benef-row {
    display:flex; align-items:center; gap:12px;
    padding:11px 16px; border-bottom:1px solid var(--cinza-borda);
    cursor:pointer; transition:background .1s;
}
.benef-row:last-child { border-bottom:none; }
.benef-row:hover { background:var(--azul-light); }
.benef-row input[type=checkbox] { width:18px; height:18px; accent-color:var(--teal); cursor:pointer; flex-shrink:0; }
.menores-badge { font-size:10px;background:#fff8e1;color:#e65100;padding:1px 6px;border-radius:10px; }
</style>
@endpush

@section('content')

<div style="margin-bottom:16px;">
    <a href="{{ route('acoes.show', $acao) }}" style="font-size:12px;color:var(--cinza-light);text-decoration:none;">← {{ $acao->titulo }}</a>
</div>

{{-- Cabeçalho da sessão --}}
<div class="card" style="margin-bottom:16px;border-top:3px solid var(--teal);">
    <div class="card-body">
        <div style="display:flex;align-items:center;gap:16px;flex-wrap:wrap;">
            <div style="flex:1;">
                <div style="font-size:18px;font-weight:700;margin-bottom:4px;">{{ $sessao->data_execucao->translatedFormat('l, d \d\e F \d\e Y') }}</div>
                <div style="display:flex;gap:16px;flex-wrap:wrap;font-size:13px;color:var(--cinza-light);">
                    @if($sessao->hora_inicio)
                        <span>🕐 {{ $sessao->hora_inicio }} – {{ $sessao->hora_fim ?? '—' }}
                            @if($sessao->duracao) ({{ $sessao->duracao }}) @endif
                        </span>
                    @endif
                    <span>📍 {{ $sessao->local_override ?? $acao->local ?? '—' }}</span>
                    <span>👤 {{ $sessao->facilitador_override ?? $acao->responsavel_nome ?? '—' }}</span>
                </div>
            </div>
            <div style="text-align:center;background:var(--teal-light);border-radius:10px;padding:12px 20px;">
                <div id="count-display" style="font-size:28px;font-weight:700;color:var(--teal-deep);">{{ count($presentesIds) }}</div>
                <div style="font-size:11px;color:var(--cinza-light);">presentes</div>
            </div>
        </div>
    </div>
</div>

<form method="POST" action="{{ route('acoes.sessao.presenca', [$acao, $sessao]) }}">
@csrf

<div class="card" style="margin-bottom:16px;">
    <div class="card-header" style="justify-content:space-between;">
        <span class="card-title">Beneficiários ({{ count($todos) }})</span>
        <div style="display:flex;gap:8px;">
            <button type="button" onclick="marcarTodos(true)"  class="btn btn-ghost btn-sm">✔ Todos presentes</button>
            <button type="button" onclick="marcarTodos(false)" class="btn btn-ghost btn-sm">✕ Limpar</button>
        </div>
    </div>

    @forelse($todos as $b)
    <label class="benef-row" for="b_{{ $b->id }}">
        <input type="checkbox" name="presentes[]" value="{{ $b->id }}"
               id="b_{{ $b->id }}"
               {{ in_array($b->id, $presentesIds) ? 'checked' : '' }}
               onchange="atualizarContador()">
        <div style="flex:1;">
            <div style="font-size:13.5px;font-weight:500;color:var(--texto);">{{ $b->nome }}
                @if($b->is_menor)<span class="menores-badge">menor</span>@endif
            </div>
            @if($b->data_nascimento)
                <div style="font-size:11.5px;color:var(--cinza-light);">{{ $b->idade }} anos</div>
            @endif
        </div>
        @if($b->nome_responsavel)
            <div style="font-size:11px;color:var(--cinza-light);text-align:right;">Resp.: {{ $b->nome_responsavel }}</div>
        @endif
    </label>
    @empty
    <div style="padding:24px;text-align:center;color:var(--cinza-light);font-size:13px;">
        Nenhum beneficiário ativo cadastrado.
        <a href="{{ route('beneficiarios.create') }}" style="color:var(--teal);">Cadastrar →</a>
    </div>
    @endforelse
</div>

@if($sessao->observacoes)
<div style="font-size:12.5px;color:var(--cinza-light);margin-bottom:12px;">
    <strong>Obs. da sessão:</strong> {{ $sessao->observacoes }}
</div>
@endif

<div style="display:flex;gap:10px;align-items:center;">
    <button type="submit" class="btn btn-primary">Salvar lista de presença</button>
    <a href="{{ route('acoes.show', $acao) }}" class="btn btn-ghost">Voltar</a>
    <span style="font-size:12px;color:var(--cinza-light);margin-left:auto;">
        <span id="count-footer">{{ count($presentesIds) }}</span> de {{ count($todos) }} marcados
    </span>
</div>

</form>

<script>
function atualizarContador() {
    const n = document.querySelectorAll('input[name="presentes[]"]:checked').length;
    document.getElementById('count-display').textContent  = n;
    document.getElementById('count-footer').textContent   = n;
}
function marcarTodos(estado) {
    document.querySelectorAll('input[name="presentes[]"]').forEach(cb => cb.checked = estado);
    atualizarContador();
}
</script>
@endsection
