<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DocumentController;
use App\Http\Controllers\DocumentTypeController;
use App\Http\Controllers\PersonController;
use App\Http\Controllers\ChecklistController;
use App\Http\Controllers\ProjectController;
use App\Http\Controllers\SettingsController;
use App\Http\Controllers\TransparencyController;
use App\Http\Controllers\EditalController;
use App\Http\Controllers\BeneficiarioController;
use App\Http\Controllers\AcaoController;
use Illuminate\Support\Facades\Route;

// Raiz → login ou dashboard
Route::get('/', function () {
    return auth()->check()
        ? redirect()->route('dashboard')
        : redirect()->route('login');
});

// Área autenticada
Route::middleware(['auth'])->group(function () {

    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    Route::resource('documents', DocumentController::class);
    Route::get('/documents/{document}/download', [DocumentController::class, 'download'])->name('documents.download');
    Route::resource('document-types', DocumentTypeController::class);
    Route::resource('people',         PersonController::class);
    Route::resource('checklists',     ChecklistController::class);

    Route::resource('projects', ProjectController::class);
    Route::post('/projects/{project}/attachments', [ProjectController::class, 'storeAttachment'])->name('projects.attachments.store');
    Route::get('/projects/{project}/attachments/{attachment}/download', [ProjectController::class, 'downloadAttachment'])->name('projects.attachments.download');
    Route::delete('/projects/{project}/attachments/{attachment}', [ProjectController::class, 'destroyAttachment'])->name('projects.attachments.destroy');

    Route::get('/settings',   [SettingsController::class, 'index'])->name('settings.index');
    Route::patch('/settings', [SettingsController::class, 'update'])->name('settings.update');

    // Radar de Editais
    Route::get('/editais/sync', [EditalController::class, 'syncNow'])->name('editais.sync');
    Route::get('/editais/analisar',  [EditalController::class, 'analisarForm'])->name('editais.analisar');
    Route::post('/editais/analisar', [EditalController::class, 'analisar'])->name('editais.analisar.store');
    Route::post('/editais/{edital}/compatibility', [EditalController::class, 'checkCompatibility'])->name('editais.compatibility');
    Route::patch('/editais/{edital}/submissao', [EditalController::class, 'updateSubmissao'])->name('editais.update-submissao');
    Route::post('/editais/{edital}/sugerir-projetos', [EditalController::class, 'sugerirProjetos'])->name('editais.sugerir');
    Route::post('/editais/{edital}/gerar-projeto', [EditalController::class, 'gerarProjeto'])->name('editais.gerar-projeto');
    Route::get('/editais/{attachment}/download', [EditalController::class, 'downloadAttachment'])->name('editais.attachment.download');
    Route::resource('editais', EditalController::class)
        ->only(['index','show','create','store','destroy'])
        ->parameters(['editais' => 'edital']);

    // Beneficiários
    Route::resource('beneficiarios', BeneficiarioController::class);

    // Ações + Sessões
    Route::resource('acoes', AcaoController::class);
    Route::get('/acoes/{acao}/relatorio', [AcaoController::class, 'relatorio'])->name('acoes.relatorio');
    Route::post('/acoes/{acao}/sessoes', [AcaoController::class, 'storeSessao'])->name('acoes.sessao.store');
    Route::get('/acoes/{acao}/sessoes/{sessao}', [AcaoController::class, 'showSessao'])->name('acoes.sessao.show');
    Route::post('/acoes/{acao}/sessoes/{sessao}/presenca', [AcaoController::class, 'storePresenca'])->name('acoes.sessao.presenca');

    // Perfil Breeze
    Route::get('/profile',    [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile',  [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// Portal público (sem login)
Route::get('/transparencia',          [TransparencyController::class, 'index'])->name('transparency.index');
Route::get('/transparencia/documento/{document}/download',
    [TransparencyController::class, 'download'])->name('transparency.download');

require __DIR__.'/auth.php';
