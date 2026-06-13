<?php
namespace App\Http\Controllers;

use App\Models\Document;
use App\Models\Institution;
use Illuminate\Support\Facades\Storage;

class TransparencyController extends Controller
{
    public function index()
    {
        $institution = Institution::where('slug', 'promessa')->first();

        $documents = Document::where('institution_id', $institution?->id)
            ->where('is_public', true)
            ->where('is_current', true)
            ->with('documentType')
            ->orderBy('document_type_id')
            ->get()
            ->groupBy('documentType.category');

        return view('transparency.index', compact('institution', 'documents'));
    }

    public function download(Document $document)
    {
        abort_unless($document->is_public && $document->is_current, 403);
        abort_unless(Storage::disk('local')->exists($document->file_path), 404);
        return Storage::disk('local')->download($document->file_path, $document->original_filename);
    }
}
