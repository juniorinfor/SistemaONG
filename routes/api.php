<?php

use App\Http\Controllers\Api\EditalIngestController;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Api\EditalExtractController;

// Endpoints chamados pelo GitHub Actions — protegidos por token secreto
Route::post('/editais/ingest',  [EditalIngestController::class,  'handle']);
Route::post('/editais/extract', [EditalExtractController::class, 'handle']);
