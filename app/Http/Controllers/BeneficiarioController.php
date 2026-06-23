<?php

namespace App\Http\Controllers;

use App\Models\Beneficiario;
use App\Models\Institution;
use Illuminate\Http\Request;

class BeneficiarioController extends Controller
{
    private function institution(): Institution
    {
        return Institution::where('slug', 'promessa')->firstOrFail();
    }

    public function index(Request $request)
    {
        $institution = $this->institution();
        $query = Beneficiario::where('institution_id', $institution->id)
            ->orderBy('nome');

        if ($request->filled('q')) {
            $query->where('nome', 'like', '%' . $request->q . '%');
        }
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        if ($request->filled('faixa')) {
            match ($request->faixa) {
                'crianca'   => $query->whereRaw('TIMESTAMPDIFF(YEAR, data_nascimento, CURDATE()) < 12'),
                'adolescente' => $query->whereRaw('TIMESTAMPDIFF(YEAR, data_nascimento, CURDATE()) BETWEEN 12 AND 17'),
                'adulto'    => $query->whereRaw('TIMESTAMPDIFF(YEAR, data_nascimento, CURDATE()) >= 18'),
                'sem_dob'   => $query->whereNull('data_nascimento'),
                default     => null,
            };
        }

        $beneficiarios = $query->paginate(20)->withQueryString();
        $total = Beneficiario::where('institution_id', $institution->id)->count();

        return view('beneficiarios.index', compact('beneficiarios', 'total'));
    }

    public function create()
    {
        return view('beneficiarios.create');
    }

    public function store(Request $request)
    {
        $institution = $this->institution();

        $validated = $request->validate([
            'nome'             => 'required|string|max:200',
            'data_nascimento'  => 'nullable|date|before:today',
            'cpf'              => 'nullable|string|max:14',
            'rg'               => 'nullable|string|max:20',
            'genero'           => 'required|in:masculino,feminino,nao_binario,prefiro_nao_informar',
            'raca_cor'         => 'required|in:branca,preta,parda,amarela,indigena,nao_informado',
            'nome_responsavel' => 'nullable|string|max:200',
            'cpf_responsavel'  => 'nullable|string|max:14',
            'parentesco'       => 'nullable|string|max:50',
            'telefone'         => 'nullable|string|max:20',
            'email'            => 'nullable|email|max:200',
            'cep'              => 'nullable|string|max:10',
            'endereco'         => 'nullable|string|max:300',
            'numero'           => 'nullable|string|max:10',
            'bairro'           => 'nullable|string|max:100',
            'cidade'           => 'nullable|string|max:100',
            'status'           => 'required|in:ativo,inativo',
            'observacoes'      => 'nullable|string',
        ]);

        Beneficiario::create(array_merge($validated, ['institution_id' => $institution->id]));

        return redirect()->route('beneficiarios.index')
            ->with('success', 'Beneficiário cadastrado com sucesso.');
    }

    public function show(Beneficiario $beneficiario)
    {
        $beneficiario->load(['sessoes.acao']);
        return view('beneficiarios.show', compact('beneficiario'));
    }

    public function edit(Beneficiario $beneficiario)
    {
        return view('beneficiarios.edit', compact('beneficiario'));
    }

    public function update(Request $request, Beneficiario $beneficiario)
    {
        $validated = $request->validate([
            'nome'             => 'required|string|max:200',
            'data_nascimento'  => 'nullable|date|before:today',
            'cpf'              => 'nullable|string|max:14',
            'rg'               => 'nullable|string|max:20',
            'genero'           => 'required|in:masculino,feminino,nao_binario,prefiro_nao_informar',
            'raca_cor'         => 'required|in:branca,preta,parda,amarela,indigena,nao_informado',
            'nome_responsavel' => 'nullable|string|max:200',
            'cpf_responsavel'  => 'nullable|string|max:14',
            'parentesco'       => 'nullable|string|max:50',
            'telefone'         => 'nullable|string|max:20',
            'email'            => 'nullable|email|max:200',
            'cep'              => 'nullable|string|max:10',
            'endereco'         => 'nullable|string|max:300',
            'numero'           => 'nullable|string|max:10',
            'bairro'           => 'nullable|string|max:100',
            'cidade'           => 'nullable|string|max:100',
            'status'           => 'required|in:ativo,inativo',
            'observacoes'      => 'nullable|string',
        ]);

        $beneficiario->update($validated);

        return redirect()->route('beneficiarios.show', $beneficiario)
            ->with('success', 'Beneficiário atualizado.');
    }

    public function destroy(Beneficiario $beneficiario)
    {
        $beneficiario->delete();
        return redirect()->route('beneficiarios.index')
            ->with('success', 'Beneficiário removido.');
    }
}
