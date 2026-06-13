<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Entrar') — PromessaDocs</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600&family=Roboto:wght@400;700&display=swap" rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        :root {
            --azul: #6EC1E4; --azul-dark: #1e6e93; --azul-light: #eaf6fc;
            --teal: #00BAA3; --teal-light: #e0f7f5;
            --amarelo: #FFAC00;
            --texto: #444444; --cinza: #5C5C5C; --cinza-light: #7A7A7A;
            --borda: #e0e4e8;
        }

        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

        html, body {
            height: 100%;
            font-family: 'Poppins', sans-serif;
            background: var(--azul-light);
            -webkit-font-smoothing: antialiased;
        }

        .auth-wrapper { min-height: 100vh; display: flex; }

        /* Painel esquerdo */
        .auth-left {
            flex: 1;
            background: var(--azul);
            display: flex; align-items: center; justify-content: center;
            padding: 48px; position: relative; overflow: hidden;
        }

        /* Ondas decorativas */
        .auth-left::after {
            content: '';
            position: absolute;
            bottom: -60px; left: -60px;
            width: 320px; height: 320px;
            border-radius: 50%;
            background: rgba(255,255,255,.06);
        }

        .auth-left::before {
            content: '';
            position: absolute;
            top: -80px; right: -80px;
            width: 400px; height: 400px;
            border-radius: 50%;
            background: rgba(0,186,163,.12);
        }

        .auth-left-content { position: relative; z-index: 1; max-width: 380px; }

        .auth-logo {
            width: 56px; height: 56px;
            background: rgba(255,255,255,.2);
            border-radius: 14px;
            display: flex; align-items: center; justify-content: center;
            margin-bottom: 28px;
        }

        .auth-logo svg { width: 28px; height: 28px; color: #fff; }

        .auth-tagline {
            font-family: 'Roboto', sans-serif;
            font-size: 34px; font-weight: 700;
            color: #fff; line-height: 1.25; margin-bottom: 16px;
        }

        .auth-tagline span { color: var(--amarelo); }

        .auth-tagline-sub {
            font-size: 14px; color: rgba(255,255,255,.7); line-height: 1.7;
        }

        .auth-features { margin-top: 32px; display: flex; flex-direction: column; gap: 10px; }

        .auth-feature {
            display: flex; align-items: center; gap: 10px;
            font-size: 13px; color: rgba(255,255,255,.75);
        }

        .auth-feature-icon {
            width: 26px; height: 26px;
            background: rgba(255,255,255,.15);
            border-radius: 6px;
            display: flex; align-items: center; justify-content: center;
            flex-shrink: 0;
        }

        .auth-feature-icon svg { width: 14px; height: 14px; color: var(--amarelo); }

        /* Painel direito */
        .auth-right {
            width: 440px; flex-shrink: 0;
            background: #fff;
            display: flex; align-items: center; justify-content: center;
            padding: 48px 44px;
        }

        .auth-form-wrap { width: 100%; }

        .auth-form-title {
            font-family: 'Roboto', sans-serif;
            font-size: 26px; font-weight: 700;
            color: var(--texto); margin-bottom: 6px;
        }

        .auth-form-sub { font-size: 13px; color: var(--cinza-light); margin-bottom: 32px; }

        .form-group { margin-bottom: 18px; }

        .form-label {
            display: block; font-size: 11.5px; font-weight: 600;
            letter-spacing: .05em; text-transform: uppercase;
            color: var(--cinza); margin-bottom: 6px;
        }

        .form-control {
            width: 100%; padding: 10px 14px;
            font-size: 14px; font-family: 'Poppins', sans-serif;
            color: var(--texto); background: #fff;
            border: 1px solid var(--borda); border-radius: 8px;
            outline: none; transition: border-color .15s, box-shadow .15s;
        }

        .form-control:focus {
            border-color: var(--azul);
            box-shadow: 0 0 0 3px rgba(110,193,228,.15);
        }

        .form-control.is-invalid { border-color: #e53935; }
        .field-error { font-size: 12px; color: #c62828; margin-top: 5px; }

        .btn-auth {
            width: 100%; padding: 12px;
            font-size: 14px; font-weight: 600;
            font-family: 'Poppins', sans-serif;
            background: var(--teal); color: #fff;
            border: none; border-radius: 8px;
            cursor: pointer; transition: background .15s;
            margin-top: 8px;
        }

        .btn-auth:hover { background: #009688; }

        .auth-divider { height: 1px; background: var(--borda); margin: 28px 0; }
        .auth-footer { text-align: center; font-size: 11.5px; color: var(--cinza-light); }

        @media (max-width: 768px) {
            .auth-left { display: none; }
            .auth-right { width: 100%; padding: 32px 24px; }
        }
    </style>
</head>
<body>
<div class="auth-wrapper">
    <div class="auth-left">
        <div class="auth-left-content">
            <div class="auth-logo">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/>
                    <polyline points="14 2 14 8 20 8"/>
                    <line x1="9" y1="13" x2="15" y2="13"/>
                    <line x1="9" y1="17" x2="13" y2="17"/>
                </svg>
            </div>

            <div class="auth-tagline">
                Documentação em <span>dia</span>,<br>edital garantido.
            </div>

            <p class="auth-tagline-sub">
                Gestão completa de certidões, atas e titulações para a Associação Promessa estar sempre pronta para editais.
            </p>

            <div class="auth-features">
                <div class="auth-feature">
                    <div class="auth-feature-icon">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                    </div>
                    Alertas de vencimento automáticos
                </div>
                <div class="auth-feature">
                    <div class="auth-feature-icon">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="9 11 12 14 22 4"/><path d="M21 12v7a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11"/></svg>
                    </div>
                    Checklists MROSC, CMDCA, CMAS e CEBAS
                </div>
                <div class="auth-feature">
                    <div class="auth-feature-icon">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                    </div>
                    Portal de transparência público
                </div>
            </div>
        </div>
    </div>

    <div class="auth-right">
        <div class="auth-form-wrap">
            @yield('content')
            <div class="auth-divider"></div>
            <div class="auth-footer">PromessaDocs · Associação Promessa · Jaboatão dos Guararapes/PE</div>
        </div>
    </div>
</div>
</body>
</html>
