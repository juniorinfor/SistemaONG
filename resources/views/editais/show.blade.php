@extends('layouts.app')

@section('page-title', 'Edital')

@push('styles')
<style>
.detail-grid { display: grid; grid-template-columns: 1fr 320px; gap: 20px; }
.info-row { display: flex; flex-direction: column; margin-bottom: 14px; }
.info-label { font-size: 10px; font-weight: 700; text-transform: uppercase; letter-spacing: .6px; color: var(--cinza-light); margin-bottom: 3px; }
.info-value { font-size: 14px; color: var(--texto); }
.criterios-list { font-size: 13px; line-height: 1.9; color: var(--texto); white-space: pre-line; }
.compat-ring {
    width: 90px; height: 90px;
    border-radius: 50%;
    display: flex; align-items: center; justify-content: center;
    font-size: 22px; font-weight: 700;
    margin: 0 auto 12px;
    border: 6px solid #eee;
}
.compat-ring.high { border-color: var(--teal); color: var(--teal); }
.compat-ring.mid  { border-color: var(--amarelo); color: var(--amarelo); }
.compat-ring.low  { border-color: #e53935; color: #e53935; }
.compat-item { display: flex; align-items: flex-start; gap: 8px; font-size: 13px; margin-bottom: 6px; }
.compat-item .icon { flex-shrink: 0; margin-top: 1px; }
.attach-item {
    display: flex; align-items: center; gap: 10px;
    padding: 10px 12px; border: 1px solid var(--cinza-borda);
    border-radius: 8px; margin-bottom: 8px; background: #fff;
}
.attach-tipo {
    font-size: 10px; font-weight: 600; text-transform: uppercase;
    padding: 2px 7px; border-radius: 10px; background: #f3e5f5; color: #6a1b9a;
    flex-shrink: 0;
}
</style>
@endpush

@section('content')

@php
    $score = $edital->compatibility_score;
    $details = $edital->compatibility_details ?? [];
    $ps = $edital->prazo_status;
    $fonteLabel = match($edital->fonte) {
        'transferegov' => 'Transferegov (Gov Federal)',
        'iati'         => 'IATI (Internacional)',
        'dou'          => 'Diário Oficial da União',
        'upload'       => 'Edital enviado (análise por IA)',
        default        => 'Cadastro Manual',
    };
@endphp

{{-- Cabeçalho --}}
<div style="display:flex;align-items:flex-start;justify-content:space-between;gap:16px;margin-bottom:20px;flex-wrap:wrap;">
    <div style="flex:1;min-width:0;">
        <a href="{{ route('editais.index') }}" style="font-size:12px;color:var(--cinza-light);text-decoration:none;">← Radar de Editais</a>
        <h1 style="font-size:18px;font-weight:700;color:var(--texto);margin:4px 0 6px;line-height:1.4;">{{ $edital->titulo }}</h1>
        <div style="display:flex;gap:8px;flex-wrap:wrap;">
            @if($edital->area)
                <span style="font-size:12px;padding:2px 10px;border-radius:20px;background:#e3f2fd;color:#1565c0;">{{ ucfirst($edital->area) }}</span>
            @endif
            <span style="font-size:12px;padding:2px 10px;border-radius:20px;background:#f3e5f5;color:#6a1b9a;">{{ $fonteLabel }}</span>
        </div>
    </div>
    <div style="display:flex;gap:8px;flex-shrink:0;flex-wrap:wrap;">
        @if($edital->link_submissao)
            <a href="{{ $edital->link_submissao }}" target="_blank" class="btn btn-primary btn-sm"
               style="background:#1565c0;border-color:#1565c0;">
                📤 Enviar proposta
            </a>
        @endif
        @if($edital->link_oficial)
            <a href="{{ $edital->link_oficial }}" target="_blank" class="btn btn-ghost btn-sm">↗ Site oficial</a>
        @endif
        <form method="POST" action="{{ route('editais.destroy', $edital) }}" onsubmit="return confirm('Remover este edital?')">
            @csrf @method('DELETE')
            <button class="btn btn-ghost btn-sm" style="color:#c62828;" type="submit">Remover</button>
        </form>
    </div>
</div>

@if(session('success'))
    <div class="alert alert-success" style="margin-bottom:16px;">{{ session('success') }}</div>
@endif
@if(session('error'))
    <div class="alert alert-danger" style="margin-bottom:16px;">{{ session('error') }}</div>
@endif

<div class="detail-grid">
    {{-- Coluna esquerda --}}
    <div>
        {{-- Informações principais --}}
        <div class="card" style="margin-bottom:16px;">
            <div class="card-header"><span class="card-title">Informações do Edital</span></div>
            <div class="card-body">
                <div style="display:grid;grid-template-columns:1fr 1fr;gap:0 20px;">
                    <div class="info-row">
                        <span class="info-label">Prazo de inscrição</span>
                        <span class="info-value" style="color:{{ $ps==='urgente'?'#c62828':($ps==='breve'?'#e65100':'inherit') }};">
                            {{ $edital->prazo_inscricao?->format('d/m/Y') ?? '—' }}
                            @if($ps === 'urgente') <strong>(URGENTE)</strong>
                            @elseif($ps === 'breve') <strong>(Em breve)</strong>
                            @endif
                        </span>
                    </div>
                    <div class="info-row">
                        <span class="info-label">Prazo de execução</span>
                        <span class="info-value">{{ $edital->prazo_execucao?->format('d/m/Y') ?? '—' }}</span>
                    </div>
                    <div class="info-row">
                        <span class="info-label">Valor</span>
                        <span class="info-value" style="font-weight:600;color:var(--teal);">
                            {{ $edital->valor_formatado ?? '—' }}
                        </span>
                    </div>
                    <div class="info-row">
                        <span class="info-label">Fonte</span>
                        <span class="info-value">{{ $fonteLabel }}</span>
                    </div>
                </div>
                @if($edital->resumo)
                    <div class="info-row" style="margin-top:8px;">
                        <span class="info-label">Resumo</span>
                        <span class="info-value" style="line-height:1.6;">{{ $edital->resumo }}</span>
                    </div>
                @endif
            </div>
        </div>

        {{-- Critérios / Requisitos --}}
        @if($edital->criterios)
        <div class="card" style="margin-bottom:16px;">
            <div class="card-header"><span class="card-title">Critérios de Habilitação</span></div>
            <div class="card-body">
                <div class="criterios-list">{{ $edital->criterios }}</div>
            </div>
        </div>
        @endif

        {{-- Projetos sugeridos (Fase 2) --}}
        <div class="card" style="margin-bottom:16px;">
            <div class="card-header">
                <span class="card-title">Projetos sugeridos para este edital</span>
                @if($edital->suggestions_at)
                    <span style="font-size:11px;color:var(--cinza-light);">Gerado {{ $edital->suggestions_at->diffForHumans() }}</span>
                @endif
            </div>
            <div class="card-body">
                @php $sugestoes = $edital->project_suggestions ?? []; @endphp

                @if(!empty($sugestoes))
                    @foreach($sugestoes as $i => $s)
                    @php
                        $ad     = (int) ($s['aderencia'] ?? 0);
                        $cor    = $ad >= 70 ? '#00897b' : ($ad >= 40 ? '#e65100' : '#c62828');
                        $isNovo = ($s['tipo'] ?? '') === 'novo' || empty($s['project_id']);
                    @endphp
                    <div style="padding:14px 0;{{ $i > 0 ? 'border-top:1px solid var(--cinza-borda);' : '' }}">
                        <div style="display:flex;align-items:center;gap:8px;margin-bottom:6px;">
                            <span style="font-size:16px;">{{ $i === 0 ? '🎯' : ($isNovo ? '💡' : '•') }}</span>
                            <span style="font-size:14px;font-weight:600;color:var(--texto);flex:1;">{{ $s['titulo'] ?? 'Projeto' }}</span>
                            @if($isNovo)
                                <span style="font-size:10px;font-weight:700;padding:2px 8px;border-radius:20px;background:#fff8e1;color:#f57f17;border:1px solid #ffe082;white-space:nowrap;">Ideia nova</span>
                            @else
                                <span style="font-size:10px;font-weight:700;padding:2px 8px;border-radius:20px;background:#e8f5e9;color:#2e7d32;border:1px solid #a5d6a7;white-space:nowrap;">Portfólio</span>
                            @endif
                            <span style="font-size:13px;font-weight:700;color:{{ $cor }};">{{ $ad }}%</span>
                        </div>
                        <div style="height:5px;background:#eee;border-radius:4px;overflow:hidden;margin-bottom:8px;">
                            <div style="height:100%;width:{{ $ad }}%;background:{{ $cor }};border-radius:4px;"></div>
                        </div>
                        @if(!empty($s['justificativa']))
                            <p style="font-size:12.5px;color:var(--texto);line-height:1.5;margin-bottom:4px;">{{ $s['justificativa'] }}</p>
                        @endif
                        @if($isNovo && !empty($s['descricao']))
                            <p style="font-size:12px;color:var(--cinza);line-height:1.5;margin-bottom:6px;font-style:italic;">{{ $s['descricao'] }}</p>
                        @endif
                        @if(!$isNovo && !empty($s['ajustes']))
                            <p style="font-size:12px;color:var(--cinza-light);line-height:1.5;margin-bottom:8px;">
                                <strong>Ajustar:</strong> {{ $s['ajustes'] }}
                            </p>
                        @endif
                        <div style="display:flex;gap:6px;align-items:center;">
                            @if(!$isNovo && !empty($s['project_id']))
                                <a href="{{ route('projects.show', $s['project_id']) }}" class="btn btn-ghost btn-sm" style="font-size:11px;">Ver base</a>
                            @endif
                            <form method="POST" action="{{ route('editais.gerar-projeto', $edital) }}" style="display:inline;">
                                @csrf
                                <input type="hidden" name="project_id"      value="{{ $s['project_id'] ?? '' }}">
                                <input type="hidden" name="titulo_base"     value="{{ $s['titulo'] ?? '' }}">
                                <input type="hidden" name="area_base"       value="{{ $s['area'] ?? '' }}">
                                <input type="hidden" name="descricao_base"  value="{{ $s['descricao'] ?? '' }}">
                                <button type="submit" class="btn btn-primary btn-sm" style="font-size:11px;"
                                        onclick="this.innerHTML='⏳ Gerando projeto...';this.form.style.pointerEvents='none';">
                                    {{ $isNovo ? '✨ Gerar e incluir no portfólio' : '✨ Gerar projeto com IA' }}
                                </button>
                            </form>
                        </div>
                    </div>
                    @endforeach

                    <form method="POST" action="{{ route('editais.sugerir', $edital) }}" style="margin-top:12px;">
                        @csrf
                        <button class="btn btn-ghost btn-sm" type="submit"
                                onclick="this.innerHTML='⏳ Analisando...';this.style.pointerEvents='none';">
                            ↻ Gerar novas sugestões
                        </button>
                    </form>
                @else
                    <div style="text-align:center;color:var(--cinza-light);padding:12px 0;">
                        <div style="font-size:28px;margin-bottom:8px;">💡</div>
                        <p style="font-size:13px;margin-bottom:14px;">A IA analisa seu portfólio de projetos e sugere os 3 mais aderentes a este edital.</p>
                        <form method="POST" action="{{ route('editais.sugerir', $edital) }}">
                            @csrf
                            <button class="btn btn-primary btn-sm" type="submit"
                                    onclick="this.innerHTML='⏳ Analisando portfólio...';this.style.pointerEvents='none';">
                                💡 Sugerir projetos para este edital
                            </button>
                        </form>
                    </div>
                @endif
            </div>
        </div>

        {{-- Anexos --}}
        @if($edital->attachments->count())
        <div class="card">
            <div class="card-header"><span class="card-title">Anexos ({{ $edital->attachments->count() }})</span></div>
            <div class="card-body">
                @foreach($edital->attachments as $att)
                <div class="attach-item">
                    <span class="attach-tipo">{{ $att->tipo_label }}</span>
                    <span style="flex:1;font-size:13px;">{{ $att->nome }}</span>
                    @if($att->arquivo_path)
                        <a href="{{ route('editais.attachment.download', $att) }}" class="btn btn-ghost btn-sm">Download</a>
                    @elseif($att->link)
                        <a href="{{ $att->link }}" target="_blank" class="btn btn-ghost btn-sm">Abrir</a>
                    @endif
                </div>
                @endforeach
            </div>
        </div>
        @endif
    </div>

    {{-- Coluna direita — Compatibilidade --}}
    <div>
        <div class="card" style="margin-bottom:16px;">
            <div class="card-header"><span class="card-title">Compatibilidade Documental</span></div>
            <div class="card-body" style="text-align:center;">
                @if($score !== null)
                    @php $cls = $score >= 70 ? 'high' : ($score >= 40 ? 'mid' : 'low'); @endphp
                    <div class="compat-ring {{ $cls }}">{{ $score }}%</div>
                    <p style="font-size:13px;color:var(--cinza-light);margin-bottom:16px;">
                        {{ $details['observacao'] ?? '' }}
                    </p>
                    @if(!empty($details['matched']))
                        <div style="text-align:left;margin-bottom:12px;">
                            <div style="font-size:11px;font-weight:700;text-transform:uppercase;color:var(--cinza-light);margin-bottom:6px;">Documentos atendidos</div>
                            @foreach($details['matched'] as $m)
                                <div class="compat-item">
                                    <span class="icon" style="color:var(--teal);">✔</span>
                                    <span>{{ $m }}</span>
                                </div>
                            @endforeach
                        </div>
                    @endif
                    @if(!empty($details['missing']))
                        <div style="text-align:left;margin-bottom:16px;">
                            <div style="font-size:11px;font-weight:700;text-transform:uppercase;color:var(--cinza-light);margin-bottom:6px;">Documentos faltando</div>
                            @foreach($details['missing'] as $m)
                                @php
                                    // Tenta encontrar o tipo no catálogo por similaridade de nome
                                    $match = $documentTypes->first(function($dt) use ($m) {
                                        return stripos($dt->name, $m) !== false
                                            || stripos($m, $dt->name) !== false;
                                    });
                                @endphp
                                <div style="background:#fff8f8;border:1px solid #ffcdd2;border-radius:8px;padding:10px 12px;margin-bottom:8px;">
                                    <div style="display:flex;align-items:flex-start;gap:8px;margin-bottom:{{ $match ? '8px' : '0' }};">
                                        <span style="color:#e53935;flex-shrink:0;margin-top:1px;">✕</span>
                                        <span style="font-size:13px;font-weight:600;color:var(--texto);">{{ $m }}</span>
                                    </div>
                                    @if($match)
                                        @if($match->instructions)
                                            <p style="font-size:11.5px;color:var(--cinza);line-height:1.5;margin:0 0 6px 20px;white-space:pre-line;">{{ $match->instructions }}</p>
                                        @endif
                                        <div style="display:flex;gap:6px;margin-left:20px;flex-wrap:wrap;">
                                            @if($match->official_url)
                                                <a href="{{ $match->official_url }}" target="_blank"
                                                   style="font-size:11px;color:#00897b;font-weight:600;text-decoration:none;background:#e0f7f4;padding:3px 9px;border-radius:20px;">
                                                    ↗ Obter documento
                                                </a>
                                            @endif
                                            <a href="{{ route('documents.create') }}?type_id={{ $match->id }}"
                                               style="font-size:11px;color:#1565c0;font-weight:600;text-decoration:none;background:#e3f2fd;padding:3px 9px;border-radius:20px;">
                                                + Fazer upload
                                            </a>
                                        </div>
                                    @else
                                        <p style="font-size:11px;color:var(--cinza-light);margin:4px 0 0 20px;">Não encontrado no catálogo. <a href="{{ route('document-types.index') }}" style="color:var(--teal);">Adicionar ao catálogo →</a></p>
                                    @endif
                                </div>
                            @endforeach
                        </div>
                    @endif
                @else
                    <div style="color:var(--cinza-light);font-size:13px;padding:16px 0;">
                        <div style="font-size:32px;margin-bottom:8px;">📋</div>
                        <p>A análise de compatibilidade verifica quais documentos exigidos pelo edital você já possui cadastrados.</p>
                    </div>
                @endif

                @if($edital->criterios)
                <form method="POST" action="{{ route('editais.compatibility', $edital) }}">
                    @csrf
                    <button class="btn btn-primary btn-sm" style="width:100%;">
                        {{ $score !== null ? '↻ Reanalisar' : '🔍 Verificar compatibilidade' }}
                    </button>
                </form>
                <p style="font-size:11px;color:var(--cinza-light);margin-top:6px;">
                    Usa IA (Claude Haiku) — ~0,001 crédito por análise
                </p>
                @else
                <p style="font-size:12px;color:var(--cinza-light);">
                    Adicione os critérios de habilitação para habilitar a análise.
                </p>
                @endif
            </div>
        </div>

        {{-- Enviar proposta --}}
        <div class="card" style="margin-bottom:16px;">
            <div class="card-header"><span class="card-title">Envio de proposta</span></div>
            <div class="card-body">
                @if($edital->link_submissao)
                    <a href="{{ $edital->link_submissao }}" target="_blank"
                       class="btn btn-primary btn-sm" style="width:100%;text-align:center;background:#1565c0;border-color:#1565c0;display:block;margin-bottom:10px;">
                        📤 Acessar plataforma de envio
                    </a>
                    <p style="font-size:11px;color:var(--cinza-light);text-align:center;margin:0;">
                        Link extraído automaticamente do edital pela IA
                    </p>
                @else
                    <p style="font-size:12.5px;color:var(--cinza-light);margin-bottom:10px;">
                        Nenhum link de envio identificado no edital.<br>
                        Adicione manualmente se encontrar no edital.
                    </p>
                @endif

                {{-- Campo para editar/adicionar o link manualmente --}}
                <form method="POST" action="{{ route('editais.update-submissao', $edital) }}" style="margin-top:10px;">
                    @csrf @method('PATCH')
                    <div style="display:flex;gap:6px;">
                        <input type="url" name="link_submissao"
                               value="{{ $edital->link_submissao }}"
                               placeholder="https://plataforma-do-edital.gov.br/..."
                               style="flex:1;font-size:12px;padding:7px 10px;border:1px solid var(--cinza-borda);border-radius:8px;font-family:'Roboto',sans-serif;color:var(--texto);">
                        <button type="submit" class="btn btn-ghost btn-sm" style="white-space:nowrap;">Salvar</button>
                    </div>
                </form>
            </div>
        </div>

        {{-- Ação rápida: ver checklist de docs --}}
        <div class="card">
            <div class="card-header"><span class="card-title">Ações rápidas</span></div>
            <div class="card-body" style="display:flex;flex-direction:column;gap:8px;">
                <a href="{{ route('document-types.index') }}" class="btn btn-ghost btn-sm" style="text-align:center;">
                    📋 Ver catálogo de documentos
                </a>
                <a href="{{ route('checklists.index') }}" class="btn btn-ghost btn-sm" style="text-align:center;">
                    ✔ Ver checklists de prontidão
                </a>
                @if($edital->link_oficial)
                    <a href="{{ $edital->link_oficial }}" target="_blank" class="btn btn-ghost btn-sm" style="text-align:center;">
                        ↗ Acessar edital completo
                    </a>
                @endif
            </div>
        </div>
    </div>
</div>

@endsection
