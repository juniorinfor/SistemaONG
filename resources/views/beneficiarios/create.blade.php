@extends('layouts.app')
@section('page-title', 'Novo Beneficiário')

@section('content')
<div style="max-width:780px;">

<div style="margin-bottom:16px;">
    <a href="{{ route('beneficiarios.index') }}" style="font-size:12px;color:var(--cinza-light);text-decoration:none;">← Beneficiários</a>
</div>

<form method="POST" action="{{ route('beneficiarios.store') }}">
@csrf

<div class="card" style="margin-bottom:16px;">
    <div class="card-header"><span class="card-title">Dados pessoais</span></div>
    <div class="card-body">

        <div class="form-group">
            <label class="form-label">Nome completo <span style="color:#e53935;">*</span></label>
            <input type="text" name="nome" class="form-control @error('nome') is-invalid @enderror"
                   value="{{ old('nome') }}" required autofocus>
            @error('nome')<div class="field-error">{{ $message }}</div>@enderror
        </div>

        <div style="display:grid;grid-template-columns:1fr 1fr;gap:16px;">
            <div class="form-group">
                <label class="form-label">Data de nascimento</label>
                <input type="date" name="data_nascimento" id="data_nascimento" class="form-control"
                       value="{{ old('data_nascimento') }}" onchange="detectarMenor()">
                <p class="form-hint">Campos de responsável aparecem automaticamente para menores</p>
            </div>
            <div class="form-group">
                <label class="form-label">CPF</label>
                <input type="text" name="cpf" id="cpf_field" class="form-control" placeholder="000.000.000-00"
                       value="{{ old('cpf') }}" oninput="detectarMenor()" maxlength="14">
                <p class="form-hint">Deixe em branco para crianças que ainda não têm CPF</p>
            </div>
        </div>

        <div style="display:grid;grid-template-columns:1fr 1fr;gap:16px;">
            <div class="form-group">
                <label class="form-label">RG</label>
                <input type="text" name="rg" class="form-control" value="{{ old('rg') }}">
            </div>
            <div class="form-group">
                <label class="form-label">Status <span style="color:#e53935;">*</span></label>
                <select name="status" class="form-control" required>
                    <option value="ativo"   {{ old('status','ativo') === 'ativo'   ? 'selected' : '' }}>Ativo</option>
                    <option value="inativo" {{ old('status') === 'inativo' ? 'selected' : '' }}>Inativo</option>
                </select>
            </div>
        </div>

        <div style="display:grid;grid-template-columns:1fr 1fr;gap:16px;">
            <div class="form-group">
                <label class="form-label">Gênero <span style="color:#e53935;">*</span></label>
                <select name="genero" class="form-control" required>
                    <option value="masculino"           {{ old('genero') === 'masculino'           ? 'selected' : '' }}>Masculino</option>
                    <option value="feminino"            {{ old('genero') === 'feminino'            ? 'selected' : '' }}>Feminino</option>
                    <option value="nao_binario"         {{ old('genero') === 'nao_binario'         ? 'selected' : '' }}>Não-binário</option>
                    <option value="prefiro_nao_informar"{{ old('genero','prefiro_nao_informar') === 'prefiro_nao_informar' ? 'selected' : '' }}>Prefiro não informar</option>
                </select>
            </div>
            <div class="form-group">
                <label class="form-label">Raça/Cor <span style="color:#e53935;">*</span></label>
                <select name="raca_cor" class="form-control" required>
                    <option value="branca"       {{ old('raca_cor') === 'branca'        ? 'selected' : '' }}>Branca</option>
                    <option value="preta"        {{ old('raca_cor') === 'preta'         ? 'selected' : '' }}>Preta</option>
                    <option value="parda"        {{ old('raca_cor') === 'parda'         ? 'selected' : '' }}>Parda</option>
                    <option value="amarela"      {{ old('raca_cor') === 'amarela'       ? 'selected' : '' }}>Amarela</option>
                    <option value="indigena"     {{ old('raca_cor') === 'indigena'      ? 'selected' : '' }}>Indígena</option>
                    <option value="nao_informado"{{ old('raca_cor','nao_informado') === 'nao_informado' ? 'selected' : '' }}>Prefiro não informar</option>
                </select>
                <p class="form-hint">Autodeclaração — exigido em relatórios de impacto social</p>
            </div>
        </div>
    </div>
</div>

{{-- Responsável (aparece quando menor ou sem CPF) --}}
<div class="card" id="responsavel-section" style="margin-bottom:16px;{{ old('nome_responsavel') || old('data_nascimento') ? '' : 'display:none;' }}">
    <div class="card-header">
        <span class="card-title">Responsável</span>
        <span style="font-size:12px;color:var(--cinza-light);">Obrigatório para menores de 18 anos ou sem CPF</span>
    </div>
    <div class="card-body">
        <div style="display:grid;grid-template-columns:1fr 1fr;gap:16px;">
            <div class="form-group">
                <label class="form-label">Nome do responsável</label>
                <input type="text" name="nome_responsavel" class="form-control" value="{{ old('nome_responsavel') }}">
            </div>
            <div class="form-group">
                <label class="form-label">CPF do responsável</label>
                <input type="text" name="cpf_responsavel" class="form-control" placeholder="000.000.000-00" value="{{ old('cpf_responsavel') }}" maxlength="14">
            </div>
        </div>
        <div class="form-group">
            <label class="form-label">Parentesco / vínculo</label>
            <input type="text" name="parentesco" class="form-control" placeholder="Ex.: mãe, pai, avó, tutor legal..." value="{{ old('parentesco') }}">
        </div>
    </div>
</div>

<div class="card" style="margin-bottom:16px;">
    <div class="card-header"><span class="card-title">Contato e endereço</span></div>
    <div class="card-body">
        <div style="display:grid;grid-template-columns:1fr 1fr;gap:16px;">
            <div class="form-group">
                <label class="form-label">Telefone / WhatsApp</label>
                <input type="text" name="telefone" class="form-control" placeholder="(81) 99999-9999" value="{{ old('telefone') }}">
            </div>
            <div class="form-group">
                <label class="form-label">E-mail</label>
                <input type="email" name="email" class="form-control" value="{{ old('email') }}">
            </div>
        </div>
        <div style="display:grid;grid-template-columns:120px 1fr 80px;gap:12px;">
            <div class="form-group">
                <label class="form-label">CEP</label>
                <input type="text" name="cep" class="form-control" placeholder="00000-000" value="{{ old('cep') }}" maxlength="9">
            </div>
            <div class="form-group">
                <label class="form-label">Logradouro</label>
                <input type="text" name="endereco" class="form-control" placeholder="Rua, Avenida..." value="{{ old('endereco') }}">
            </div>
            <div class="form-group">
                <label class="form-label">Número</label>
                <input type="text" name="numero" class="form-control" value="{{ old('numero') }}">
            </div>
        </div>
        <div style="display:grid;grid-template-columns:1fr 1fr;gap:16px;">
            <div class="form-group">
                <label class="form-label">Bairro</label>
                <input type="text" name="bairro" class="form-control" value="{{ old('bairro') }}">
            </div>
            <div class="form-group">
                <label class="form-label">Cidade</label>
                <input type="text" name="cidade" class="form-control" value="{{ old('cidade', 'Jaboatão dos Guararapes') }}">
            </div>
        </div>
    </div>
</div>

<div class="card" style="margin-bottom:16px;">
    <div class="card-header"><span class="card-title">Observações</span></div>
    <div class="card-body">
        <textarea name="observacoes" class="form-control" rows="3"
                  placeholder="Informações relevantes: necessidades especiais, restrições, histórico...">{{ old('observacoes') }}</textarea>
    </div>
</div>

<div style="display:flex;gap:10px;">
    <button type="submit" class="btn btn-primary">Cadastrar beneficiário</button>
    <a href="{{ route('beneficiarios.index') }}" class="btn btn-ghost">Cancelar</a>
</div>

</form>
</div>

<script>
function detectarMenor() {
    const dob = document.getElementById('data_nascimento').value;
    const cpf = document.getElementById('cpf_field').value.trim();
    let isMenor = false;

    if (dob) {
        const hoje = new Date();
        const nasc = new Date(dob);
        let idade = hoje.getFullYear() - nasc.getFullYear();
        const m = hoje.getMonth() - nasc.getMonth();
        if (m < 0 || (m === 0 && hoje.getDate() < nasc.getDate())) idade--;
        isMenor = idade < 18;
    }

    const semCpf = !cpf;
    const section = document.getElementById('responsavel-section');
    section.style.display = (isMenor || semCpf) ? 'block' : 'none';
}

// Inicializa ao carregar (em caso de old() values)
detectarMenor();
</script>
@endsection
