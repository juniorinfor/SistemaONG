<?php
namespace App\Http\Controllers;
use Illuminate\Http\Request;

class DocumentTypeController extends Controller
{
    public function index()   { return view('document-types.index'); }
    public function create()  { return view('document-types.create'); }
    public function store(Request $r)  { return redirect()->route('document-types.index'); }
    public function show($id) { return view('document-types.show', ['type' => \App\Models\DocumentType::findOrFail($id)]); }
    public function edit($id) { return view('document-types.edit', ['type' => \App\Models\DocumentType::findOrFail($id)]); }
    public function update(Request $r, $id) { return redirect()->route('document-types.index'); }
    public function destroy($id) { return redirect()->route('document-types.index'); }
}
