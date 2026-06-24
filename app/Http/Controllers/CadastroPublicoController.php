<?php

namespace App\Http\Controllers;

use App\Models\Beneficiario;
use App\Models\Institution;
use Illuminate\Http\Request;

class CadastroPublicoController extends Controller
{
    private function institution(): Institution
    {
        return Institution::where('slug', 'promessa')->firstOrFail();
    }

    public function show()
    {
        $institution = $this->institution();
        return view('cadastro.beneficiario', compact('institution'));
    }

    public function store(Request $request)
    {
        $institution = $this->institution();

        $validated = $request->validate([
            'nome'             => 'required|string|max:200',
            'data_nascimento'  => 'nullable|date|before:today',
            'cpf'              => 'nullable|string|max:14',
            'genero'           => 'required|in:masculino,feminino,nao_binario,prefiro_nao_informar',
            'raca_cor'         => 'required|in:branca,preta,parda,amarela,indigena,nao_informado',
            'telefone'         => 'nullable|string|max:20',
            'email'            => 'nullable|email|max:200',
            'nome_responsavel' => 'nullable|string|max:200',
            'cpf_responsavel'  => 'nullable|string|max:14',
            'parentesco'       => 'nullable|string|max:50',
            'cep'              => 'nullable|string|max:10',
            'endereco'         => 'nullable|string|max:300',
            'numero'           => 'nullable|string|max:10',
            'bairro'           => 'nullable|string|max:100',
            'cidade'           => 'nullable|string|max:100',
        ], [
            'nome.required'   => 'O nome completo é obrigatório.',
            'genero.required' => 'Selecione o gênero.',
            'raca_cor.required' => 'Selecione a raça/cor.',
            'email.email'     => 'Informe um e-mail válido.',
        ]);

        Beneficiario::create(array_merge($validated, [
            'institution_id' => $institution->id,
            'status'         => 'ativo',
        ]));

        return redirect()->route('cadastro.show')->with('cadastrado', true);
    }
}
