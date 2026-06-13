<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Alerta de Vencimento</title>
    <style>
        body { margin:0; padding:0; background:#f4f6f8; font-family:'Segoe UI',Arial,sans-serif; font-size:14px; color:#444; }
        .wrap { max-width:560px; margin:32px auto; background:#fff; border-radius:10px; overflow:hidden; box-shadow:0 2px 8px rgba(0,0,0,.08); }
        .header { background:#6EC1E4; padding:28px 32px; }
        .header-title { font-size:20px; font-weight:700; color:#fff; margin:0; }
        .header-sub { font-size:13px; color:rgba(255,255,255,.8); margin-top:4px; }
        .body { padding:28px 32px; }
        .alert-box { border-left:4px solid #FFAC00; background:#fff8e6; border-radius:6px; padding:14px 18px; margin-bottom:22px; }
        .alert-days { font-size:28px; font-weight:700; color:#8a6000; line-height:1; }
        .alert-label { font-size:12px; color:#8a6000; margin-top:2px; }
        .field { margin-bottom:14px; }
        .field-label { font-size:10.5px; font-weight:600; text-transform:uppercase; letter-spacing:.06em; color:#7A7A7A; margin-bottom:3px; }
        .field-value { font-size:14px; color:#444; }
        .btn { display:inline-block; padding:11px 22px; background:#00BAA3; color:#fff; font-weight:600; font-size:14px; text-decoration:none; border-radius:7px; margin-top:6px; }
        .footer { background:#f4f6f8; padding:18px 32px; font-size:11.5px; color:#7A7A7A; border-top:1px solid #e0e4e8; }
    </style>
</head>
<body>
<div class="wrap">

    <div class="header">
        <p class="header-title">PromessaDocs — Alerta de Vencimento</p>
        <p class="header-sub">Associação Promessa · Jaboatão dos Guararapes/PE</p>
    </div>

    <div class="body">

        <div class="alert-box">
            @if($daysLeft === 0)
                <div class="alert-days" style="color:#b71c1c;">HOJE</div>
                <div class="alert-label" style="color:#b71c1c;">Este documento vence hoje!</div>
            @elseif($daysLeft === 1)
                <div class="alert-days" style="color:#c62828;">1 dia</div>
                <div class="alert-label" style="color:#c62828;">Este documento vence amanhã</div>
            @else
                <div class="alert-days">{{ $daysLeft }} dias</div>
                <div class="alert-label">Restam {{ $daysLeft }} dias para o vencimento</div>
            @endif
        </div>

        <div class="field">
            <div class="field-label">Documento</div>
            <div class="field-value" style="font-weight:600;">{{ $document->documentType->name }}</div>
        </div>

        @if($document->person)
        <div class="field">
            <div class="field-label">Pessoa</div>
            <div class="field-value">{{ $document->person->name }}</div>
        </div>
        @endif

        <div class="field">
            <div class="field-label">Vence em</div>
            <div class="field-value">{{ $document->expires_at->format('d/m/Y') }}</div>
        </div>

        @if($document->protocol_number)
        <div class="field">
            <div class="field-label">Protocolo</div>
            <div class="field-value">{{ $document->protocol_number }}</div>
        </div>
        @endif

        @if($document->documentType->official_url)
        <div class="field">
            <div class="field-label">Onde renovar</div>
            <div class="field-value">
                <a href="{{ $document->documentType->official_url }}" style="color:#1e6e93;">{{ $document->documentType->official_url }}</a>
            </div>
        </div>
        @endif

        <a href="{{ config('app.url') }}/documents/{{ $document->id }}" class="btn">
            Ver no PromessaDocs
        </a>

    </div>

    <div class="footer">
        Este e-mail foi gerado automaticamente pelo PromessaDocs.<br>
        Para deixar de receber alertas, acesse as configurações do sistema.
    </div>

</div>
</body>
</html>
