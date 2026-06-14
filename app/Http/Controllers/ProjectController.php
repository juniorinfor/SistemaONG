<?php

namespace App\Http\Controllers;

use App\Models\Edital;
use App\Models\Institution;
use App\Models\Project;
use Illuminate\Http\Request;

class ProjectController extends Controller
{
    private function institution(): Institution
    {
        return Institution::where('slug', 'promessa')->firstOrFail();
    }

    public function index(Request $request)
    {
        $institution = $this->institution();

        $query = Project::where('institution_id', $institution->id)
            ->with('edital')
            ->latest();

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        if ($request->filled('area')) {
            $query->where('area', $request->area);
        }
        if ($request->filled('q')) {
            $q = $request->q;
            $query->where('title', 'like', "%{$q}%");
        }

        $projects = $query->paginate(15)->withQueryString();

        $areas = Project::where('institution_id', $institution->id)
            ->whereNotNull('area')
            ->distinct()
            ->pluck('area');

        $counts = [
            'total'       => Project::where('institution_id', $institution->id)->count(),
            'aprovados'   => Project::where('institution_id', $institution->id)->whereIn('status', ['aprovado', 'em_execucao', 'concluido'])->count(),
            'execucao'    => Project::where('institution_id', $institution->id)->where('status', 'em_execucao')->count(),
            'concluidos'  => Project::where('institution_id', $institution->id)->where('status', 'concluido')->count(),
        ];

        return view('projects.index', compact('projects', 'areas', 'counts'));
    }

    public function create(Request $request)
    {
        $institution = $this->institution();

        $editais = Edital::where('institution_id', $institution->id)
            ->whereNull('prazo_inscricao')
            ->orWhere('prazo_inscricao', '>=', now())
            ->orderByDesc('created_at')
            ->limit(50)
            ->get();

        $selectedEdital = $request->filled('edital_id')
            ? Edital::find($request->edital_id)
            : null;

        return view('projects.create', compact('editais', 'selectedEdital'));
    }

    public function store(Request $request)
    {
        $institution = $this->institution();

        $data = $request->validate([
            'title'           => 'required|string|max:255',
            'edital_id'       => 'nullable|exists:editais,id',
            'description'     => 'nullable|string|max:5000',
            'area'            => 'nullable|string|max:100',
            'status'          => 'required|in:rascunho,em_elaboracao,submetido,aprovado,reprovado,em_execucao,concluido,cancelado',
            'valor_pleiteado' => 'nullable|numeric|min:0',
            'valor_aprovado'  => 'nullable|numeric|min:0',
            'submitted_at'    => 'nullable|date',
            'approved_at'     => 'nullable|date',
            'start_date'      => 'nullable|date',
            'end_date'        => 'nullable|date|after_or_equal:start_date',
            'notes'           => 'nullable|string|max:3000',
        ]);

        $data['institution_id'] = $institution->id;
        $data['edital_id']      = $data['edital_id'] ?: null;

        $project = Project::create($data);

        return redirect()->route('projects.show', $project)
            ->with('success', 'Projeto criado com sucesso.');
    }

    public function show(Project $project)
    {
        $project->load('edital');
        return view('projects.show', compact('project'));
    }

    public function edit(Project $project)
    {
        $institution = $this->institution();

        $editais = Edital::where('institution_id', $institution->id)
            ->orderByDesc('created_at')
            ->limit(100)
            ->get();

        return view('projects.edit', compact('project', 'editais'));
    }

    public function update(Request $request, Project $project)
    {
        $data = $request->validate([
            'title'           => 'required|string|max:255',
            'edital_id'       => 'nullable|exists:editais,id',
            'description'     => 'nullable|string|max:5000',
            'area'            => 'nullable|string|max:100',
            'status'          => 'required|in:rascunho,em_elaboracao,submetido,aprovado,reprovado,em_execucao,concluido,cancelado',
            'valor_pleiteado' => 'nullable|numeric|min:0',
            'valor_aprovado'  => 'nullable|numeric|min:0',
            'submitted_at'    => 'nullable|date',
            'approved_at'     => 'nullable|date',
            'start_date'      => 'nullable|date',
            'end_date'        => 'nullable|date|after_or_equal:start_date',
            'notes'           => 'nullable|string|max:3000',
        ]);

        $data['edital_id'] = $data['edital_id'] ?: null;

        $project->update($data);

        return redirect()->route('projects.show', $project)
            ->with('success', 'Projeto atualizado com sucesso.');
    }

    public function destroy(Project $project)
    {
        $project->delete();

        return redirect()->route('projects.index')
            ->with('success', 'Projeto removido.');
    }
}
