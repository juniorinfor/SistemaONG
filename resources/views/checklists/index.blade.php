@extends('layouts.app')

@section('page-title', 'Checklists')
@section('page-subtitle', 'Prontidão para editais')

@section('content')

<div class="grid-2">
    @forelse($checklists as $cl)
        @php
            $color = $cl->pct >= 80 ? 'var(--teal)' : ($cl->pct >= 50 ? 'var(--amarelo)' : '#e53935');
            $fill  = $cl->pct < 50 ? 'danger' : ($cl->pct < 80 ? 'warn' : '');
            $borderColor = $cl->pct >= 80 ? 'var(--teal)' : ($cl->pct >= 50 ? 'var(--amarelo)' : '#e53935');
        @endphp
        <div class="card" style="border-top:3px solid {{ $borderColor }};">
            <div class="card-body" style="padding:22px;">
                <div style="display:flex;align-items:flex-start;justify-content:space-between;gap:12px;margin-bottom:14px;">
                    <div>
                        <div style="font-family:'Roboto',sans-serif;font-size:15px;font-weight:700;color:var(--texto);">{{ $cl->name }}</div>
                        @if($cl->description)
                            <div style="font-size:12px;color:var(--cinza-light);margin-top:4px;">{{ $cl->description }}</div>
                        @endif
                    </div>
                    <div style="text-align:right;flex-shrink:0;">
                        <div style="font-size:28px;font-weight:700;font-family:'Roboto',sans-serif;color:{{ $color }};line-height:1;">{{ $cl->pct }}%</div>
                        <div style="font-size:11px;color:var(--cinza-light);margin-top:2px;">prontidão</div>
                    </div>
                </div>

                <div class="progress-bar-wrap" style="height:8px;margin-bottom:10px;">
                    <div class="progress-bar-fill {{ $fill }}" style="width:{{ $cl->pct }}%;"></div>
                </div>

                <div style="display:flex;align-items:center;justify-content:space-between;font-size:12px;color:var(--cinza-light);">
                    <span>{{ $cl->items_count }} itens no checklist</span>
                    @if($cl->missing > 0)
                        <span style="color:#e53935;font-weight:500;">{{ $cl->missing }} faltando</span>
                    @else
                        <span style="color:var(--teal);font-weight:500;">Completo!</span>
                    @endif
                </div>

                @if($cl->legal_basis)
                    <div style="font-size:11px;color:var(--cinza-light);margin-top:8px;">Base legal: {{ $cl->legal_basis }}</div>
                @endif
            </div>
            <div class="card-footer" style="display:flex;justify-content:flex-end;">
                <a href="{{ route('checklists.show', $cl->id) }}" class="btn btn-ghost btn-sm">Ver detalhes</a>
            </div>
        </div>
    @empty
        <div class="card" style="grid-column:1/-1;">
            <div class="card-body" style="text-align:center;padding:48px;color:var(--cinza-light);">
                Nenhum checklist ativo. Execute <code>php artisan db:seed</code> para carregar.
            </div>
        </div>
    @endforelse
</div>

@endsection
