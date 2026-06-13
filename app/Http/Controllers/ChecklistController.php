<?php
namespace App\Http\Controllers;

use App\Models\Checklist;
use App\Models\ChecklistItem;
use App\Models\Document;
use App\Models\DocumentType;
use App\Models\Institution;

class ChecklistController extends Controller
{
    private function institution()
    {
        return Institution::where('slug', 'promessa')->firstOrFail();
    }

    public function index()
    {
        $institution = $this->institution();
        $checklists  = Checklist::where('institution_id', $institution->id)
            ->where('is_active', true)
            ->withCount('items')
            ->get()
            ->map(function ($cl) use ($institution) {
                $cl->pct     = $cl->getReadinessPercentage($institution);
                $required    = $cl->items()->where('is_required', true)->count();
                $covered     = (int) round($cl->pct * $required / 100);
                $cl->missing = $required - $covered;
                return $cl;
            });

        return view('checklists.index', compact('checklists'));
    }

    public function show(Checklist $checklist)
    {
        $institution = $this->institution();
        $checklist->load('items.documentType');
        $pct = $checklist->getReadinessPercentage($institution);

        $currentDocs = Document::where('institution_id', $institution->id)
            ->where('is_current', true)
            ->whereIn('document_type_id', $checklist->items->pluck('document_type_id'))
            ->with('documentType', 'person')
            ->get()
            ->keyBy('document_type_id');

        return view('checklists.show', compact('checklist', 'pct', 'currentDocs'));
    }

    // Checklists são gerenciados por seeder; não expõe create/store/edit/update/destroy ao usuário
    public function create()  { abort(404); }
    public function store(\Illuminate\Http\Request $r) { abort(404); }
    public function edit(Checklist $checklist) { abort(404); }
    public function update(\Illuminate\Http\Request $r, Checklist $checklist) { abort(404); }
    public function destroy(Checklist $checklist) { abort(404); }
}
