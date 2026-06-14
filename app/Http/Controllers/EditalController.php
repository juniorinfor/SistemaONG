<?php

namespace App\Http\Controllers;

use App\Models\Edital;
use App\Models\EditalAttachment;
use App\Models\Institution;
use App\Models\DocumentType;
use App\Models\Document;
use App\Services\EditalSyncService;
use App\Services\ClaudeExtractionService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class EditalController extends Controller
{
    public function __construct(
        private EditalSyncService $sync,
        private ClaudeExtractionService $claude
    ) {}

    private function institution(): Institution
    {
        return Institution::where('slug', 'promessa')->firstOrFail();
    }

    // ---------------------------------------------------------------
    // Index — listagem com filtros
    // ---------------------------------------------------------------
    public function index(Request $request)
    {
        $institution = $this->institution();

        $query = Edital::where('institution_id', $institution->id)
            ->with('attachments')
            ->orderByRaw("CASE WHEN prazo_inscricao IS NULL THEN 1 ELSE 0 END")
            ->orderBy('prazo_inscricao');

        if ($request->filled('status')) {
            if ($request->status === 'abertos') {
                $query->abertos();
            } elseif ($request->status === 'encerrados') {
                $query->where(fn($q) => $q->where('status', 'encerrado')
                    ->orWhere('prazo_inscricao', '<', now()->toDateString()));
            }
        } else {
            $query->abertos();
        }

        if ($request->filled('area')) {
            $query->where('area', $request->area);
        }

        if ($request->filled('fonte')) {
            $query->where('fonte', $request->fonte);
        }

        if ($request->filled('q')) {
            $query->where('titulo', 'like', '%' . $request->q . '%');
        }

        $editais = $query->paginate(15)->withQueryString();
        $areas   = Edital::where('institution_id', $institution->id)->distinct()->pluck('area')->filter()->sort();
        $lastSync = Edital::where('institution_id', $institution->id)->max('synced_at');

        return view('editais.index', compact('editais', 'areas', 'lastSync'));
    }

    // ---------------------------------------------------------------
    // Show — detalhe do edital
    // ---------------------------------------------------------------
    public function show(Edital $edital)
    {
        $edital->load('attachments');
        return view('editais.show', compact('edital'));
    }

    // ---------------------------------------------------------------
    // Create / Store — cadastro manual
    // ---------------------------------------------------------------
    public function create()
    {
        return view('editais.create');
    }

    public function store(Request $request)
    {
        $institution = $this->institution();

        $validated = $request->validate([
            'titulo'          => 'required|string|max:500',
            'area'            => 'nullable|string|max:100',
            'link_oficial'    => 'nullable|url|max:500',
            'valor_min'       => 'nullable|numeric|min:0',
            'valor_max'       => 'nullable|numeric|min:0',
            'prazo_inscricao' => 'nullable|date',
            'prazo_execucao'  => 'nullable|date',
            'resumo'          => 'nullable|string',
            'criterios'       => 'nullable|string',
            'raw_text'        => 'nullable|string',
            'attachments.*'   => 'nullable|file|max:20480|mimes:pdf,doc,docx,xls,xlsx,jpg,png',
            'attach_links'    => 'nullable|string',
            'attach_nomes'    => 'nullable|string',
            'attach_tipos'    => 'nullable|string',
        ]);

        // Se tem raw_text e sem resumo/criterios, extrai via IA
        if (!empty($validated['raw_text']) && empty($validated['resumo'])) {
            $extracted = $this->claude->extrairEdital($validated['raw_text']);
            if (!isset($extracted['error'])) {
                $validated['resumo']   = $extracted['resumo']   ?? $validated['resumo'];
                $validated['criterios']= $extracted['criterios']?? $validated['criterios'];
                $validated['area']     = $validated['area']     ?: ($extracted['area'] ?? null);
                if (empty($validated['valor_max']) && !empty($extracted['valor_max'])) {
                    $validated['valor_max'] = $extracted['valor_max'];
                }
                if (empty($validated['prazo_inscricao']) && !empty($extracted['prazo_inscricao'])) {
                    $validated['prazo_inscricao'] = $extracted['prazo_inscricao'];
                }
            }
        }

        $edital = Edital::create(array_merge($validated, [
            'institution_id' => $institution->id,
            'fonte'          => 'manual',
            'status'         => 'aberto',
        ]));

        // Processa anexos (arquivos)
        foreach ($request->file('attachments', []) as $idx => $file) {
            $path = $file->store("editais/{$edital->id}", 'local');
            EditalAttachment::create([
                'edital_id'    => $edital->id,
                'nome'         => $file->getClientOriginalName(),
                'arquivo_path' => $path,
                'tipo'         => 'anexo',
            ]);
        }

        // Processa links de anexos (linhas separadas: nome|link|tipo)
        if ($request->filled('attach_links')) {
            foreach (explode("\n", trim($request->attach_links)) as $linha) {
                $partes = explode('|', $linha);
                if (count($partes) >= 2) {
                    EditalAttachment::create([
                        'edital_id' => $edital->id,
                        'nome'      => trim($partes[0]),
                        'link'      => trim($partes[1]),
                        'tipo'      => trim($partes[2] ?? 'anexo'),
                    ]);
                }
            }
        }

        return redirect()->route('editais.show', $edital)
            ->with('success', 'Edital cadastrado com sucesso.');
    }

    // ---------------------------------------------------------------
    // Destroy
    // ---------------------------------------------------------------
    public function destroy(Edital $edital)
    {
        $edital->delete();
        return redirect()->route('editais.index')->with('success', 'Edital removido.');
    }

    // ---------------------------------------------------------------
    // Sync manual (botão "Atualizar agora")
    // ---------------------------------------------------------------
    public function syncNow()
    {
        $institution = $this->institution();
        $results     = $this->sync->syncAll($institution);
        $total       = array_sum($results);

        $msg = $total > 0
            ? "Sincronização concluída: {$total} novo(s) edital(is) encontrado(s)."
            : 'Sincronização concluída. Nenhum edital novo encontrado.';

        return redirect()->route('editais.index')->with('success', $msg);
    }

    // ---------------------------------------------------------------
    // Verificar compatibilidade via IA
    // ---------------------------------------------------------------
    public function checkCompatibility(Edital $edital)
    {
        $institution = $this->institution();

        if (empty($edital->criterios)) {
            return back()->with('error', 'Este edital não possui critérios cadastrados para análise.');
        }

        // Documentos atuais da instituição (apenas nomes — mínimo de tokens)
        $docsDisponiveis = Document::where('documents.institution_id', $institution->id)
            ->where('documents.is_current', true)
            ->join('document_types', 'documents.document_type_id', '=', 'document_types.id')
            ->pluck('document_types.name')
            ->toArray();

        $result = $this->claude->verificarCompatibilidade($edital->criterios, $docsDisponiveis);

        if (isset($result['error'])) {
            return back()->with('error', 'Erro na análise: ' . $result['error']);
        }

        $edital->update([
            'compatibility_score'   => $result['score'] ?? null,
            'compatibility_details' => $result,
        ]);

        return back()->with('success', "Análise concluída. Compatibilidade: {$result['score']}%");
    }

    // ---------------------------------------------------------------
    // Download de anexo
    // ---------------------------------------------------------------
    public function downloadAttachment(EditalAttachment $attachment)
    {
        if ($attachment->arquivo_path && Storage::disk('local')->exists($attachment->arquivo_path)) {
            return Storage::disk('local')->download($attachment->arquivo_path, $attachment->nome);
        }
        if ($attachment->link) {
            return redirect($attachment->link);
        }
        abort(404);
    }
}
