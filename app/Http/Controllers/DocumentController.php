<?php
namespace App\Http\Controllers;

use App\Models\Document;
use App\Models\DocumentType;
use App\Models\Institution;
use App\Models\Person;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class DocumentController extends Controller
{
    private function institution()
    {
        return Institution::where('slug', 'promessa')->firstOrFail();
    }

    public function index(Request $request)
    {
        $institution = $this->institution();
        $query = Document::where('institution_id', $institution->id)
            ->where('is_current', true)
            ->with(['documentType', 'person'])
            ->orderBy('expires_at');

        if ($request->filled('status')) {
            $today = now();
            $warn  = now()->addDays(config('documents.warn_days_before_expiry', 30));
            if ($request->status === 'valido') {
                $query->where(fn($q) => $q->whereNull('expires_at')->orWhere('expires_at', '>=', $warn));
            } elseif ($request->status === 'vence_em_breve') {
                $query->whereBetween('expires_at', [$today, $warn]);
            } elseif ($request->status === 'vencido') {
                $query->where('expires_at', '<', $today);
            }
        }

        if ($request->filled('category')) {
            $query->whereHas('documentType', fn($q) => $q->where('category', $request->category));
        }

        if ($request->filled('search')) {
            $s = $request->search;
            $query->whereHas('documentType', fn($q) => $q->where('name', 'like', "%$s%"));
        }

        $documents  = $query->paginate(20)->withQueryString();
        $categories = DocumentType::where('institution_id', $institution->id)
            ->select('category')->distinct()->pluck('category');

        return view('documents.index', compact('documents', 'categories'));
    }

    public function create(Request $request)
    {
        $institution   = $this->institution();
        $documentTypes = DocumentType::where('institution_id', $institution->id)
            ->where('is_active', true)->orderBy('category')->orderBy('sort_order')->get()
            ->groupBy('category');
        $people       = Person::where('institution_id', $institution->id)
            ->where('is_active', true)->orderBy('name')->get();
        $selectedType = $request->filled('type_id') ? DocumentType::find($request->type_id) : null;

        return view('documents.create', compact('documentTypes', 'people', 'selectedType'));
    }

    public function store(Request $request)
    {
        $institution = $this->institution();

        $data = $request->validate([
            'document_type_id' => 'required|exists:document_types,id',
            'person_id'        => 'nullable|exists:people,id',
            'file'             => 'required|file|mimes:pdf,jpg,jpeg,png|max:10240',
            'issued_at'        => 'nullable|date',
            'expires_at'       => 'nullable|date',
            'protocol_number'  => 'nullable|string|max:100',
            'notes'            => 'nullable|string|max:1000',
        ]);

        $file = $request->file('file');
        $path = $file->store('documents/' . date('Y/m'), 'local');
        $type = DocumentType::findOrFail($data['document_type_id']);

        Document::where('institution_id', $institution->id)
            ->where('document_type_id', $data['document_type_id'])
            ->when($data['person_id'] ?? null, fn($q, $pid) => $q->where('person_id', $pid))
            ->where('is_current', true)
            ->update(['is_current' => false]);

        Document::create([
            'institution_id'    => $institution->id,
            'document_type_id'  => $data['document_type_id'],
            'person_id'         => $data['person_id'] ?? null,
            'file_path'         => $path,
            'original_filename' => $file->getClientOriginalName(),
            'mime_type'         => $file->getMimeType(),
            'file_size'         => $file->getSize(),
            'issued_at'         => $data['issued_at'] ?? null,
            'expires_at'        => $data['expires_at'] ?? null,
            'protocol_number'   => $data['protocol_number'] ?? null,
            'notes'             => $data['notes'] ?? null,
            'is_public'         => $request->boolean('is_public', $type->is_public_by_default),
            'is_current'        => true,
            'uploaded_by'       => auth()->id(),
        ]);

        return redirect()->route('documents.index')
            ->with('success', 'Documento enviado com sucesso.');
    }

    public function show(Document $document)
    {
        $document->load(['documentType', 'person', 'uploader']);
        $history = Document::where('document_type_id', $document->document_type_id)
            ->where('institution_id', $document->institution_id)
            ->when($document->person_id, fn($q) => $q->where('person_id', $document->person_id))
            ->orderByDesc('created_at')->get();

        return view('documents.show', compact('document', 'history'));
    }

    public function edit(Document $document)
    {
        $institution = $this->institution();
        $people = Person::where('institution_id', $institution->id)
            ->where('is_active', true)->orderBy('name')->get();

        return view('documents.edit', compact('document', 'people'));
    }

    public function update(Request $request, Document $document)
    {
        $data = $request->validate([
            'issued_at'       => 'nullable|date',
            'expires_at'      => 'nullable|date',
            'protocol_number' => 'nullable|string|max:100',
            'notes'           => 'nullable|string|max:1000',
        ]);

        $document->update([
            'issued_at'       => $data['issued_at'] ?? null,
            'expires_at'      => $data['expires_at'] ?? null,
            'protocol_number' => $data['protocol_number'] ?? null,
            'notes'           => $data['notes'] ?? null,
            'is_public'       => $request->boolean('is_public'),
        ]);

        return redirect()->route('documents.show', $document)
            ->with('success', 'Documento atualizado.');
    }

    public function destroy(Document $document)
    {
        Storage::disk('local')->delete($document->file_path);
        $document->delete();

        return redirect()->route('documents.index')
            ->with('success', 'Documento removido.');
    }

    public function download(Document $document)
    {
        abort_unless(Storage::disk('local')->exists($document->file_path), 404);
        return Storage::disk('local')->download($document->file_path, $document->original_filename);
    }
}
