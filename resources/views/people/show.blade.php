@extends('layouts.app')

@section('page-title', $person->name)
@section('page-subtitle', $person->type_label . ($person->role ? ' — ' . $person->role : ''))

@section('content')

<div style="display:grid;grid-template-columns:1fr 300px;gap:20px;max-width:900px;">

{{-- Coluna principal --}}
<div>

    <div class="card mb-4">
        <div class="card-header">
            <span class="card-title">Informações</span>
            <a href="{{ route('people.edit', $person) }}" class="btn btn-ghost btn-sm">Editar</a>
        </div>
        <div class="card-body">
            <div style="display:grid;grid-template-columns:1fr 1fr;gap:0;">
                @php
                    $fields = [
                        ['Nome',     $person->name],
                        ['Tipo',     $person->type_label],
                        ['Cargo',    $person->role ?? '—'],
                        ['CPF',      $person->cpf ?? '—'],
                        ['RG',       $person->rg ?? '—'],
                        ['E-mail',   $person->email ?? '—'],
                        ['Telefone', $person->phone ?? '—'],
                    ];
                @endphp
                @foreach($fields as $i => [$label, $value])
                    <div style="padding:12px 0;{{ $i > 0 ? 'border-top:1px solid var(--cinza-borda);' : '' }}">
                        <div style="font-size:10.5px;font-weight:600;text-transform:uppercase;letter-spacing:.05em;color:var(--cinza-light);margin-bottom:3px;">{{ $label }}</div>
                        <div style="font-size:13px;color:var(--texto);">{{ $value }}</div>
                    </div>
                @endforeach
            </div>

            @if($person->mandate_start || $person->mandate_end)
                <div style="margin-top:12px;padding-top:12px;border-top:1px solid var(--cinza-borda);">
                    <div style="font-size:10.5px;font-weight:600;text-transform:uppercase;letter-spacing:.05em;color:var(--cinza-light);margin-bottom:4px;">Mandato</div>
                    <div style="font-size:13px;color:var(--texto);">
                        {{ $person->mandate_start?->format('d/m/Y') ?? '?' }} —
                        {{ $person->mandate_end ? $person->mandate_end->format('d/m/Y') : 'em exercício' }}
                        @if($person->isMandateActive())
                            <span class="badge badge-valido" style="margin-left:8px;"><span class="badge-dot"></span>Em exercício</span>
                        @else
                            <span class="badge badge-faltante" style="margin-left:8px;"><span class="badge-dot"></span>Encerrado</span>
                        @endif
                    </div>
                </div>
            @endif

            @if($person->works_with_children)
                <div style="margin-top:12px;padding:10px 14px;background:var(--amarelo-light);border-radius:8px;font-size:12px;color:#8a5e00;">
                    Trabalha com crianças/adolescentes — documentos adicionais requeridos
                </div>
            @endif
        </div>
    </div>

    {{-- Documentos da pessoa --}}
    <div class="card">
        <div class="card-header">
            <span class="card-title">Documentos ({{ $person->documents->count() }})</span>
            <a href="{{ route('documents.create') }}" class="btn btn-primary btn-sm">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" style="width:13px;height:13px;"><path d="M12 5v14M5 12h14"/></svg>
                Enviar
            </a>
        </div>
        @if($person->documents->count())
        <div class="table-wrapper">
            <table class="data-table">
                <thead>
                    <tr><th>Documento</th><th>Status</th><th>Vencimento</th><th></th></tr>
                </thead>
                <tbody>
                    @foreach($person->documents as $doc)
                        @php
                            $sc = match($doc->status) {
                                'valido'         => 'badge-valido',
                                'vence_em_breve' => 'badge-em-breve',
                                'vencido'        => 'badge-vencido',
                                default          => 'badge-faltante',
                            };
                            $sl = match($doc->status) {
                                'valido'         => 'Válido',
                                'vence_em_breve' => 'Vencendo',
                                'vencido'        => 'Vencido',
                                default          => 'Sem validade',
                            };
                        @endphp
                        <tr>
                            <td style="font-weight:500;">{{ $doc->documentType->name }}</td>
                            <td><span class="badge {{ $sc }}"><span class="badge-dot"></span>{{ $sl }}</span></td>
                            <td style="font-size:12px;color:var(--cinza-light);">{{ $doc->expires_at?->format('d/m/Y') ?? '—' }}</td>
                            <td><a href="{{ route('documents.show', $doc) }}" class="btn btn-ghost btn-sm">Ver</a></td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @else
            <div class="card-body" style="text-align:center;color:var(--cinza-light);font-size:13px;">
                Nenhum documento vinculado a esta pessoa.
            </div>
        @endif
    </div>

</div>

{{-- Coluna lateral --}}
<div>
    <div class="card">
        <div class="card-body" style="display:flex;flex-direction:column;gap:10px;">
            <a href="{{ route('people.edit', $person) }}" class="btn btn-primary btn-sm" style="text-align:center;">Editar dados</a>
            <a href="{{ route('documents.create') }}" class="btn btn-ghost btn-sm" style="text-align:center;">Enviar documento</a>
            <form method="POST" action="{{ route('people.destroy', $person) }}"
                  onsubmit="return confirm('Desativar {{ $person->name }}?')">
                @csrf @method('DELETE')
                <button type="submit" class="btn btn-ghost btn-sm" style="width:100%;color:#e53935;border-color:transparent;">
                    {{ $person->is_active ? 'Desativar' : 'Já inativa' }}
                </button>
            </form>
        </div>
    </div>
</div>

</div>

@endsection
