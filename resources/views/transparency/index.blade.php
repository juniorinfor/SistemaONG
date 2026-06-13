@extends('layouts.public')

@section('title', 'Portal de Transparência')

@section('content')

@php
    $catLabels = [
        'juridico'  => 'Jurídico / Institucional',
        'federal'   => 'Certidões Federais',
        'estadual'  => 'Certidões Estaduais (PE)',
        'municipal' => 'Certidões Municipais',
        'contabil'  => 'Contábil / Financeiro',
        'titulacao' => 'Titulações e Registros',
        'pessoal'   => 'Documentos Pessoais',
    ];
    $totalDocs = $documents->flatten()->count();
@endphp

@if($totalDocs === 0)
    <div style="text-align:center;padding:64px 24px;color:var(--cinza-light);">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"
             style="width:48px;height:48px;margin:0 auto 16px;display:block;color:var(--azul);">
            <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/>
            <polyline points="14 2 14 8 20 8"/>
        </svg>
        <p style="font-size:16px;color:var(--cinza);font-weight:500;">Nenhum documento público disponível no momento.</p>
        <p style="font-size:13px;margin-top:8px;">Os documentos serão listados aqui assim que forem marcados para exibição pública.</p>
    </div>
@else

    <div style="margin-bottom:20px;display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:10px;">
        <p style="font-size:13px;color:var(--cinza-light);">{{ $totalDocs }} documento(s) disponível(is) para download</p>
        <p style="font-size:11.5px;color:var(--cinza-light);">Atualizado em {{ now()->format('d/m/Y') }}</p>
    </div>

    @foreach($documents as $category => $docs)
        <div class="card" style="margin-bottom:20px;">
            <div class="card-header">
                <span class="card-title">{{ $catLabels[$category] ?? ucfirst($category) }}</span>
                <span style="font-size:12px;color:var(--cinza-light);">{{ $docs->count() }} documento(s)</span>
            </div>
            <div class="table-wrapper">
                <table>
                    <thead>
                        <tr>
                            <th>Documento</th>
                            <th>Situação</th>
                            <th>Validade</th>
                            <th style="width:110px;"></th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($docs as $doc)
                            @php
                                $isValid = is_null($doc->expires_at) || $doc->expires_at->isFuture();
                            @endphp
                            <tr>
                                <td>
                                    <div style="font-weight:500;color:var(--texto);">{{ $doc->documentType->name }}</div>
                                    @if($doc->protocol_number)
                                        <div style="font-size:11px;color:var(--cinza-light);margin-top:2px;">
                                            Protocolo: {{ $doc->protocol_number }}
                                        </div>
                                    @endif
                                    @if($doc->person)
                                        <div style="font-size:11px;color:var(--cinza-light);margin-top:2px;">
                                            {{ $doc->person->name }}
                                        </div>
                                    @endif
                                </td>
                                <td>
                                    @if($isValid)
                                        <span class="badge badge-valido">
                                            <span class="badge-dot"></span>Válido
                                        </span>
                                    @else
                                        <span class="badge badge-vencido">
                                            <span class="badge-dot"></span>Vencido
                                        </span>
                                    @endif
                                </td>
                                <td style="font-size:13px;color:var(--cinza-light);">
                                    {{ $doc->expires_at?->format('d/m/Y') ?? 'Sem vencimento' }}
                                </td>
                                <td>
                                    <a href="{{ route('transparency.download', $doc) }}"
                                       class="btn btn-teal"
                                       title="Baixar {{ $doc->documentType->name }}">
                                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="width:13px;height:13px;">
                                            <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/>
                                            <polyline points="7 10 12 15 17 10"/>
                                            <line x1="12" y1="15" x2="12" y2="3"/>
                                        </svg>
                                        Baixar
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    @endforeach

@endif

{{-- Info legal --}}
<div style="margin-top:32px;padding:16px 20px;background:#fff;border:1px solid var(--borda);border-radius:8px;font-size:12px;color:var(--cinza-light);line-height:1.7;">
    <strong style="color:var(--cinza);">Sobre este portal:</strong>
    A Associação Promessa disponibiliza seus documentos institucionais em cumprimento à Lei nº 12.527/2011 (Lei de Acesso à Informação)
    e às exigências de transparência de editais públicos (MROSC – Lei nº 13.019/2014).
    Os documentos disponibilizados aqui são de caráter público e podem ser baixados livremente.
    Para informações adicionais, entre em contato pelo site
    <a href="https://www.promessa.ong.br" target="_blank" rel="noopener" style="color:var(--azul-dark);">www.promessa.ong.br</a>.
</div>

@endsection
