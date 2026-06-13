<?php
namespace App\Http\Controllers;

use App\Models\DocumentType;
use App\Models\Institution;
use Illuminate\Http\Request;

class DocumentTypeController extends Controller
{
    public function index()
    {
        $institution = Institution::where('slug', 'promessa')->firstOrFail();

        $types = DocumentType::where('institution_id', $institution->id)
            ->where('is_active', true)
            ->with(['currentDocument' => fn($q) => $q->where('institution_id', $institution->id)])
            ->orderBy('sort_order')
            ->get();

        $currentDocIds = \App\Models\Document::where('institution_id', $institution->id)
            ->where('is_current', true)
            ->pluck('document_type_id')
            ->toArray();
        $currentDocs = collect(array_flip($currentDocIds));

        $grouped = $types->groupBy('category');

        $categoryOrder = ['juridico','federal','estadual','municipal','contabil','titulacao','pessoal'];
        $grouped = collect($categoryOrder)
            ->filter(fn($k) => $grouped->has($k))
            ->mapWithKeys(fn($k) => [$k => $grouped[$k]]);

        return view('document-types.index', compact('grouped', 'currentDocs'));
    }
    public function create()  { return view('document-types.create'); }
    public function store(Request $r)  { return redirect()->route('document-types.index'); }
    public function show($id) { return view('document-types.show', ['type' => \App\Models\DocumentType::findOrFail($id)]); }
    public function edit($id) { return view('document-types.edit', ['type' => \App\Models\DocumentType::findOrFail($id)]); }
    public function update(Request $r, $id) { return redirect()->route('document-types.index'); }
    public function destroy($id) { return redirect()->route('document-types.index'); }
}
