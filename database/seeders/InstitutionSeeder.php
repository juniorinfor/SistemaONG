<?php

namespace Database\Seeders;

use App\Models\Institution;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class InstitutionSeeder extends Seeder
{
    public function run(): void
    {
        Institution::firstOrCreate(
            ['cnpj' => '00.000.000/0001-00'], // substituir pelo CNPJ real
            [
                'name'    => 'Associação Promessa',
                'slug'    => 'promessa',
                'address' => 'Jaboatão dos Guararapes, PE',
                'city'    => 'Jaboatão dos Guararapes',
                'state'   => 'PE',
                'mission' => 'Promover a inclusão social e o desenvolvimento humano de crianças, adolescentes e famílias em situação de vulnerabilidade.',
                'is_active' => true,
            ]
        );
    }
}
