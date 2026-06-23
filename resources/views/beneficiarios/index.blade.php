@extends('layouts.app')
@section('page-title', 'Beneficiários')
@section('page-subtitle', $total . ' cadastrados')

@section('content')

<div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:20px;flex-wrap:wrap;gap:12px;">
    <div></div>
    <a href="{{ route('beneficiarios.create') }}" class="btn btn-primary">+ Novo beneficiário</a>
</div>

{{-- Filtros --}}
<form method="GET" class="filter-box" style="background:#fff;border:1px solid var(--cinza-borda);border-radius:10px;padding:14px 16px;margin-bottom:20px;">
    <div style="display:flex;gap:8px;flex-wrap:wrap;align-items:center;">
        <input type="text" name="q" placeholder="Buscar por nome..." value="{{ request('q') }}"
               style="flex:1;min-width:180px;border:1px solid var(--cinza-borda);border-radius:8px;padding:7px 11px;font-size:13px;font-family:'Poppins',sans-serif;">
        <select name="status" style="border:1px solid var(--cinza-borda);border-radius:8px;padding:7px 11px;font-size:13px;font-family:'Poppins',sans-serif;">
            <option value="">Todos os status</option>
            <option value="ativo"   {{ request('status') === 'ativo'   ? 'selected' : '' }}>Ativos</option>
            <option value="inativo" {{ request('status') === 'inativo' ? 'selected' : '' }}>Inativos</option>
        </select>
        <select name="faixa" style="border:1px solid var(--cinza-borda);border-radius:8px;padding:7px 11px;font-size:13px;font-family:'Poppins',sans-serif;">
            <option value="">Todas as faixas</option>
            <option value="crianca"      {{ request('faixa') === 'crianca'      ? 'selected' : '' }}>Crianças (0–11)</option>
            <option value="adolescente"  {{ request('faixa') === 'adolescente'  ? 'selected' : '' }}>Adolescentes (12–17)</option>
            <option value="adulto"       {{ request('faixa') === 'adulto'       ? 'selected' : '' }}>Adultos (18+)</option>
            <option value="sem_dob"      {{ request('faixa') === 'sem_dob'      ? 'selected' : '' }}>Sem data de nasc.</option>
        </select>
        <button class="btn btn-primary btn-sm" type="submit">Filtrar</button>
        <a href="{{ route('beneficiarios.index') }}" class="btn btn-ghost btn-sm">Limpar</a>
    </div>
</form>

<div class="card">
    <div class="table-wrapper">
        <table class="data-table">
            <thead>
                <tr>
                    <th>Nome</th>
                    <th>Idade / Faixa</th>
                    <th>CPF / Responsável</th>
                    <th>Contato</th>
                    <th>Status</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                @forelse($beneficiarios as $b)
                <tr>
                    <td>
                        <div style="font-weight:600;color:var(--texto);">{{ $b->nome }}</div>
                        @if($b->bairro)<div style="font-size:11.5px;color:var(--cinza-light);">{{ $b->bairro }}</div>@endif
                    </td>
                    <td>
                        @if($b->data_nascimento)
                            <div>{{ $b->idade }} anos</div>
                            <div style="font-size:11px;color:var(--cinza-light);">{{ $b->data_nascimento->format('d/m/Y') }}</div>
                        @else
                            <span style="color:var(--cinza-light);font-size:12px;">Não informado</span>
                        @endif
                    </td>
                    <td>
                        @if($b->cpf)
                            <div style="font-size:12.5px;">{{ $b->cpf }}</div>
                        @else
                            <span style="font-size:11px;background:#fff8e1;color:#e65100;padding:2px 7px;border-radius:10px;">Sem CPF</span>
                        @endif
                        @if($b->nome_responsavel)
                            <div style="font-size:11.5px;color:var(--cinza-light);margin-top:2px;">Resp.: {{ $b->nome_responsavel }}</div>
                        @endif
                    </td>
                    <td style="font-size:12.5px;">
                        {{ $b->telefone ?? '—' }}
                    </td>
                    <td>
                        @if($b->status === 'ativo')
                            <span style="font-size:11px;background:#e8f5e9;color:#2e7d32;padding:2px 9px;border-radius:20px;">Ativo</span>
                        @else
                            <span style="font-size:11px;background:#f5f5f5;color:#757575;padding:2px 9px;border-radius:20px;">Inativo</span>
                        @endif
                    </td>
                    <td style="text-align:right;">
                        <a href="{{ route('beneficiarios.show', $b) }}" class="btn btn-ghost btn-sm">Ver</a>
                        <a href="{{ route('beneficiarios.edit', $b) }}" class="btn btn-ghost btn-sm">Editar</a>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" style="text-align:center;padding:40px;color:var(--cinza-light);">
                        <div style="font-size:28px;margin-bottom:8px;">👥</div>
                        <p>Nenhum beneficiário encontrado.</p>
                        <a href="{{ route('beneficiarios.create') }}" class="btn btn-primary btn-sm" style="margin-top:10px;">Cadastrar primeiro beneficiário</a>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<div style="margin-top:16px;">{{ $beneficiarios->links() }}</div>

@endsection
