@extends('layouts.auth')

@section('title', 'Entrar')

@section('content')
    <div class="auth-form-title">Bem-vindo de volta</div>
    <p class="auth-form-sub">Acesse o painel de gestão documental.</p>

    <form method="POST" action="{{ route('login') }}">
        @csrf

        <div class="form-group">
            <label class="form-label" for="email">E-mail</label>
            <input
                id="email"
                type="email"
                name="email"
                class="form-control {{ $errors->has('email') ? 'is-invalid' : '' }}"
                value="{{ old('email') }}"
                required autofocus autocomplete="username"
                placeholder="seu@email.com.br"
            >
            @error('email')
                <div class="field-error">{{ $message }}</div>
            @enderror
        </div>

        <div class="form-group">
            <label class="form-label" for="password">Senha</label>
            <input
                id="password"
                type="password"
                name="password"
                class="form-control {{ $errors->has('password') ? 'is-invalid' : '' }}"
                required autocomplete="current-password"
                placeholder="••••••••"
            >
            @error('password')
                <div class="field-error">{{ $message }}</div>
            @enderror
        </div>

        <button type="submit" class="btn-auth">Entrar</button>
    </form>
@endsection
