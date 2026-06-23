<?php

namespace App\Http\Controllers;

use App\Models\Acao;
use App\Models\Beneficiario;
use App\Models\Institution;
use App\Models\Project;
use App\Models\SessaoAcao;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AcaoController extends Controller
{
    private function institution(): Institution
    {
        return Institution::where('slug', 'promessa')->firstOrFail();
    }

    public function index(Request $request)
    {
        $institution = $this->institution();
        $query = Acao::where('institution_id', $institution->id)
            ->with('project')
            ->orderByDesc('updated_at');

        if ($request->filled('q')) {
            $query->where('titulo', 'like', '%' . $request->q . '%');
        }
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        if ($request->filled('tipo')) {
            $query->where('tipo', $request->tipo);
        }
        if ($request->filled('project_id')) {
            $query->where('project_id', $request->project_id);
        }

        $acoes    = $query->paginate(15)->withQueryString();
        $projects = Project::where('institution_id', $institution->id)->orderBy('title')->get(['id', 'title']);

        return view('acoes.index', compact('acoes', 'projects'));
    }

    public function create()
    {
        $institution = $this->institution();
        $projects = Project::where('institution_id', $institution->id)->orderBy('title')->get(['id', 'title']);
        return view('acoes.create', compact('projects'));
    }

    public function store(Request $request)
    {
        $institution = $this->institution();

        $validated = $request->validate([
            'titulo'               => 'required|string|max:300',
            'descricao'            => 'nullable|string',
            'tipo'                 => 'required|in:oficina,palestra,atendimento_individual,grupo,capacitacao,evento,visita_domiciliar,reuniao,outro',
            'local'                => 'nullable|string|max:200',
            'responsavel_nome'     => 'nullable|string|max:200',
            'responsavel_cargo'    => 'nullable|string|max:100',
            'carga_horaria_sessao' => 'nullable|numeric|min:0.5|max:24',
            'status'               => 'required|in:planejada,em_andamento,concluida,cancelada',
            'project_id'           => 'nullable|exists:projects,id',
            'objetivos'            => 'nullable|string',
            'metodologia'          => 'nullable|string',
            'observacoes'          => 'nullable|string',
        ]);

        $acao = Acao::create(array_merge($validated, ['institution_id' => $institution->id]));

        return redirect()->route('acoes.show', $acao)
            ->with('success', 'Ação cadastrada com sucesso.');
    }

    public function show(Acao $acao)
    {
        $acao->load(['project', 'sessoes.beneficiarios']);
        return view('acoes.show', compact('acao'));
    }

    public function edit(Acao $acao)
    {
        $institution = $this->institution();
        $projects = Project::where('institution_id', $institution->id)->orderBy('title')->get(['id', 'title']);
        return view('acoes.edit', compact('acao', 'projects'));
    }

    public function update(Request $request, Acao $acao)
    {
        $validated = $request->validate([
            'titulo'               => 'required|string|max:300',
            'descricao'            => 'nullable|string',
            'tipo'                 => 'required|in:oficina,palestra,atendimento_individual,grupo,capacitacao,evento,visita_domiciliar,reuniao,outro',
            'local'                => 'nullable|string|max:200',
            'responsavel_nome'     => 'nullable|string|max:200',
            'responsavel_cargo'    => 'nullable|string|max:100',
            'carga_horaria_sessao' => 'nullable|numeric|min:0.5|max:24',
            'status'               => 'required|in:planejada,em_andamento,concluida,cancelada',
            'project_id'           => 'nullable|exists:projects,id',
            'objetivos'            => 'nullable|string',
            'metodologia'          => 'nullable|string',
            'observacoes'          => 'nullable|string',
        ]);

        $acao->update($validated);

        return redirect()->route('acoes.show', $acao)
            ->with('success', 'Ação atualizada.');
    }

    public function destroy(Acao $acao)
    {
        $acao->delete();
        return redirect()->route('acoes.index')
            ->with('success', 'Ação removida.');
    }

    // ── Sessões ──────────────────────────────────────────────────────

    public function storeSessao(Request $request, Acao $acao)
    {
        $validated = $request->validate([
            'data_execucao'      => 'required|date',
            'hora_inicio'        => 'nullable|date_format:H:i',
            'hora_fim'           => 'nullable|date_format:H:i|after:hora_inicio',
            'local_override'     => 'nullable|string|max:200',
            'facilitador_override' => 'nullable|string|max:200',
            'observacoes'        => 'nullable|string',
        ]);

        $sessao = $acao->sessoes()->create($validated);

        // Se a ação estava planejada, avança para em_andamento automaticamente
        if ($acao->status === 'planejada') {
            $acao->update(['status' => 'em_andamento']);
        }

        return redirect()->route('acoes.sessao.show', [$acao, $sessao])
            ->with('success', 'Sessão registrada. Registre agora a lista de presença.');
    }

    public function showSessao(Acao $acao, SessaoAcao $sessao)
    {
        $institution = $this->institution();
        $sessao->load('beneficiarios');
        $todos = Beneficiario::where('institution_id', $institution->id)
            ->where('status', 'ativo')
            ->orderBy('nome')
            ->get();

        $presentesIds = $sessao->beneficiarios->pluck('id')->toArray();

        return view('acoes.sessao', compact('acao', 'sessao', 'todos', 'presentesIds'));
    }

    public function storePresenca(Request $request, Acao $acao, SessaoAcao $sessao)
    {
        $request->validate([
            'presentes'   => 'nullable|array',
            'presentes.*' => 'exists:beneficiarios,id',
        ]);

        $presentes = $request->input('presentes', []);

        // Todos os beneficiários da instituição que estão na lista ou já estavam
        $institution = $this->institution();
        $todos = Beneficiario::where('institution_id', $institution->id)
            ->where('status', 'ativo')
            ->pluck('id')
            ->toArray();

        $sync = [];
        foreach ($todos as $id) {
            $sync[$id] = ['presente' => in_array($id, $presentes)];
        }

        $sessao->beneficiarios()->sync($sync);

        return redirect()->route('acoes.show', $acao)
            ->with('success', 'Presença registrada: ' . count($presentes) . ' presentes.');
    }

    // ── Relatório de comprovação ──────────────────────────────────────

    public function relatorio(Acao $acao)
    {
        $acao->load(['project', 'sessoes.beneficiarios']);
        $institution = $this->institution();

        // Agrega beneficiários únicos com frequência
        $beneficiariosMap = [];
        foreach ($acao->sessoes as $sessao) {
            foreach ($sessao->beneficiarios as $b) {
                if (!$b->pivot->presente) continue;
                if (!isset($beneficiariosMap[$b->id])) {
                    $beneficiariosMap[$b->id] = ['beneficiario' => $b, 'presencas' => 0];
                }
                $beneficiariosMap[$b->id]['presencas']++;
            }
        }
        usort($beneficiariosMap, fn($a, $b) => strcmp($a['beneficiario']->nome, $b['beneficiario']->nome));

        // Dados demográficos para relatório de impacto
        $generos     = collect($beneficiariosMap)->groupBy(fn($r) => $r['beneficiario']->genero)->map->count();
        $racas       = collect($beneficiariosMap)->groupBy(fn($r) => $r['beneficiario']->raca_cor)->map->count();
        $menores     = collect($beneficiariosMap)->filter(fn($r) => $r['beneficiario']->is_menor)->count();

        return view('acoes.relatorio', compact('acao', 'institution', 'beneficiariosMap', 'generos', 'racas', 'menores'));
    }
}
