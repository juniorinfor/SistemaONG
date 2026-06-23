@extends('layouts.app')
@section('page-title', 'Editar Beneficiário')

@section('content')
<div style="max-width:780px;">

<div style="margin-bottom:16px;">
    <a href="{{ route('beneficiarios.show', $beneficiario) }}" style="font-size:12px;color:var(--cinza-light);text-decoration:none;">← {{ $beneficiario->nome }}</a>
</div>

<form method="POST" action="{{ route('beneficiarios.update', $beneficiario) }}">
@csrf @method('PUT')

<div class="card" style="margin-bottom:16px;">
    <div class="card-header"><span class="card-title">Dados pessoais</span></div>
    <div class="card-body">
        <div class="form-group">
            <label class="form-label">Nome completo <span style="color:#e53935;">*</span></label>
            <input type="text" name="nome" class="form-control" value="{{ old('nome', $beneficiario->nome) }}" required>
        </div>
        <div style="display:grid;grid-template-columns:1fr 1fr;gap:16px;">
            <div class="form-group">
                <label class="form-label">Data de nascimento</label>
                <input type="date" name="data_nascimento" id="data_nascimento" class="form-control"
                       value="{{ old('data_nascimento', $beneficiario->data_nascimento?->format('Y-m-d')) }}"
                       onchange="detectarMenor()">
            </div>
            <div class="form-group">
                <label class="form-label">CPF</label>
                <input type="text" name="cpf" id="cpf_field" class="form-control" placeholder="000.000.000-00"
                       value="{{ old('cpf', $beneficiario->cpf) }}" oninput="detectarMenor()" maxlength="14">
            </div>
        </div>
        <div style="display:grid;grid-template-columns:1fr 1fr;gap:16px;">
            <div class="form-group">
                <label class="form-label">RG</label>
                <input type="text" name="rg" class="form-control" value="{{ old('rg', $beneficiario->rg) }}">
            </div>
            <div class="form-group">
                <label class="form-label">Status</label>
                <select name="status" class="form-control">
                    <option value="ativo"   {{ old('status', $beneficiario->status) === 'ativo'   ? 'selected' : '' }}>Ativo</option>
                    <option value="inativo" {{ old('status', $beneficiario->status) === 'inativo' ? 'selected' : '' }}>Inativo</option>
                </select>
            </div>
        </div>
        <div style="display:grid;grid-template-columns:1fr 1fr;gap:16px;">
            <div class="form-group">
                <label class="form-label">Gênero</label>
                <select name="genero" class="form-control">
                    @foreach(['masculino'=>'Masculino','feminino'=>'Feminino','nao_binario'=>'Não-binário','prefiro_nao_informar'=>'Prefiro não informar'] as $v => $l)
                        <option value="{{ $v }}" {{ old('genero', $beneficiario->genero) === $v ? 'selected' : '' }}>{{ $l }}</option>
                    @endforeach
                </select>
            </div>
            <div class="form-group">
                <label class="form-label">Raça/Cor</label>
                <select name="raca_cor" class="form-control">
                    @foreach(['branca'=>'Branca','preta'=>'Preta','parda'=>'Parda','amarela'=>'Amarela','indigena'=>'Indígena','nao_informado'=>'Prefiro não informar'] as $v => $l)
                        <option value="{{ $v }}" {{ old('raca_cor', $beneficiario->raca_cor) === $v ? 'selected' : '' }}>{{ $l }}</option>
                    @endforeach
                </select>
            </div>
        </div>
    </div>
</div>

<div class="card" id="responsavel-section" style="margin-bottom:16px;">
    <div class="card-header"><span class="card-title">Responsável</span></div>
    <div class="card-body">
        <div style="display:grid;grid-template-columns:1fr 1fr;gap:16px;">
            <div class="form-group">
                <label class="form-label">Nome do responsável</label>
                <input type="text" name="nome_responsavel" class="form-control" value="{{ old('nome_responsavel', $beneficiario->nome_responsavel) }}">
            </div>
            <div class="form-group">
                <label class="form-label">CPF do responsável</label>
                <input type="text" name="cpf_responsavel" class="form-control" value="{{ old('cpf_responsavel', $beneficiario->cpf_responsavel) }}" maxlength="14">
            </div>
        </div>
        <div class="form-group">
            <label class="form-label">Parentesco / vínculo</label>
            <input type="text" name="parentesco" class="form-control" value="{{ old('parentesco', $beneficiario->parentesco) }}" placeholder="mãe, pai, avó, tutor legal...">
        </div>
    </div>
</div>

<div class="card" style="margin-bottom:16px;">
    <div class="card-header"><span class="card-title">Contato e endereço</span></div>
    <div class="card-body">
        <div style="display:grid;grid-template-columns:1fr 1fr;gap:16px;">
            <div class="form-group">
                <label class="form-label">Telefone</label>
                <input type="text" name="telefone" class="form-control" value="{{ old('telefone', $beneficiario->telefone) }}">
            </div>
            <div class="form-group">
                <label class="form-label">E-mail</label>
                <input type="email" name="email" class="form-control" value="{{ old('email', $beneficiario->email) }}">
            </div>
        </div>
        <div style="display:grid;grid-template-columns:120px 1fr 80px;gap:12px;">
            <div class="form-group">
                <label class="form-label">CEP</label>
                <input type="text" name="cep" class="form-control" value="{{ old('cep', $beneficiario->cep) }}" maxlength="9">
            </div>
            <div class="form-group">
                <label class="form-label">Logradouro</label>
                <input type="text" name="endereco" class="form-control" value="{{ old('endereco', $beneficiario->endereco) }}">
            </div>
            <div class="form-group">
                <label class="form-label">Número</label>
                <input type="text" name="numero" class="form-control" value="{{ old('numero', $beneficiario->numero) }}">
            </div>
        </div>
        <div style="display:grid;grid-template-columns:1fr 1fr;gap:16px;">
            <div class="form-group">
                <label class="form-label">Bairro</label>
                <input type="text" name="bairro" class="form-control" value="{{ old('bairro', $beneficiario->bairro) }}">
            </div>
            <div class="form-group">
                <label class="form-label">Cidade</label>
                <input type="text" name="cidade" class="form-control" value="{{ old('cidade', $beneficiario->cidade) }}">
            </div>
        </div>
    </div>
</div>

<div class="card" style="margin-bottom:16px;">
    <div class="card-header"><span class="card-title">Observações</span></div>
    <div class="card-body">
        <textarea name="observacoes" class="form-control" rows="3">{{ old('observacoes', $beneficiario->observacoes) }}</textarea>
    </div>
</div>

<div style="display:flex;gap:10px;">
    <button type="submit" class="btn btn-primary">Salvar alterações</button>
    <a href="{{ route('beneficiarios.show', $beneficiario) }}" class="btn btn-ghost">Cancelar</a>
</div>

</form>
</div>

<script>
function detectarMenor() {
    const dob = document.getElementById('data_nascimento').value;
    const cpf = document.getElementById('cpf_field').value.trim();
    let isMenor = false;
    if (dob) {
        const hoje = new Date(); const nasc = new Date(dob);
        let idade = hoje.getFullYear() - nasc.getFullYear();
        const m = hoje.getMonth() - nasc.getMonth();
        if (m < 0 || (m === 0 && hoje.getDate() < nasc.getDate())) idade--;
        isMenor = idade < 18;
    }
    document.getElementById('responsavel-section').style.display = (isMenor || !cpf) ? 'block' : 'none';
}
detectarMenor();
</script>
@endsection
