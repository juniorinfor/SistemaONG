<?php
namespace App\Http\Controllers;

use App\Models\Institution;
use App\Models\Person;
use Illuminate\Http\Request;

class PersonController extends Controller
{
    private function institution()
    {
        return Institution::where('slug', 'promessa')->firstOrFail();
    }

    public function index()
    {
        $institution = $this->institution();
        $people = Person::where('institution_id', $institution->id)
            ->orderBy('name')
            ->withCount('documents')
            ->get();

        return view('people.index', compact('people'));
    }

    public function create()
    {
        return view('people.create');
    }

    public function store(Request $request)
    {
        $institution = $this->institution();

        $data = $request->validate([
            'name'               => 'required|string|max:150',
            'cpf'                => 'nullable|string|max:20',
            'rg'                 => 'nullable|string|max:30',
            'role'               => 'nullable|string|max:100',
            'type'               => 'required|in:diretoria,voluntario,colaborador',
            'mandate_start'      => 'nullable|date',
            'mandate_end'        => 'nullable|date|after_or_equal:mandate_start',
            'works_with_children'=> 'boolean',
            'email'              => 'nullable|email|max:150',
            'phone'              => 'nullable|string|max:30',
        ]);

        Person::create(array_merge($data, [
            'institution_id'      => $institution->id,
            'works_with_children' => $request->boolean('works_with_children'),
            'is_active'           => true,
        ]));

        return redirect()->route('people.index')
            ->with('success', 'Pessoa cadastrada com sucesso.');
    }

    public function show(Person $person)
    {
        $person->load(['documents' => fn($q) => $q->where('is_current', true)->with('documentType')]);
        return view('people.show', compact('person'));
    }

    public function edit(Person $person)
    {
        return view('people.edit', compact('person'));
    }

    public function update(Request $request, Person $person)
    {
        $data = $request->validate([
            'name'               => 'required|string|max:150',
            'cpf'                => 'nullable|string|max:20',
            'rg'                 => 'nullable|string|max:30',
            'role'               => 'nullable|string|max:100',
            'type'               => 'required|in:diretoria,voluntario,colaborador',
            'mandate_start'      => 'nullable|date',
            'mandate_end'        => 'nullable|date|after_or_equal:mandate_start',
            'works_with_children'=> 'boolean',
            'email'              => 'nullable|email|max:150',
            'phone'              => 'nullable|string|max:30',
        ]);

        $person->update(array_merge($data, [
            'works_with_children' => $request->boolean('works_with_children'),
            'is_active'           => $request->boolean('is_active', true),
        ]));

        return redirect()->route('people.show', $person)
            ->with('success', 'Dados atualizados.');
    }

    public function destroy(Person $person)
    {
        $person->update(['is_active' => false]);
        return redirect()->route('people.index')
            ->with('success', 'Pessoa desativada.');
    }
}
