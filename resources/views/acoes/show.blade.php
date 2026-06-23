@extends('layouts.app')
@section('page-title', $acao->titulo)

@push('styles')
<style>
.sessao-card {
    border:1px solid var(--cinza-borda); border-radius:10px;
    padding:14px 16px; margin-bottom:10px; background:#fff;
    display:flex; align-items:flex-start; gap:14px;
}
.sessao-date {
    width:56px; height:56px; border-radius:8px;
    background:var(--teal-light); color:var(--teal-deep);
    display:flex; flex-direction:column; align-items:center;
    justify-content:center; font-family:'Roboto',sans-serif;
    flex-shrink:0; text-align:center;
}
.sessao-date .day  { font-size:22px; font-weight:700; line-height:1; }
.sessao-date .mon  { font-size:10px; font-weight:600; text-transform:uppercase; }
.nova-sessao-form  { background:#f8f9fa; border:2px dashed var(--cinza-borda); border-radius:10px; padding:18px; margin-top:16px; }
</style>
@endpush

@section('content')

{{-- Cabeçalho --}}
<div style="display:flex;align-items:flex-start;justify-content:space-between;gap:12px;margin-bottom:20px;flex-wrap:wrap;">
    <div>
        <a href="{{ route('acoes.index') }}" style="font-size:12px;color:var(--cinza-light);text-decoration:none;">← Ações</a>
        <h1 style="font-size:18px;font-weight:700;margin:4px 0 6px;line-height:1.3;">{{ $acao->titulo }}</h1>
        <div style="display:flex;gap:8px;flex-wrap:wrap;">
            <span style="font-size:12px;font-weight:600;padding:2px 10px;border-radius:20px;background:{{ $acao->status_bg }};color:{{ $acao->status_color }};">{{ $acao->status_label }}</span>
            <span style="font-size:12px;padding:2px 10px;border-radius:20px;background:#f3e5f5;color:#6a1b9a;">{{ $acao->tipo_label }}</span>
            @if($acao->project)
                <a href="{{ route('projects.show', $acao->project) }}" style="font-size:12px;padding:2px 10px;border-radius:20px;background:#e3f2fd;color:#1565c0;text-decoration:none;">📁 {{ $acao->project->title }}</a>
            @endif
        </div>
    </div>
    <div style="display:flex;gap:8px;flex-wrap:wrap;">
        <a href="{{ route('acoes.relatorio', $acao) }}" class="btn btn-ghost btn-sm">📄 Relatório</a>
        <a href="{{ route('acoes.edit', $acao) }}" class="btn btn-ghost btn-sm">Editar</a>
        <form method="POST" action="{{ route('acoes.destroy', $acao) }}" onsubmit="return confirm('Remover esta ação?')">
            @csrf @method('DELETE')
            <button class="btn btn-ghost btn-sm" style="color:#c62828;">Remover</button>
        </form>
    </div>
</div>

<div style="display:grid;grid-template-columns:1fr 280px;gap:16px;">
    {{-- Coluna principal --}}
    <div>
        {{-- Dados técnicos --}}
        <div class="card" style="margin-bottom:16px;">
            <div class="card-body">
                <div style="display:grid;grid-template-columns:1fr 1fr 1fr;gap:12px 24px;margin-bottom:{{ $acao->descricao || $acao->objetivos || $acao->metodologia ? '16px' : '0' }};">
                    <div>
                        <div style="font-size:10px;text-transform:uppercase;letter-spacing:.06em;color:var(--cinza-light);font-weight:700;margin-bottom:3px;">Local</div>
                        <div style="font-size:13.5px;">{{ $acao->local ?? '—' }}</div>
                    </div>
                    <div>
                        <div style="font-size:10px;text-transform:uppercase;letter-spacing:.06em;color:var(--cinza-light);font-weight:700;margin-bottom:3px;">Responsável</div>
                        <div style="font-size:13.5px;">{{ $acao->responsavel_nome ?? '—' }}</div>
                        @if($acao->responsavel_cargo)<div style="font-size:11.5px;color:var(--cinza-light);">{{ $acao->responsavel_cargo }}</div>@endif
                    </div>
                    <div>
                        <div style="font-size:10px;text-transform:uppercase;letter-spacing:.06em;color:var(--cinza-light);font-weight:700;margin-bottom:3px;">CH por sessão</div>
                        <div style="font-size:13.5px;">{{ $acao->carga_horaria_sessao ? $acao->carga_horaria_sessao . 'h' : '—' }}</div>
                    </div>
                </div>

                @if($acao->descricao)
                <div style="margin-bottom:12px;">
                    <div style="font-size:10px;text-transform:uppercase;letter-spacing:.06em;color:var(--cinza-light);font-weight:700;margin-bottom:4px;">Descrição</div>
                    <div style="font-size:13.5px;line-height:1.6;white-space:pre-line;">{{ $acao->descricao }}</div>
                </div>
                @endif
                @if($acao->objetivos)
                <div style="margin-bottom:12px;">
                    <div style="font-size:10px;text-transform:uppercase;letter-spacing:.06em;color:var(--cinza-light);font-weight:700;margin-bottom:4px;">Objetivos</div>
                    <div style="font-size:13.5px;line-height:1.6;white-space:pre-line;">{{ $acao->objetivos }}</div>
                </div>
                @endif
                @if($acao->metodologia)
                <div>
                    <div style="font-size:10px;text-transform:uppercase;letter-spacing:.06em;color:var(--cinza-light);font-weight:700;margin-bottom:4px;">Metodologia</div>
                    <div style="font-size:13.5px;line-height:1.6;white-space:pre-line;">{{ $acao->metodologia }}</div>
                </div>
                @endif
            </div>
        </div>

        {{-- Sessões --}}
        <div class="card-header" style="background:#fff;border:1px solid var(--cinza-borda);border-radius:10px 10px 0 0;border-bottom:none;padding:14px 20px;display:flex;align-items:center;justify-content:space-between;">
            <span class="card-title">Sessões realizadas</span>
            <button onclick="toggleNovaSessao()" class="btn btn-primary btn-sm">+ Registrar sessão</button>
        </div>

        {{-- Formulário nova sessão --}}
        <div id="nova-sessao-wrap" style="display:none;background:#f0f9f7;border:1px solid #b2dfdb;border-top:none;padding:16px 20px;">
            <form method="POST" action="{{ route('acoes.sessao.store', $acao) }}">
                @csrf
                <div style="display:grid;grid-template-columns:160px 110px 110px 1fr;gap:12px;align-items:end;">
                    <div class="form-group" style="margin-bottom:0;">
                        <label class="form-label">Data <span style="color:#e53935;">*</span></label>
                        <input type="date" name="data_execucao" class="form-control" value="{{ date('Y-m-d') }}" required>
                    </div>
                    <div class="form-group" style="margin-bottom:0;">
                        <label class="form-label">Início</label>
                        <input type="time" name="hora_inicio" class="form-control">
                    </div>
                    <div class="form-group" style="margin-bottom:0;">
                        <label class="form-label">Fim</label>
                        <input type="time" name="hora_fim" class="form-control">
                    </div>
                    <div class="form-group" style="margin-bottom:0;">
                        <label class="form-label">Local (se diferente)</label>
                        <input type="text" name="local_override" class="form-control" placeholder="{{ $acao->local ?? 'mesmo local da ação' }}">
                    </div>
                </div>
                <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px;margin-top:10px;">
                    <div class="form-group" style="margin-bottom:0;">
                        <label class="form-label">Facilitador (se diferente)</label>
                        <input type="text" name="facilitador_override" class="form-control" placeholder="{{ $acao->responsavel_nome ?? 'mesmo responsável' }}">
                    </div>
                    <div class="form-group" style="margin-bottom:0;">
                        <label class="form-label">Observações</label>
                        <input type="text" name="observacoes" class="form-control" placeholder="Notas sobre esta sessão...">
                    </div>
                </div>
                <div style="margin-top:12px;display:flex;gap:8px;">
                    <button type="submit" class="btn btn-primary btn-sm">Registrar e adicionar presença</button>
                    <button type="button" onclick="toggleNovaSessao()" class="btn btn-ghost btn-sm">Cancelar</button>
                </div>
            </form>
        </div>

        <div style="background:#fff;border:1px solid var(--cinza-borda);border-top:none;border-radius:0 0 10px 10px;padding:16px 20px;">
            @forelse($acao->sessoes as $sessao)
            @php
                $presentes = $sessao->beneficiarios->filter(fn($b) => $b->pivot->presente)->count();
                $total     = $sessao->beneficiarios->count();
            @endphp
            <div class="sessao-card">
                <div class="sessao-date">
                    <div class="day">{{ $sessao->data_execucao->format('d') }}</div>
                    <div class="mon">{{ $sessao->data_execucao->translatedFormat('M') }}</div>
                </div>
                <div style="flex:1;min-width:0;">
                    <div style="display:flex;gap:8px;flex-wrap:wrap;align-items:center;margin-bottom:4px;">
                        <span style="font-size:13px;font-weight:600;">{{ $sessao->data_execucao->format('d/m/Y') }}</span>
                        @if($sessao->hora_inicio)
                            <span style="font-size:12px;color:var(--cinza-light);">{{ $sessao->hora_inicio }} – {{ $sessao->hora_fim ?? '?' }}</span>
                            @if($sessao->duracao)<span style="font-size:11px;color:var(--teal);">({{ $sessao->duracao }})</span>@endif
                        @endif
                        @if($sessao->local_override)
                            <span style="font-size:12px;color:var(--cinza-light);">📍 {{ $sessao->local_override }}</span>
                        @endif
                        @if($sessao->facilitador_override)
                            <span style="font-size:12px;color:var(--cinza-light);">👤 {{ $sessao->facilitador_override }}</span>
                        @endif
                    </div>
                    <div style="font-size:12.5px;color:var(--cinza-light);">
                        @if($total > 0)
                            <span style="color:{{ $presentes > 0 ? '#2e7d32' : '#c62828' }};font-weight:600;">{{ $presentes }} presente(s)</span>
                            de {{ $total }} registrados
                        @else
                            <span style="color:#e65100;">Presença não registrada</span>
                        @endif
                    </div>
                    @if($sessao->observacoes)
                        <div style="font-size:12px;color:var(--cinza-light);margin-top:2px;">{{ $sessao->observacoes }}</div>
                    @endif
                </div>
                <a href="{{ route('acoes.sessao.show', [$acao, $sessao]) }}" class="btn btn-ghost btn-sm" style="flex-shrink:0;">
                    {{ $total > 0 ? '✔ Lista' : '+ Presença' }}
                </a>
            </div>
            @empty
            <div style="text-align:center;padding:24px;color:var(--cinza-light);">
                <p style="font-size:13px;margin-bottom:8px;">Nenhuma sessão registrada ainda.</p>
                <p style="font-size:12px;">Clique em <strong>"+ Registrar sessão"</strong> para adicionar o primeiro encontro.</p>
            </div>
            @endforelse
        </div>
    </div>

    {{-- Coluna lateral: estatísticas --}}
    <div>
        <div class="card" style="margin-bottom:12px;">
            <div class="card-header"><span class="card-title">Resumo de execução</span></div>
            <div class="card-body" style="display:flex;flex-direction:column;gap:14px;">
                <div style="text-align:center;">
                    <div style="font-size:34px;font-weight:700;color:var(--teal);">{{ $acao->total_sessoes }}</div>
                    <div style="font-size:12px;color:var(--cinza-light);">sessões realizadas</div>
                </div>
                <div style="display:grid;grid-template-columns:1fr 1fr;gap:8px;text-align:center;">
                    <div style="background:var(--azul-light);border-radius:8px;padding:10px;">
                        <div style="font-size:22px;font-weight:700;color:var(--azul-dark);">{{ $acao->total_beneficiarios_unicos }}</div>
                        <div style="font-size:10.5px;color:var(--cinza-light);">benef. únicos</div>
                    </div>
                    <div style="background:#f3e5f5;border-radius:8px;padding:10px;">
                        <div style="font-size:22px;font-weight:700;color:#6a1b9a;">{{ $acao->total_presencas }}</div>
                        <div style="font-size:10.5px;color:var(--cinza-light);">presenças totais</div>
                    </div>
                </div>
                @if($acao->carga_horaria_total !== null)
                <div style="text-align:center;background:var(--teal-light);border-radius:8px;padding:10px;">
                    <div style="font-size:22px;font-weight:700;color:var(--teal-deep);">{{ $acao->carga_horaria_total }}h</div>
                    <div style="font-size:10.5px;color:var(--cinza-light);">carga horária total</div>
                </div>
                @endif
            </div>
        </div>

        @if($acao->project)
        <div class="card">
            <div class="card-header"><span class="card-title">Projeto vinculado</span></div>
            <div class="card-body">
                <p style="font-size:13px;font-weight:600;margin-bottom:6px;">{{ $acao->project->title }}</p>
                <p style="font-size:12px;color:var(--cinza-light);margin-bottom:10px;">
                    As sessões desta ação são comprovação de execução deste projeto.
                </p>
                <a href="{{ route('projects.show', $acao->project) }}" class="btn btn-ghost btn-sm" style="width:100%;text-align:center;display:block;">
                    Ver projeto →
                </a>
            </div>
        </div>
        @endif
    </div>
</div>

<script>
function toggleNovaSessao() {
    const el = document.getElementById('nova-sessao-wrap');
    el.style.display = el.style.display === 'none' ? 'block' : 'none';
}
@if($acao->sessoes->count() === 0)
// Abre o formulário automaticamente na primeira sessão
// toggleNovaSessao();
@endif
</script>
@endsection
