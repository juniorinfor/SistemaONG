@extends('layouts.app')

@section('page-title', 'Pessoas')
@section('page-subtitle', 'Diretoria, voluntários e colaboradores')

@section('content')

<div class="card">
    <div class="card-header">
        <span class="card-title">{{ $people->count() }} pessoa(s)</span>
        <a href="{{ route('people.create') }}" class="btn btn-primary btn-sm">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" style="width:14px;height:14px;"><path d="M12 5v14M5 12h14"/></svg>
            Cadastrar Pessoa
        </a>
    </div>
    <div class="table-wrapper">
        <table class="data-table">
            <thead>
                <tr>
                    <th>Nome</th>
                    <th>Tipo</th>
                    <th>Cargo</th>
                    <th>Mandato</th>
                    <th>Docs</th>
                    <th>Situação</th>
                    <th style="width:80px;"></th>
                </tr>
            </thead>
            <tbody>
                @forelse($people as $person)
                    <tr>
                        <td>
                            <div style="font-weight:500;">{{ $person->name }}</div>
                            @if($person->email)
                                <div style="font-size:11px;color:var(--cinza-light);">{{ $person->email }}</div>
                            @endif
                        </td>
                        <td>
                            <span style="background:var(--azul-light);color:var(--azul-dark);font-size:11px;font-weight:500;padding:2px 9px;border-radius:20px;">
                                {{ $person->type_label }}
                            </span>
                        </td>
                        <td style="font-size:13px;color:var(--cinza-light);">{{ $person->role ?? '—' }}</td>
                        <td style="font-size:12px;">
                            @if($person->mandate_start || $person->mandate_end)
                                {{ $person->mandate_start?->format('m/Y') ?? '?' }}
                                —
                                {{ $person->mandate_end?->format('m/Y') ?? 'atual' }}
                            @else
                                <span style="color:var(--cinza-light);">—</span>
                            @endif
                        </td>
                        <td>
                            <span style="font-size:12px;color:var(--cinza-light);">{{ $person->documents_count }}</span>
                        </td>
                        <td>
                            @if($person->is_active)
                                <span class="badge badge-valido"><span class="badge-dot"></span>Ativa</span>
                            @else
                                <span class="badge badge-faltante"><span class="badge-dot"></span>Inativa</span>
                            @endif
                        </td>
                        <td>
                            <a href="{{ route('people.show', $person) }}" class="btn btn-ghost btn-sm">Ver</a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" style="text-align:center;padding:40px;color:var(--cinza-light);">
                            Nenhuma pessoa cadastrada.
                            <a href="{{ route('people.create') }}" style="color:var(--teal);margin-left:6px;">Cadastrar a primeira</a>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

@endsection
