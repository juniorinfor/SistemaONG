@extends('layouts.app')

@section('page-title', 'Projeto')
@section('page-subtitle', Str::limit($project->title, 50))

@section('content')

<div style="display:grid;grid-template-columns:1fr 280px;gap:20px;align-items:flex-start;max-width:1060px;">

{{-- Principal --}}
<div>

    {{-- Header do projeto --}}
    <div class="card" style="margin-bottom:16px;">
        <div class="card-body">
            <div style="display:flex;align-items:flex-start;justify-content:space-between;gap:16px;flex-wrap:wrap;">
                <div style="flex:1;min-width:0;">
                    <div style="display:flex;align-items:center;gap:10px;flex-wrap:wrap;margin-bottom:8px;">
                        <span style="font-size:12px;padding:3px 12px;border-radius:20px;font-weight:600;background:{{ $project->statusBg }};color:{{ $project->statusColor }};border:1px solid {{ $project->statusColor }}30;">
                            {{ $project->statusLabel }}
                        </span>
                        @if($project->area)
                        <span style="font-size:12px;padding:3px 10px;border-radius:20px;background:#e3f2fd;color:#1565c0;border:1px solid #bbdefb;">
                            {{ ucfirst($project->area) }}
                        </span>
                        @endif
                    </div>
                    <h2 style="font-size:18px;font-weight:700;color:var(--texto);margin:0 0 6px;">{{ $project->title }}</h2>
                    <div style="font-size:12px;color:var(--cinza-light);">
                        Criado em {{ $project->created_at->format('d/m/Y') }}
                        · Atualizado {{ $project->updated_at->diffForHumans() }}
                    </div>
                </div>
                <div style="display:flex;gap:8px;flex-shrink:0;">
                    <a href="{{ route('projects.edit', $project) }}" class="btn btn-ghost btn-sm">Editar</a>
                    <form method="POST" action="{{ route('projects.destroy', $project) }}"
                          onsubmit="return confirm('Remover este projeto?')">
                        @csrf @method('DELETE')
                        <button type="submit" class="btn btn-ghost btn-sm" style="color:#e53935;">Remover</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    {{-- Edital vinculado --}}
    @if($project->edital)
    <div class="card" style="margin-bottom:16px;">
        <div class="card-header">
            <span class="card-title">Edital vinculado</span>
        </div>
        <div class="card-body">
            <div style="font-size:14px;font-weight:500;color:var(--texto);margin-bottom:4px;">
                {{ $project->edital->titulo }}
            </div>
            <div style="font-size:12px;color:var(--cinza-light);margin-bottom:10px;">
                @if($project->edital->prazo_inscricao)
                    Prazo: {{ $project->edital->prazo_inscricao->format('d/m/Y') }} ·
                @endif
                Fonte: {{ $project->edital->fonte }}
            </div>
            <a href="{{ route('editais.show', $project->edital) }}" class="btn btn-ghost btn-sm">
                Ver edital completo →
            </a>
        </div>
    </div>
    @endif

    {{-- Descrição --}}
    @if($project->description)
    <div class="card" style="margin-bottom:16px;">
        <div class="card-header">
            <span class="card-title">Objeto / Descrição</span>
        </div>
        <div class="card-body">
            <div style="font-size:14px;color:var(--texto);line-height:1.7;white-space:pre-wrap;">{{ $project->description }}</div>
        </div>
    </div>
    @endif

    {{-- Observações --}}
    @if($project->notes)
    <div class="card" style="margin-bottom:16px;">
        <div class="card-header">
            <span class="card-title">Observações</span>
        </div>
        <div class="card-body">
            <div style="font-size:13px;color:var(--cinza);line-height:1.6;white-space:pre-wrap;">{{ $project->notes }}</div>
        </div>
    </div>
    @endif

    {{-- Anexos (proposta, plano, orçamento...) --}}
    <div class="card" style="margin-bottom:16px;">
        <div class="card-header">
            <span class="card-title">Documentos do projeto</span>
            <span style="font-size:12px;color:var(--cinza-light);">{{ $project->attachments->count() }} arquivo(s)</span>
        </div>
        <div class="card-body">

            @forelse($project->attachments as $att)
            <div style="display:flex;align-items:center;gap:12px;padding:10px 0;{{ !$loop->first ? 'border-top:1px solid var(--cinza-borda);' : '' }}">
                <div style="font-size:22px;flex-shrink:0;">{{ $att->icone }}</div>
                <div style="flex:1;min-width:0;">
                    <div style="font-size:13px;font-weight:500;color:var(--texto);overflow:hidden;text-overflow:ellipsis;white-space:nowrap;">{{ $att->nome }}</div>
                    <div style="font-size:11px;color:var(--cinza-light);">
                        <span style="background:#e3f2fd;color:#1565c0;padding:1px 8px;border-radius:12px;">{{ $att->tipoLabel }}</span>
                        · {{ $att->tamanhoFormatado }}
                        · {{ $att->created_at->format('d/m/Y') }}
                    </div>
                </div>
                <div style="display:flex;gap:6px;flex-shrink:0;">
                    <a href="{{ route('projects.attachments.download', [$project, $att]) }}" class="btn btn-ghost btn-sm" title="Baixar">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="width:13px;height:13px;"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/></svg>
                    </a>
                    <form method="POST" action="{{ route('projects.attachments.destroy', [$project, $att]) }}"
                          onsubmit="return confirm('Remover este anexo?')" style="display:inline;">
                        @csrf @method('DELETE')
                        <button type="submit" class="btn btn-ghost btn-sm" style="color:#e53935;" title="Remover">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="width:13px;height:13px;"><polyline points="3 6 5 6 21 6"/><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"/></svg>
                        </button>
                    </form>
                </div>
            </div>
            @empty
            <p style="font-size:13px;color:var(--cinza-light);margin:0 0 14px;">Nenhum documento anexado ainda. Anexe a proposta, o plano de trabalho ou o orçamento.</p>
            @endforelse

            {{-- Formulário de upload --}}
            <form method="POST" action="{{ route('projects.attachments.store', $project) }}"
                  enctype="multipart/form-data"
                  style="margin-top:16px;padding-top:16px;border-top:1px solid var(--cinza-borda);">
                @csrf
                <div style="display:flex;gap:10px;flex-wrap:wrap;align-items:flex-end;">
                    <div style="flex:1;min-width:200px;">
                        <label class="form-label" style="font-size:11px;">Arquivo (PDF, DOC, XLS, imagem · máx. 20 MB)</label>
                        <input type="file" name="arquivo" class="form-control @error('arquivo') is-invalid @enderror"
                               accept=".pdf,.doc,.docx,.xls,.xlsx,.jpg,.jpeg,.png" required>
                    </div>
                    <div style="min-width:150px;">
                        <label class="form-label" style="font-size:11px;">Tipo</label>
                        <select name="tipo" class="form-control">
                            <option value="proposta">Proposta</option>
                            <option value="plano">Plano de trabalho</option>
                            <option value="orcamento">Orçamento</option>
                            <option value="relatorio">Relatório</option>
                            <option value="anexo">Anexo</option>
                        </select>
                    </div>
                    <button type="submit" class="btn btn-primary btn-sm">Anexar</button>
                </div>
                @error('arquivo')
                    <div class="field-error" style="margin-top:6px;">{{ $message }}</div>
                @enderror
            </form>

        </div>
    </div>

</div>

{{-- Lateral --}}
<div style="position:sticky;top:24px;display:flex;flex-direction:column;gap:14px;">

    {{-- Valores --}}
    <div class="card">
        <div class="card-header"><span class="card-title">Valores</span></div>
        <div class="card-body" style="padding:14px 18px;">
            <div style="margin-bottom:12px;">
                <div style="font-size:11px;text-transform:uppercase;letter-spacing:.05em;color:var(--cinza-light);margin-bottom:3px;">Pleiteado</div>
                <div style="font-size:18px;font-weight:700;color:var(--texto);">
                    {{ $project->valorPleiteadoFormatado ?? '—' }}
                </div>
            </div>
            @if($project->valor_aprovado)
            <div>
                <div style="font-size:11px;text-transform:uppercase;letter-spacing:.05em;color:var(--cinza-light);margin-bottom:3px;">Aprovado</div>
                <div style="font-size:18px;font-weight:700;color:#00897b;">
                    {{ $project->valorAprovadoFormatado }}
                </div>
            </div>
            @endif
        </div>
    </div>

    {{-- Linha do tempo --}}
    <div class="card">
        <div class="card-header"><span class="card-title">Linha do tempo</span></div>
        <div class="card-body" style="padding:14px 18px;">
            @php
            $timeline = [
                ['Submetido em',    $project->submitted_at],
                ['Aprovado em',     $project->approved_at],
                ['Início execução', $project->start_date],
                ['Término previsto',$project->end_date],
            ];
            $hasAny = collect($timeline)->filter(fn($t) => $t[1])->isNotEmpty();
            @endphp
            @if($hasAny)
                @foreach($timeline as [$lbl, $date])
                @if($date)
                <div style="display:flex;justify-content:space-between;align-items:center;padding:6px 0;border-bottom:1px solid var(--cinza-borda);font-size:12px;">
                    <span style="color:var(--cinza-light);">{{ $lbl }}</span>
                    <span style="font-weight:600;color:var(--texto);">{{ $date->format('d/m/Y') }}</span>
                </div>
                @endif
                @endforeach
            @else
                <p style="font-size:13px;color:var(--cinza-light);margin:0;">Nenhuma data registrada.</p>
            @endif
        </div>
    </div>

    {{-- Ações rápidas --}}
    <div style="display:flex;flex-direction:column;gap:8px;">
        <a href="{{ route('projects.edit', $project) }}" class="btn btn-primary btn-sm" style="text-align:center;">
            Editar projeto
        </a>
        <a href="{{ route('projects.create') }}" class="btn btn-ghost btn-sm" style="text-align:center;">
            + Novo projeto
        </a>
        <a href="{{ route('projects.index') }}" class="btn btn-ghost btn-sm" style="text-align:center;font-size:12px;">
            ← Voltar à lista
        </a>
    </div>

</div>
</div>

@if(session('success'))
<div class="alert alert-success" style="margin-top:16px;">{{ session('success') }}</div>
@endif

@endsection
