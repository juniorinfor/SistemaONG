<?php

namespace App\Http\Controllers;

use App\Models\Institution;
use Illuminate\Http\Request;

class SettingsController extends Controller
{
    private function institution(): Institution
    {
        return Institution::where('slug', 'promessa')->firstOrFail();
    }

    public function index()
    {
        return view('settings.index', [
            'institution' => $this->institution(),
        ]);
    }

    public function update(Request $request)
    {
        $institution = $this->institution();

        $data = $request->validate([
            'name'    => 'required|string|max:255',
            'cnpj'    => 'required|string|max:18',
            'email'   => 'nullable|email|max:255',
            'phone'   => 'nullable|string|max:20',
            'website' => 'nullable|url|max:255',
            'address' => 'nullable|string|max:500',
            'city'    => 'nullable|string|max:100',
            'state'   => 'nullable|string|size:2',
            'mission' => 'nullable|string|max:2000',
        ]);

        $institution->update($data);

        return redirect()->route('settings.index')
            ->with('success', 'Configurações salvas com sucesso.');
    }
}
