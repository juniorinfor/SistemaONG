@extends('layouts.app')
@section('page-title', $beneficiario->nome)

@section('content')

<div style="display:flex;align-items:flex-start;justify-content:space-between;gap:12px;margin-bottom:20px;flex-wrap:wrap;">
    <div>
        <a href="{{ route('beneficiarios.index') }}" style="font-size:12px;color:var(--cinza-light);text-decoration:none;">← Beneficiários</a>
        <h1 style="font-size:20px;font-weight:700;margin:4px 0 4px;">{{ $beneficiario->nome }}</h1>
        <div style="display:flex;gap:8px;flex-wrap:wrap;align-items:center;">
            @if($beneficiario->status === 'ativo')
                <span style="font-size:12px;background:#e8f5e9;color:#2e7d32;padding:2px 10px;border-radius:20px;">Ativo</span>
            @else
                <span style="font-size:12px;background:#f5f5f5;color:#757575;padding:2px 10px;border-radius:20px;">Inativo</span>
            @endif
            @if($beneficiario->is_menor)
                <span style="font-size:12px;background:#fff8e1;color:#e65100;padding:2px 10px;border-radius:20px;">Menor de idade</span>
            @endif
            @if($beneficiario->idade !== null)
                <span style="font-size:12px;color:var(--cinza-light);">{{ $beneficiario->idade }} anos</span>
            @endif
        </div>
    </div>
    <div style="display:flex;gap:8px;">
        <a href="{{ route('beneficiarios.edit', $beneficiario) }}" class="btn btn-ghost btn-sm">Editar</a>
        <form method="POST" action="{{ route('beneficiarios.destroy', $beneficiario) }}" onsubmit="return confirm('Remover este beneficiário?')">
            @csrf @method('DELETE')
            <button class="btn btn-ghost btn-sm" style="color:#c62828;">Remover</button>
        </form>
    </div>
</div>

<div style="display:grid;grid-template-columns:1fr 320px;gap:16px;">
    <div>
        {{-- Dados pessoais --}}
        <div class="card" style="margin-bottom:16px;">
            <div class="card-header"><span class="card-title">Dados pessoais</span></div>
            <div class="card-body">
                <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px 24px;">
                    <div>
                        <div style="font-size:10px;text-transform:uppercase;letter-spacing:.06em;color:var(--cinza-light);font-weight:700;margin-bottom:3px;">Data de nascimento</div>
                        <div style="font-size:14px;">{{ $beneficiario->data_nascimento?->format('d/m/Y') ?? '—' }}</div>
                    </div>
                    <div>
                        <div style="font-size:10px;text-transform:uppercase;letter-spacing:.06em;color:var(--cinza-light);font-weight:700;margin-bottom:3px;">CPF</div>
                        <div style="font-size:14px;">{{ $beneficiario->cpf ?? '—' }}</div>
                    </div>
                    <div>
                        <div style="font-size:10px;text-transform:uppercase;letter-spacing:.06em;color:var(--cinza-light);font-weight:700;margin-bottom:3px;">Gênero</div>
                        <div style="font-size:14px;">{{ $beneficiario->genero_label }}</div>
                    </div>
                    <div>
                        <div style="font-size:10px;text-transform:uppercase;letter-spacing:.06em;color:var(--cinza-light);font-weight:700;margin-bottom:3px;">Raça/Cor</div>
                        <div style="font-size:14px;">{{ $beneficiario->raca_cor_label }}</div>
                    </div>
                    <div>
                        <div style="font-size:10px;text-transform:uppercase;letter-spacing:.06em;color:var(--cinza-light);font-weight:700;margin-bottom:3px;">Telefone</div>
                        <div style="font-size:14px;">{{ $beneficiario->telefone ?? '—' }}</div>
                    </div>
                    <div>
                        <div style="font-size:10px;text-transform:uppercase;letter-spacing:.06em;color:var(--cinza-light);font-weight:700;margin-bottom:3px;">E-mail</div>
                        <div style="font-size:14px;">{{ $beneficiario->email ?? '—' }}</div>
                    </div>
                    @if($beneficiario->bairro || $beneficiario->cidade)
                    <div style="grid-column:span 2;">
                        <div style="font-size:10px;text-transform:uppercase;letter-spacing:.06em;color:var(--cinza-light);font-weight:700;margin-bottom:3px;">Endereço</div>
                        <div style="font-size:14px;">
                            {{ collect([$beneficiario->endereco, $beneficiario->numero])->filter()->implode(', ') }}
                            {{ $beneficiario->bairro ? '— ' . $beneficiario->bairro : '' }}
                            {{ $beneficiario->cidade ? '/ ' . $beneficiario->cidade : '' }}
                        </div>
                    </div>
                    @endif
                </div>
            </div>
        </div>

        @if($beneficiario->nome_responsavel)
        <div class="card" style="margin-bottom:16px;border-left:3px solid var(--amarelo);">
            <div class="card-header"><span class="card-title">Responsável</span></div>
            <div class="card-body">
                <div style="display:grid;grid-template-columns:1fr 1fr 1fr;gap:12px 24px;">
                    <div>
                        <div style="font-size:10px;text-transform:uppercase;letter-spacing:.06em;color:var(--cinza-light);font-weight:700;margin-bottom:3px;">Nome</div>
                        <div style="font-size:14px;font-weight:600;">{{ $beneficiario->nome_responsavel }}</div>
                    </div>
                    <div>
                        <div style="font-size:10px;text-transform:uppercase;letter-spacing:.06em;color:var(--cinza-light);font-weight:700;margin-bottom:3px;">CPF</div>
                        <div style="font-size:14px;">{{ $beneficiario->cpf_responsavel ?? '—' }}</div>
                    </div>
                    <div>
                        <div style="font-size:10px;text-transform:uppercase;letter-spacing:.06em;color:var(--cinza-light);font-weight:700;margin-bottom:3px;">Parentesco</div>
                        <div style="font-size:14px;">{{ $beneficiario->parentesco ?? '—' }}</div>
                    </div>
                </div>
            </div>
        </div>
        @endif

        @if($beneficiario->observacoes)
        <div class="card" style="margin-bottom:16px;">
            <div class="card-header"><span class="card-title">Observações</span></div>
            <div class="card-body" style="font-size:13.5px;line-height:1.6;white-space:pre-line;">{{ $beneficiario->observacoes }}</div>
        </div>
        @endif

        {{-- Histórico de presença --}}
        <div class="card">
            <div class="card-header"><span class="card-title">Histórico de participação</span></div>
            @php $sessoes = $beneficiario->sessoes->sortByDesc('data_execucao'); @endphp
            @if($sessoes->count())
            <div class="table-wrapper">
                <table class="data-table">
                    <thead><tr><th>Data</th><th>Ação</th><th>Presença</th></tr></thead>
                    <tbody>
                        @foreach($sessoes as $s)
                        <tr>
                            <td>{{ $s->data_execucao->format('d/m/Y') }}</td>
                            <td>
                                <a href="{{ route('acoes.show', $s->acao) }}" style="color:var(--teal);font-weight:500;">{{ $s->acao->titulo }}</a>
                            </td>
                            <td>
                                @if($s->pivot->presente)
                                    <span style="color:#2e7d32;font-size:13px;">✔ Presente</span>
                                @else
                                    <span style="color:#c62828;font-size:13px;">✕ Faltou</span>
                                @endif
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @else
            <div class="card-body" style="color:var(--cinza-light);font-size:13px;text-align:center;padding:24px;">
                Nenhuma participação registrada ainda.
            </div>
            @endif
        </div>
    </div>

    {{-- Coluna lateral --}}
    <div>
        <div class="card">
            <div class="card-header"><span class="card-title">Participação</span></div>
            <div class="card-body" style="text-align:center;">
                @php
                    $totalSessoes    = $beneficiario->sessoes->count();
                    $totalPresencas  = $beneficiario->sessoes->filter(fn($s) => $s->pivot->presente)->count();
                    $pct = $totalSessoes > 0 ? round($totalPresencas / $totalSessoes * 100) : 0;
                @endphp
                <div style="font-size:36px;font-weight:700;color:var(--teal);">{{ $totalPresencas }}</div>
                <div style="font-size:12px;color:var(--cinza-light);margin-bottom:12px;">presenças registradas</div>
                @if($totalSessoes > 0)
                <div style="height:6px;background:#eee;border-radius:4px;overflow:hidden;margin-bottom:6px;">
                    <div style="height:100%;width:{{ $pct }}%;background:var(--teal);border-radius:4px;"></div>
                </div>
                <div style="font-size:12px;color:var(--cinza-light);">{{ $pct }}% de frequência ({{ $totalSessoes }} sessões)</div>
                @endif
            </div>
        </div>
    </div>
</div>

@endsection
