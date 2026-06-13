<?php
namespace App\Http\Controllers;

use App\Models\Checklist;
use App\Models\Document;
use App\Models\DocumentType;
use App\Models\Institution;

class DashboardController extends Controller
{
    public function index()
    {
        $institution = Institution::where('slug', 'promessa')->firstOrFail();
        $iid = $institution->id;

        $total   = Document::where('institution_id', $iid)->where('is_current', true)->count();
        $validos = Document::where('institution_id', $iid)->where('is_current', true)
            ->where(fn($q) => $q->whereNull('expires_at')->orWhere('expires_at', '>=', now()->addDays(30)))
            ->count();
        $breve   = Document::where('institution_id', $iid)->where('is_current', true)->expiringSoon(30)->count();
        $vencidos = Document::where('institution_id', $iid)->where('is_current', true)
            ->where('expires_at', '<', now())->count();

        $expiring = Document::where('institution_id', $iid)
            ->where('is_current', true)
            ->expiringSoon(30)
            ->with('documentType')
            ->orderBy('expires_at')
            ->take(8)
            ->get();

        $expired = Document::where('institution_id', $iid)
            ->where('is_current', true)
            ->where('expires_at', '<', now())
            ->with('documentType')
            ->orderBy('expires_at')
            ->get();

        $checklists = Checklist::where('institution_id', $iid)
            ->where('is_active', true)
            ->get()
            ->map(function ($cl) use ($institution) {
                $pct     = $cl->getReadinessPercentage($institution);
                $required = $cl->items()->where('is_required', true)->count();
                $covered = (int) round($pct * $required / 100);
                return [
                    'id'      => $cl->id,
                    'name'    => $cl->name,
                    'pct'     => $pct,
                    'missing' => $required - $covered,
                ];
            });

        $catalog = DocumentType::where('institution_id', $iid)
            ->select('category')
            ->selectRaw('COUNT(*) as total')
            ->groupBy('category')
            ->pluck('total', 'category');

        return view('dashboard', compact(
            'total', 'validos', 'breve', 'vencidos',
            'expiring', 'expired', 'checklists', 'catalog'
        ));
    }
}
