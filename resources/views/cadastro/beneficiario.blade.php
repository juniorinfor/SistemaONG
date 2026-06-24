<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cadastro de Beneficiário — {{ $institution->name }}</title>
    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

        body {
            font-family: 'Segoe UI', Arial, sans-serif;
            background: #f0f9f7;
            min-height: 100vh;
            color: #263238;
        }

        /* Header */
        .pub-header {
            background: linear-gradient(135deg, #00897b 0%, #00695c 100%);
            color: #fff;
            padding: 28px 20px 24px;
            text-align: center;
        }
        .pub-header .org-name {
            font-size: 13px;
            letter-spacing: .08em;
            text-transform: uppercase;
            opacity: .85;
            margin-bottom: 6px;
        }
        .pub-header h1 {
            font-size: 22px;
            font-weight: 700;
            line-height: 1.3;
        }
        .pub-header p {
            font-size: 13.5px;
            opacity: .85;
            margin-top: 6px;
        }

        /* Container */
        .container {
            max-width: 600px;
            margin: 0 auto;
            padding: 20px 16px 48px;
        }

        /* Sucesso */
        .success-card {
            background: #fff;
            border-radius: 16px;
            padding: 40px 28px;
            text-align: center;
            margin-top: 12px;
            box-shadow: 0 2px 12px rgba(0,0,0,.07);
        }
        .success-icon {
            width: 72px; height: 72px;
            background: #e8f5e9;
            border-radius: 50%;
            display: flex; align-items: center; justify-content: center;
            margin: 0 auto 20px;
            font-size: 34px;
        }
        .success-card h2 { font-size: 20px; font-weight: 700; color: #2e7d32; margin-bottom: 10px; }
        .success-card p  { font-size: 14px; color: #546e7a; line-height: 1.7; }
        .btn-novo {
            display: inline-block;
            margin-top: 24px;
            background: #00897b;
            color: #fff;
            border: none;
            border-radius: 8px;
            padding: 12px 28px;
            font-size: 14px;
            font-weight: 600;
            text-decoration: none;
            cursor: pointer;
        }

        /* Seções do formulário */
        .section {
            background: #fff;
            border-radius: 12px;
            padding: 22px 20px;
            margin-bottom: 14px;
            box-shadow: 0 1px 6px rgba(0,0,0,.06);
        }
        .section-title {
            font-size: 11px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: .08em;
            color: #00897b;
            margin-bottom: 16px;
            padding-bottom: 8px;
            border-bottom: 2px solid #e0f7f4;
        }

        /* Campos */
        .form-row {
            display: grid;
            gap: 12px;
            margin-bottom: 12px;
        }
        .form-row.two { grid-template-columns: 1fr 1fr; }
        .form-row.three { grid-template-columns: 2fr 1fr 1fr; }

        .form-group { display: flex; flex-direction: column; gap: 5px; }
        .form-label {
            font-size: 12px;
            font-weight: 600;
            color: #546e7a;
            text-transform: uppercase;
            letter-spacing: .04em;
        }
        .form-label .req { color: #e53935; }
        .form-control {
            border: 1.5px solid #cfd8dc;
            border-radius: 8px;
            padding: 11px 13px;
            font-size: 15px;
            color: #263238;
            background: #fff;
            outline: none;
            transition: border-color .15s;
            width: 100%;
        }
        .form-control:focus { border-color: #00897b; box-shadow: 0 0 0 3px rgba(0,137,123,.12); }
        .form-control.is-invalid { border-color: #e53935; }
        .invalid-feedback { font-size: 12px; color: #e53935; margin-top: 3px; }

        select.form-control { appearance: none; background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='8' viewBox='0 0 12 8'%3E%3Cpath d='M1 1l5 5 5-5' stroke='%23546e7a' stroke-width='1.5' fill='none' stroke-linecap='round'/%3E%3C/svg%3E"); background-repeat: no-repeat; background-position: right 13px center; padding-right: 36px; }

        /* Bloco de responsável */
        #bloco-responsavel {
            background: #fff8e1;
            border: 1.5px solid #ffe082;
            border-radius: 10px;
            padding: 16px;
            margin-top: 4px;
        }
        #bloco-responsavel .resp-title {
            font-size: 12px;
            font-weight: 700;
            color: #e65100;
            margin-bottom: 12px;
            display: flex;
            align-items: center;
            gap: 6px;
        }

        /* Botão submit */
        .btn-submit {
            width: 100%;
            background: #00897b;
            color: #fff;
            border: none;
            border-radius: 10px;
            padding: 15px;
            font-size: 16px;
            font-weight: 700;
            cursor: pointer;
            margin-top: 4px;
            transition: background .15s;
        }
        .btn-submit:hover { background: #00695c; }
        .btn-submit:disabled { background: #b2dfdb; cursor: not-allowed; }

        .hint { font-size: 12px; color: #90a4ae; margin-top: 4px; }
        .legal { font-size: 12px; color: #90a4ae; text-align: center; margin-top: 16px; line-height: 1.6; }

        /* Responsive */
        @media (max-width: 480px) {
            .form-row.two, .form-row.three { grid-template-columns: 1fr; }
            .pub-header h1 { font-size: 19px; }
        }
    </style>
</head>
<body>

<div class="pub-header">
    <div class="org-name">{{ $institution->name }}</div>
    <h1>Cadastro de Beneficiário</h1>
    <p>Preencha os dados abaixo para se cadastrar nos nossos programas e ações sociais.</p>
</div>

<div class="container">

    {{-- Sucesso --}}
    @if(session('cadastrado'))
    <div class="success-card">
        <div class="success-icon">✓</div>
        <h2>Cadastro realizado!</h2>
        <p>
            Seus dados foram enviados com sucesso.<br>
            Em breve entraremos em contato para confirmar seu cadastro.
        </p>
        <a href="{{ route('cadastro.show') }}" class="btn-novo">Cadastrar outra pessoa</a>
    </div>
    @else

    <form method="POST" action="{{ route('cadastro.store') }}" id="form-cadastro">
    @csrf

    {{-- Dados pessoais --}}
    <div class="section">
        <div class="section-title">Dados pessoais</div>

        <div class="form-row" style="margin-bottom:12px;">
            <div class="form-group">
                <label class="form-label">Nome completo <span class="req">*</span></label>
                <input type="text" name="nome" class="form-control @error('nome') is-invalid @enderror"
                       value="{{ old('nome') }}" placeholder="Como consta no documento" autocomplete="name" required>
                @error('nome')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
        </div>

        <div class="form-row two">
            <div class="form-group">
                <label class="form-label">Data de nascimento</label>
                <input type="date" name="data_nascimento" id="data_nascimento"
                       class="form-control @error('data_nascimento') is-invalid @enderror"
                       value="{{ old('data_nascimento') }}" max="{{ date('Y-m-d') }}">
                @error('data_nascimento')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="form-group">
                <label class="form-label">CPF</label>
                <input type="text" name="cpf" id="cpf"
                       class="form-control @error('cpf') is-invalid @enderror"
                       value="{{ old('cpf') }}" placeholder="000.000.000-00" maxlength="14">
                <span class="hint">Deixe em branco se não tiver</span>
            </div>
        </div>

        <div class="form-row two">
            <div class="form-group">
                <label class="form-label">Gênero <span class="req">*</span></label>
                <select name="genero" class="form-control @error('genero') is-invalid @enderror" required>
                    <option value="">Selecione...</option>
                    <option value="masculino"            {{ old('genero') == 'masculino'            ? 'selected' : '' }}>Masculino</option>
                    <option value="feminino"             {{ old('genero') == 'feminino'             ? 'selected' : '' }}>Feminino</option>
                    <option value="nao_binario"          {{ old('genero') == 'nao_binario'          ? 'selected' : '' }}>Não-binário</option>
                    <option value="prefiro_nao_informar" {{ old('genero') == 'prefiro_nao_informar' ? 'selected' : '' }}>Prefiro não informar</option>
                </select>
                @error('genero')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="form-group">
                <label class="form-label">Raça/cor <span class="req">*</span></label>
                <select name="raca_cor" class="form-control @error('raca_cor') is-invalid @enderror" required>
                    <option value="">Selecione...</option>
                    <option value="branca"        {{ old('raca_cor') == 'branca'        ? 'selected' : '' }}>Branca</option>
                    <option value="preta"         {{ old('raca_cor') == 'preta'         ? 'selected' : '' }}>Preta</option>
                    <option value="parda"         {{ old('raca_cor') == 'parda'         ? 'selected' : '' }}>Parda</option>
                    <option value="amarela"       {{ old('raca_cor') == 'amarela'       ? 'selected' : '' }}>Amarela</option>
                    <option value="indigena"      {{ old('raca_cor') == 'indigena'      ? 'selected' : '' }}>Indígena</option>
                    <option value="nao_informado" {{ old('raca_cor') == 'nao_informado' ? 'selected' : '' }}>Prefiro não informar</option>
                </select>
                <span class="hint">Autodeclaração — conforme IBGE</span>
                @error('raca_cor')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
        </div>

        {{-- Responsável (menores) --}}
        <div id="bloco-responsavel" style="display:none;">
            <div class="resp-title">⚠ Menor de 18 anos — dados do responsável</div>
            <div class="form-row" style="margin-bottom:12px;">
                <div class="form-group">
                    <label class="form-label">Nome do responsável <span class="req">*</span></label>
                    <input type="text" name="nome_responsavel" id="nome_responsavel"
                           class="form-control @error('nome_responsavel') is-invalid @enderror"
                           value="{{ old('nome_responsavel') }}" placeholder="Nome completo do responsável">
                    @error('nome_responsavel')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
            </div>
            <div class="form-row two">
                <div class="form-group">
                    <label class="form-label">CPF do responsável</label>
                    <input type="text" name="cpf_responsavel"
                           class="form-control @error('cpf_responsavel') is-invalid @enderror"
                           value="{{ old('cpf_responsavel') }}" placeholder="000.000.000-00" maxlength="14">
                </div>
                <div class="form-group">
                    <label class="form-label">Parentesco</label>
                    <select name="parentesco" class="form-control">
                        <option value="">Selecione...</option>
                        @foreach(['Mãe','Pai','Avó','Avô','Tia','Tio','Irmã','Irmão','Tutora','Tutor','Outro'] as $p)
                        <option value="{{ $p }}" {{ old('parentesco') == $p ? 'selected' : '' }}>{{ $p }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
        </div>
    </div>

    {{-- Contato --}}
    <div class="section">
        <div class="section-title">Contato</div>
        <div class="form-row two">
            <div class="form-group">
                <label class="form-label">Telefone / WhatsApp</label>
                <input type="tel" name="telefone"
                       class="form-control @error('telefone') is-invalid @enderror"
                       value="{{ old('telefone') }}" placeholder="(81) 99999-9999">
                @error('telefone')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="form-group">
                <label class="form-label">E-mail</label>
                <input type="email" name="email"
                       class="form-control @error('email') is-invalid @enderror"
                       value="{{ old('email') }}" placeholder="seu@email.com">
                @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
        </div>
    </div>

    {{-- Endereço --}}
    <div class="section">
        <div class="section-title">Endereço</div>

        <div class="form-row two" style="margin-bottom:12px;">
            <div class="form-group">
                <label class="form-label">CEP</label>
                <input type="text" name="cep" id="cep"
                       class="form-control @error('cep') is-invalid @enderror"
                       value="{{ old('cep') }}" placeholder="00000-000" maxlength="9">
            </div>
            <div style="display:flex;align-items:flex-end;">
                <span id="cep-loading" style="font-size:12px;color:#90a4ae;display:none;padding-bottom:13px;">Buscando...</span>
            </div>
        </div>

        <div class="form-row three" style="margin-bottom:12px;">
            <div class="form-group">
                <label class="form-label">Logradouro</label>
                <input type="text" name="endereco" id="endereco"
                       class="form-control @error('endereco') is-invalid @enderror"
                       value="{{ old('endereco') }}" placeholder="Rua, Av...">
            </div>
            <div class="form-group">
                <label class="form-label">Número</label>
                <input type="text" name="numero"
                       class="form-control @error('numero') is-invalid @enderror"
                       value="{{ old('numero') }}" placeholder="123">
            </div>
            <div class="form-group">
                <label class="form-label">Bairro</label>
                <input type="text" name="bairro" id="bairro"
                       class="form-control @error('bairro') is-invalid @enderror"
                       value="{{ old('bairro') }}" placeholder="Bairro">
            </div>
        </div>

        <div class="form-group">
            <label class="form-label">Cidade</label>
            <input type="text" name="cidade" id="cidade"
                   class="form-control @error('cidade') is-invalid @enderror"
                   value="{{ old('cidade', 'Jaboatão dos Guararapes') }}">
        </div>
    </div>

    {{-- Erros gerais --}}
    @if($errors->any())
    <div style="background:#fce4e4;border:1.5px solid #ef9a9a;border-radius:10px;padding:14px 16px;margin-bottom:14px;">
        <div style="font-size:13px;font-weight:600;color:#c62828;margin-bottom:6px;">Corrija os campos abaixo:</div>
        <ul style="font-size:13px;color:#c62828;padding-left:18px;">
            @foreach($errors->all() as $error)
            <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
    @endif

    <button type="submit" class="btn-submit" id="btn-submit">
        Enviar cadastro
    </button>

    <p class="legal">
        Seus dados são utilizados exclusivamente para fins de gestão social<br>
        e elaboração de relatórios de impacto pela {{ $institution->name }}.
    </p>

    </form>
    @endif

</div>

<script>
// Detectar menor de idade
function verificarMenor() {
    const dob = document.getElementById('data_nascimento').value;
    const cpf = document.getElementById('cpf').value.trim();
    const bloco = document.getElementById('bloco-responsavel');
    const nomeResp = document.getElementById('nome_responsavel');

    let ehMenor = false;
    if (dob) {
        const hoje = new Date();
        const nasc  = new Date(dob);
        let idade = hoje.getFullYear() - nasc.getFullYear();
        const m = hoje.getMonth() - nasc.getMonth();
        if (m < 0 || (m === 0 && hoje.getDate() < nasc.getDate())) idade--;
        ehMenor = idade < 18;
    } else if (!cpf) {
        ehMenor = true;
    }

    bloco.style.display = ehMenor ? 'block' : 'none';
    if (nomeResp) nomeResp.required = ehMenor;
}

document.getElementById('data_nascimento').addEventListener('change', verificarMenor);
document.getElementById('cpf').addEventListener('input', verificarMenor);

// Mostrar bloco se voltou com erros e era menor
@if(old('nome_responsavel') || old('data_nascimento'))
verificarMenor();
@endif

// Máscara simples de CPF
function mascaraCPF(input) {
    let v = input.value.replace(/\D/g, '').slice(0, 11);
    if (v.length > 9) v = v.replace(/(\d{3})(\d{3})(\d{3})(\d{0,2})/, '$1.$2.$3-$4');
    else if (v.length > 6) v = v.replace(/(\d{3})(\d{3})(\d{0,3})/, '$1.$2.$3');
    else if (v.length > 3) v = v.replace(/(\d{3})(\d{0,3})/, '$1.$2');
    input.value = v;
}
document.querySelectorAll('input[name="cpf"], input[name="cpf_responsavel"]').forEach(el => {
    el.addEventListener('input', () => mascaraCPF(el));
});

// Máscara de CEP e busca ViaCEP
const cepInput = document.getElementById('cep');
cepInput.addEventListener('input', () => {
    let v = cepInput.value.replace(/\D/g, '').slice(0, 8);
    if (v.length > 5) v = v.replace(/(\d{5})(\d{0,3})/, '$1-$2');
    cepInput.value = v;
    if (v.replace('-','').length === 8) buscarCEP(v.replace('-',''));
});

function buscarCEP(cep) {
    const loading = document.getElementById('cep-loading');
    loading.style.display = 'inline';
    fetch(`https://viacep.com.br/ws/${cep}/json/`)
        .then(r => r.json())
        .then(d => {
            loading.style.display = 'none';
            if (!d.erro) {
                document.getElementById('endereco').value = d.logradouro || '';
                document.getElementById('bairro').value   = d.bairro    || '';
                document.getElementById('cidade').value   = d.localidade || '';
            }
        })
        .catch(() => { loading.style.display = 'none'; });
}

// Evitar duplo envio
document.getElementById('form-cadastro')?.addEventListener('submit', function() {
    const btn = document.getElementById('btn-submit');
    btn.disabled = true;
    btn.textContent = 'Enviando...';
});
</script>

</body>
</html>
