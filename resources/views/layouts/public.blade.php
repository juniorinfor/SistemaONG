<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Portal de Transparência') — Associação Promessa</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600&family=Roboto:wght@400;700&display=swap" rel="stylesheet">

    <style>
        :root {
            --azul:       #6EC1E4;
            --azul-dark:  #1e6e93;
            --azul-light: #eaf6fc;
            --teal:       #00BAA3;
            --teal-deep:  #009688;
            --teal-light: #e0f7f5;
            --amarelo:    #FFAC00;
            --texto:      #444444;
            --cinza:      #5C5C5C;
            --cinza-light:#7A7A7A;
            --borda:      #e0e4e8;
            --bg:         #f4f6f8;
        }

        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

        body {
            font-family: 'Poppins', sans-serif;
            background: var(--bg);
            color: var(--texto);
            font-size: 14px;
            line-height: 1.6;
            -webkit-font-smoothing: antialiased;
        }

        /* TOPBAR */
        .pub-header {
            background: var(--azul);
            padding: 0 32px;
            display: flex; align-items: center; gap: 16px;
            height: 60px;
        }

        .pub-logo {
            width: 36px; height: 36px;
            background: rgba(255,255,255,.2); border-radius: 8px;
            display: flex; align-items: center; justify-content: center;
        }

        .pub-logo svg { width: 20px; height: 20px; color: #fff; }

        .pub-brand {
            font-family: 'Roboto', sans-serif;
            font-size: 16px; font-weight: 700; color: #fff;
        }

        .pub-brand span { color: var(--amarelo); }

        .pub-header-spacer { flex: 1; }

        .pub-header-link {
            font-size: 12.5px; color: rgba(255,255,255,.8);
            text-decoration: none; padding: 6px 12px;
            border-radius: 6px; border: 1px solid rgba(255,255,255,.25);
            transition: background .15s;
        }

        .pub-header-link:hover { background: rgba(255,255,255,.15); color: #fff; }

        /* HERO */
        .pub-hero {
            background: linear-gradient(135deg, var(--azul) 0%, var(--teal) 100%);
            padding: 48px 32px 40px;
            text-align: center;
        }

        .pub-hero h1 {
            font-family: 'Roboto', sans-serif;
            font-size: 28px; font-weight: 700;
            color: #fff; margin-bottom: 8px;
        }

        .pub-hero p { font-size: 14px; color: rgba(255,255,255,.8); max-width: 540px; margin: 0 auto; }

        /* CONTEÚDO */
        .pub-body { max-width: 900px; margin: 0 auto; padding: 32px 24px 64px; }

        /* CARDS */
        .card { background: #fff; border: 1px solid var(--borda); border-radius: 10px; overflow: hidden; }
        .card-header {
            padding: 16px 22px;
            border-bottom: 1px solid var(--borda);
            display: flex; align-items: center; justify-content: space-between; gap: 12px;
        }
        .card-title { font-family: 'Roboto', sans-serif; font-size: 15px; font-weight: 700; color: var(--texto); }
        .card-body { padding: 22px; }

        /* TABELA */
        .table-wrapper { overflow-x: auto; }
        table { width: 100%; border-collapse: collapse; font-size: 13.5px; }
        thead th {
            padding: 10px 16px; text-align: left;
            font-size: 11px; font-weight: 600;
            letter-spacing: .06em; text-transform: uppercase;
            color: var(--cinza-light); background: var(--bg);
            border-bottom: 1px solid var(--borda);
        }
        tbody tr { border-bottom: 1px solid #f0f0f0; transition: background .1s; }
        tbody tr:last-child { border-bottom: none; }
        tbody tr:hover { background: var(--azul-light); }
        tbody td { padding: 12px 16px; vertical-align: middle; }

        /* BADGE */
        .badge {
            display: inline-flex; align-items: center; gap: 5px;
            font-size: 11.5px; font-weight: 500;
            padding: 3px 10px; border-radius: 20px;
        }
        .badge-dot { width: 6px; height: 6px; border-radius: 50%; }
        .badge-valido     { background: var(--teal-light); color: var(--teal-deep); }
        .badge-valido .badge-dot { background: var(--teal); }
        .badge-vencido    { background: #fdecea; color: #b71c1c; }
        .badge-vencido .badge-dot { background: #e53935; }

        /* BTN */
        .btn {
            display: inline-flex; align-items: center; gap: 6px;
            font-size: 12.5px; font-weight: 500;
            padding: 6px 14px; border-radius: 7px;
            cursor: pointer; text-decoration: none;
            border: 1px solid var(--borda);
            background: #fff; color: var(--cinza);
            transition: all .15s; font-family: 'Poppins', sans-serif;
        }
        .btn:hover { background: var(--bg); color: var(--texto); }
        .btn-teal { background: var(--teal); color: #fff; border-color: var(--teal); }
        .btn-teal:hover { background: var(--teal-deep); border-color: var(--teal-deep); color: #fff; }

        /* FOOTER */
        .pub-footer {
            background: #fff; border-top: 1px solid var(--borda);
            text-align: center; padding: 20px;
            font-size: 11.5px; color: var(--cinza-light);
        }

        .cat-pill {
            display: inline-block;
            background: var(--azul-light); color: var(--azul-dark);
            font-size: 11px; font-weight: 500;
            padding: 2px 10px; border-radius: 20px;
        }

        @media (max-width: 600px) {
            .pub-hero { padding: 32px 20px; }
            .pub-hero h1 { font-size: 22px; }
            .pub-body { padding: 20px 16px 48px; }
            .pub-header { padding: 0 16px; }
        }
    </style>
</head>
<body>

<header class="pub-header">
    <div class="pub-logo">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/>
            <polyline points="14 2 14 8 20 8"/>
        </svg>
    </div>
    <div class="pub-brand">Promessa<span>Docs</span></div>
    <div class="pub-header-spacer"></div>
    <a href="https://www.promessa.ong.br" target="_blank" rel="noopener" class="pub-header-link">
        www.promessa.ong.br
    </a>
    @auth
        <a href="{{ route('dashboard') }}" class="pub-header-link" style="margin-left:6px;">Painel interno</a>
    @else
        <a href="{{ route('login') }}" class="pub-header-link" style="margin-left:6px;">Área restrita</a>
    @endauth
</header>

<section class="pub-hero">
    <h1>Portal de Transparência</h1>
    <p>Documentos institucionais da Associação Promessa disponíveis para consulta pública, conforme exigência de editais e Lei de Acesso à Informação.</p>
</section>

<main class="pub-body">
    @yield('content')
</main>

<footer class="pub-footer">
    Associação Promessa · CNPJ {{ $institution?->cnpj ?? '—' }} · Jaboatão dos Guararapes/PE<br>
    Portal de Transparência — dados atualizados automaticamente pelo PromessaDocs
</footer>

</body>
</html>
