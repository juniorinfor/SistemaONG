<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'PromessaDocs') — Associação Promessa</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600&family=Roboto:wght@400;500;700&display=swap" rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        :root {
            --azul:       #6EC1E4;
            --azul-deep:  #3a9dc7;
            --azul-dark:  #1e6e93;
            --azul-light: #eaf6fc;
            --teal:       #00BAA3;
            --teal-deep:  #009688;
            --teal-light: #e0f7f5;
            --amarelo:    #FFAC00;
            --amarelo-light: #fff8e6;
            --cinza:      #5C5C5C;
            --cinza-light:#7A7A7A;
            --cinza-bg:   #f4f6f8;
            --cinza-borda:#e0e4e8;
            --branco:     #ffffff;
            --texto:      #444444;
            --sidebar-w:  256px;
        }

        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

        html, body {
            height: 100%;
            background: var(--cinza-bg);
            color: var(--texto);
            font-family: 'Poppins', sans-serif;
            font-size: 14px;
            line-height: 1.6;
            -webkit-font-smoothing: antialiased;
        }

        .layout { display: flex; min-height: 100vh; }

        /* SIDEBAR */
        .sidebar {
            width: var(--sidebar-w);
            min-height: 100vh;
            background: var(--branco);
            border-right: 1px solid var(--cinza-borda);
            display: flex; flex-direction: column;
            position: fixed; top: 0; left: 0;
            z-index: 100; overflow-y: auto;
        }

        .sidebar-brand {
            padding: 20px 20px 16px;
            border-bottom: 2px solid var(--azul-light);
            display: flex; align-items: center; gap: 12px;
        }

        .sidebar-brand-icon {
            width: 40px; height: 40px;
            background: var(--azul); border-radius: 10px;
            display: flex; align-items: center; justify-content: center; flex-shrink: 0;
        }

        .sidebar-brand-icon svg { width: 22px; height: 22px; color: #fff; }

        .sidebar-brand-name {
            font-family: 'Roboto', sans-serif;
            font-size: 15px; font-weight: 700;
            color: var(--azul-dark); line-height: 1.2;
        }

        .sidebar-brand-sub { font-size: 10.5px; color: var(--cinza-light); margin-top: 1px; }

        .sidebar-nav { flex: 1; padding: 12px 0; }

        .nav-section-label {
            font-size: 10px; font-weight: 600;
            letter-spacing: .1em; text-transform: uppercase;
            color: var(--cinza-light); padding: 14px 20px 5px;
        }

        .nav-item {
            display: flex; align-items: center; gap: 10px;
            padding: 9px 20px;
            color: var(--cinza);
            text-decoration: none;
            font-size: 13.5px; font-weight: 400;
            transition: background .15s, color .15s;
            border-left: 3px solid transparent;
            margin: 1px 0;
        }

        .nav-item svg { width: 17px; height: 17px; flex-shrink: 0; opacity: .7; }

        .nav-item:hover {
            background: var(--azul-light);
            color: var(--azul-dark);
            border-left-color: var(--azul);
        }

        .nav-item:hover svg { opacity: 1; }

        .nav-item.active {
            background: var(--teal-light);
            color: var(--teal-deep);
            border-left-color: var(--teal);
            font-weight: 500;
        }

        .nav-item.active svg { opacity: 1; color: var(--teal); }

        .nav-badge {
            margin-left: auto;
            background: var(--amarelo); color: #fff;
            font-size: 10px; font-weight: 600;
            padding: 1px 7px; border-radius: 20px;
        }

        .sidebar-footer {
            padding: 14px 20px;
            border-top: 1px solid var(--cinza-borda);
            font-size: 10px; color: var(--cinza-light);
        }

        /* MAIN */
        .main-wrapper {
            margin-left: var(--sidebar-w);
            flex: 1; display: flex; flex-direction: column; min-height: 100vh;
        }

        /* TOPBAR */
        .topbar {
            height: 58px;
            background: var(--branco);
            border-bottom: 1px solid var(--cinza-borda);
            display: flex; align-items: center;
            padding: 0 28px; gap: 14px;
            position: sticky; top: 0; z-index: 50;
        }

        .topbar-title {
            font-family: 'Roboto', sans-serif;
            font-size: 17px; font-weight: 700;
            color: var(--texto); flex: 1;
        }

        .topbar-title small {
            font-size: 12px; font-weight: 400;
            font-family: 'Poppins', sans-serif;
            color: var(--cinza-light); margin-left: 8px;
        }

        .topbar-divider { width: 1px; height: 20px; background: var(--cinza-borda); }

        .topbar-avatar {
            width: 32px; height: 32px;
            background: var(--azul); border-radius: 50%;
            display: flex; align-items: center; justify-content: center;
            font-size: 12px; font-weight: 600; color: #fff; flex-shrink: 0;
        }

        .topbar-user { display: flex; align-items: center; gap: 9px; font-size: 13px; }
        .topbar-user-name { font-weight: 500; color: var(--texto); }

        .btn-logout {
            display: flex; align-items: center; gap: 6px;
            font-size: 13px; color: var(--cinza-light);
            background: none; border: none; cursor: pointer;
            padding: 6px 10px; border-radius: 6px;
            transition: color .15s, background .15s;
            font-family: 'Poppins', sans-serif;
        }

        .btn-logout:hover { color: #c0392b; background: #fdf0ef; }
        .btn-logout svg { width: 15px; height: 15px; }

        /* CONTENT */
        .page-content { flex: 1; padding: 28px; }

        /* PAGE HEADER */
        .page-header { margin-bottom: 24px; }

        .page-header h1 {
            font-family: 'Roboto', sans-serif;
            font-size: 24px; font-weight: 700; color: var(--texto);
        }

        .page-header p { margin-top: 4px; font-size: 13px; color: var(--cinza-light); }

        .page-header-bar {
            display: flex; align-items: flex-end;
            justify-content: space-between; gap: 16px; flex-wrap: wrap;
        }

        /* BADGES */
        .badge {
            display: inline-flex; align-items: center; gap: 5px;
            font-size: 11.5px; font-weight: 500;
            padding: 3px 10px; border-radius: 20px;
        }

        .badge-dot { width: 6px; height: 6px; border-radius: 50%; flex-shrink: 0; }

        .badge-valido      { background: var(--teal-light); color: var(--teal-deep); }
        .badge-valido .badge-dot { background: var(--teal); }

        .badge-em-breve    { background: var(--amarelo-light); color: #8a5e00; }
        .badge-em-breve .badge-dot { background: var(--amarelo); }

        .badge-vencido     { background: #fdecea; color: #b71c1c; }
        .badge-vencido .badge-dot { background: #e53935; }

        .badge-faltante    { background: #f4f4f4; color: #888; }
        .badge-faltante .badge-dot { background: #bbb; }

        /* CARDS */
        .card {
            background: var(--branco);
            border: 1px solid var(--cinza-borda);
            border-radius: 10px; overflow: hidden;
        }

        .card-header {
            padding: 16px 22px;
            border-bottom: 1px solid var(--cinza-borda);
            display: flex; align-items: center; justify-content: space-between; gap: 12px;
        }

        .card-title {
            font-family: 'Roboto', sans-serif;
            font-size: 15px; font-weight: 700; color: var(--texto);
        }

        .card-body { padding: 22px; }

        .card-footer {
            padding: 12px 22px;
            background: var(--cinza-bg);
            border-top: 1px solid var(--cinza-borda);
        }

        /* BUTTONS */
        .btn {
            display: inline-flex; align-items: center; gap: 7px;
            font-size: 13px; font-weight: 500;
            padding: 8px 16px; border-radius: 7px;
            cursor: pointer; text-decoration: none;
            border: 1px solid transparent;
            transition: all .15s;
            font-family: 'Poppins', sans-serif; line-height: 1;
        }

        .btn svg { width: 15px; height: 15px; }

        .btn-primary { background: var(--teal); color: #fff; border-color: var(--teal); }
        .btn-primary:hover { background: var(--teal-deep); border-color: var(--teal-deep); }

        .btn-blue { background: var(--azul); color: #fff; border-color: var(--azul); }
        .btn-blue:hover { background: var(--azul-deep); border-color: var(--azul-deep); }

        .btn-yellow { background: var(--amarelo); color: #fff; border-color: var(--amarelo); font-weight: 600; }
        .btn-yellow:hover { background: #e09800; border-color: #e09800; }

        .btn-outline { background: transparent; color: var(--teal); border-color: var(--teal); }
        .btn-outline:hover { background: var(--teal-light); }

        .btn-ghost { background: transparent; color: var(--cinza-light); border-color: var(--cinza-borda); }
        .btn-ghost:hover { background: var(--cinza-bg); color: var(--texto); }

        .btn-sm { padding: 5px 12px; font-size: 12px; }
        .btn-lg { padding: 11px 22px; font-size: 14.5px; }

        /* TABLE */
        .table-wrapper { overflow-x: auto; }

        table.data-table { width: 100%; border-collapse: collapse; font-size: 13.5px; }

        table.data-table thead th {
            padding: 10px 16px; text-align: left;
            font-size: 11px; font-weight: 600;
            letter-spacing: .06em; text-transform: uppercase;
            color: var(--cinza-light);
            background: var(--cinza-bg);
            border-bottom: 1px solid var(--cinza-borda);
        }

        table.data-table tbody tr {
            border-bottom: 1px solid #f0f0f0; transition: background .1s;
        }

        table.data-table tbody tr:last-child { border-bottom: none; }
        table.data-table tbody tr:hover { background: var(--azul-light); }

        table.data-table tbody td {
            padding: 12px 16px; color: var(--texto); vertical-align: middle;
        }

        /* PROGRESS */
        .progress-bar-wrap { height: 6px; background: var(--cinza-borda); border-radius: 4px; overflow: hidden; }

        .progress-bar-fill { height: 100%; border-radius: 4px; background: var(--teal); transition: width .4s ease; }
        .progress-bar-fill.warn   { background: var(--amarelo); }
        .progress-bar-fill.danger { background: #e53935; }

        /* ALERTS */
        .alert {
            padding: 12px 16px; border-radius: 8px; font-size: 13.5px;
            display: flex; align-items: flex-start; gap: 10px; border-left: 3px solid;
        }

        .alert svg { width: 16px; height: 16px; flex-shrink: 0; margin-top: 1px; }

        .alert-success { background: var(--teal-light); border-color: var(--teal); color: var(--teal-deep); }
        .alert-warning { background: var(--amarelo-light); border-color: var(--amarelo); color: #8a5e00; }
        .alert-danger  { background: #fdecea; border-color: #e53935; color: #7f0000; }
        .alert-info    { background: var(--azul-light); border-color: var(--azul); color: var(--azul-dark); }

        /* FORMS */
        .form-group { margin-bottom: 18px; }

        .form-label {
            display: block; font-size: 11.5px; font-weight: 600;
            letter-spacing: .05em; text-transform: uppercase;
            color: var(--cinza); margin-bottom: 6px;
        }

        .form-control {
            width: 100%; padding: 9px 12px;
            font-size: 14px; font-family: 'Poppins', sans-serif;
            color: var(--texto); background: #fff;
            border: 1px solid var(--cinza-borda);
            border-radius: 7px; outline: none;
            transition: border-color .15s, box-shadow .15s;
        }

        .form-control:focus {
            border-color: var(--azul);
            box-shadow: 0 0 0 3px rgba(110,193,228,.15);
        }

        .form-hint { font-size: 11.5px; color: var(--cinza-light); margin-top: 5px; }
        .form-control.is-invalid { border-color: #e53935; }
        .field-error { font-size: 12px; color: #c62828; margin-top: 5px; }

        /* UTILS */
        .text-muted   { color: var(--cinza-light); }
        .text-teal    { color: var(--teal); }
        .text-azul    { color: var(--azul-dark); }
        .text-amarelo { color: var(--amarelo); }
        .text-sm { font-size: 12.5px; } .text-xs { font-size: 11.5px; }
        .fw-600  { font-weight: 600; }
        .roboto  { font-family: 'Roboto', sans-serif; }
        .flex    { display: flex; } .items-center { align-items: center; }
        .gap-2 { gap: 8px; } .gap-3 { gap: 12px; } .gap-4 { gap: 16px; }
        .mt-1 { margin-top: 4px; } .mt-2 { margin-top: 8px; } .mt-4 { margin-top: 16px; }
        .mb-4 { margin-bottom: 16px; } .mb-6 { margin-bottom: 24px; }
        .grid-2 { display: grid; grid-template-columns: repeat(2,1fr); gap: 16px; }
        .grid-3 { display: grid; grid-template-columns: repeat(3,1fr); gap: 16px; }
        .grid-4 { display: grid; grid-template-columns: repeat(4,1fr); gap: 16px; }

        @media (max-width: 900px) { .grid-4,.grid-3 { grid-template-columns: repeat(2,1fr); } }
        @media (max-width: 600px) {
            .sidebar { transform: translateX(-100%); }
            .main-wrapper { margin-left: 0; }
            .grid-2,.grid-4,.grid-3 { grid-template-columns: 1fr; }
            .page-content { padding: 16px; }
        }
    </style>

    @stack('styles')
</head>
<body>
<div class="layout">

    <aside class="sidebar">
        <div class="sidebar-brand">
            <div class="sidebar-brand-icon">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/>
                    <polyline points="14 2 14 8 20 8"/>
                    <line x1="9" y1="13" x2="15" y2="13"/>
                    <line x1="9" y1="17" x2="13" y2="17"/>
                </svg>
            </div>
            <div>
                <div class="sidebar-brand-name">PromessaDocs</div>
                <div class="sidebar-brand-sub">Assoc. Promessa · Jaboatão/PE</div>
            </div>
        </div>

        <nav class="sidebar-nav">
            <div class="nav-section-label">Principal</div>

            <a href="{{ route('dashboard') }}" class="nav-item {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><rect x="3" y="3" width="7" height="7" rx="1.5"/><rect x="14" y="3" width="7" height="7" rx="1.5"/><rect x="3" y="14" width="7" height="7" rx="1.5"/><rect x="14" y="14" width="7" height="7" rx="1.5"/></svg>
                Dashboard
            </a>

            <a href="{{ route('documents.index') }}" class="nav-item {{ request()->routeIs('documents.*') ? 'active' : '' }}">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="9" y1="13" x2="15" y2="13"/><line x1="9" y1="17" x2="13" y2="17"/></svg>
                Documentos
                @php $expiring = \App\Models\Document::where('is_current',true)->expiringSoon(30)->count(); @endphp
                @if($expiring > 0)<span class="nav-badge">{{ $expiring }}</span>@endif
            </a>

            <a href="{{ route('people.index') }}" class="nav-item {{ request()->routeIs('people.*') ? 'active' : '' }}">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
                Pessoas
            </a>

            <a href="{{ route('checklists.index') }}" class="nav-item {{ request()->routeIs('checklists.*') ? 'active' : '' }}">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><polyline points="9 11 12 14 22 4"/><path d="M21 12v7a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11"/></svg>
                Checklists
            </a>

            <div class="nav-section-label">Oportunidades</div>

            <a href="{{ route('editais.index') }}" class="nav-item {{ request()->routeIs('editais.*') ? 'active' : '' }}">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/><line x1="11" y1="8" x2="11" y2="14"/><line x1="8" y1="11" x2="14" y2="11"/></svg>
                Radar de Editais
            </a>

            <a href="{{ route('projects.index') }}" class="nav-item {{ request()->routeIs('projects.*') ? 'active' : '' }}">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M22 19a2 2 0 0 1-2 2H4a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h5l2 3h9a2 2 0 0 1 2 2z"/></svg>
                Projetos
            </a>

            <div class="nav-section-label">Sistema</div>

            <a href="{{ route('document-types.index') }}" class="nav-item {{ request()->routeIs('document-types.*') ? 'active' : '' }}">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><line x1="8" y1="6" x2="21" y2="6"/><line x1="8" y1="12" x2="21" y2="12"/><line x1="8" y1="18" x2="21" y2="18"/><line x1="3" y1="6" x2="3.01" y2="6"/><line x1="3" y1="12" x2="3.01" y2="12"/><line x1="3" y1="18" x2="3.01" y2="18"/></svg>
                Catálogo de Docs
            </a>

            <a href="{{ route('settings.index') }}" class="nav-item {{ request()->routeIs('settings.*') ? 'active' : '' }}">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><circle cx="12" cy="12" r="3"/><path d="M19.07 4.93l-1.41 1.41M4.93 4.93l1.41 1.41M20 12h2M2 12h2M19.07 19.07l-1.41-1.41M4.93 19.07l1.41-1.41M12 20v2M12 2v2"/></svg>
                Configurações
            </a>

            <a href="{{ route('transparency.index') }}" target="_blank" class="nav-item">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M18 13v6a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h6"/><polyline points="15 3 21 3 21 9"/><line x1="10" y1="14" x2="21" y2="3"/></svg>
                Portal Público
            </a>
        </nav>

        <div class="sidebar-footer">PromessaDocs v1.0</div>
    </aside>

    <div class="main-wrapper">

        <header class="topbar">
            <div class="topbar-title">
                @yield('page-title', 'Dashboard')
                @hasSection('page-subtitle')<small>@yield('page-subtitle')</small>@endif
            </div>

            <div style="display:flex;align-items:center;gap:12px;">
                <a href="{{ route('documents.create') }}" class="btn btn-primary btn-sm">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M12 5v14M5 12h14"/></svg>
                    Novo Documento
                </a>

                <div class="topbar-divider"></div>

                <div class="topbar-user">
                    <div class="topbar-avatar">{{ strtoupper(substr(auth()->user()->name,0,1)) }}{{ strtoupper(substr(explode(' ',auth()->user()->name)[1]??'x',0,1)) }}</div>
                    <span class="topbar-user-name">{{ auth()->user()->name }}</span>
                </div>

                <div class="topbar-divider"></div>

                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="btn-logout">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/><polyline points="16 17 21 12 16 7"/><line x1="21" y1="12" x2="9" y2="12"/></svg>
                        Sair
                    </button>
                </form>
            </div>
        </header>

        <main class="page-content">
            @if(session('success'))
                <div class="alert alert-success mb-4">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="20 6 9 17 4 12"/></svg>
                    {{ session('success') }}
                </div>
            @endif
            @if(session('error'))
                <div class="alert alert-danger mb-4">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="15" y1="9" x2="9" y2="15"/><line x1="9" y1="9" x2="15" y2="15"/></svg>
                    {{ session('error') }}
                </div>
            @endif

            @yield('content')
        </main>

    </div>
</div>
@stack('scripts')
</body>
</html>
