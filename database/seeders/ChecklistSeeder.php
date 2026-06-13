<?php

namespace Database\Seeders;

use App\Models\Checklist;
use App\Models\ChecklistItem;
use App\Models\DocumentType;
use App\Models\Institution;
use Illuminate\Database\Seeder;

class ChecklistSeeder extends Seeder
{
    public function run(): void
    {
        $institution = Institution::where('slug', 'promessa')->firstOrFail();
        $iid = $institution->id;

        // Mapa nome → type para lookup
        $typeByName = DocumentType::where('institution_id', $iid)
            ->pluck('id', 'name');

        $checklists = [
            [
                'name'        => 'Regularidade Geral da ONG',
                'slug'        => 'regularidade-geral',
                'description' => 'Todas as obrigações documentais da associação — visão completa.',
                'legal_basis' => null,
                'items'       => [
                    'Cartão CNPJ — Comprovante de Inscrição (RFB)',
                    'CND Federal — Certidão de Débitos Tributários Federais e Dívida Ativa da União (RFB/PGFN)',
                    'CRF — Certificado de Regularidade do FGTS (Caixa Econômica Federal)',
                    'CNDT — Certidão Negativa de Débitos Trabalhistas (TST)',
                    'Certidão de Regularidade Fiscal — Sefaz-PE',
                    'Certidão Negativa da Dívida Ativa Estadual — PGE-PE',
                    'CND Municipal — ISS, IPTU, Dívida Ativa (Prefeitura de Jaboatão)',
                    'Estatuto Social Consolidado',
                    'Ata de Eleição e Posse da Diretoria',
                    'Alvará de Localização e Funcionamento (Prefeitura de Jaboatão)',
                    'Balanço Patrimonial (assinado por contador CRC)',
                    'Prestação de Contas Anual / Relatório de Atividades',
                ],
            ],
            [
                'name'        => 'MROSC — Parceria com Órgão Público (Lei 13.019/2014, art. 34)',
                'slug'        => 'mrosc-parceria',
                'description' => 'Documentação exigida para celebração de termo de fomento/colaboração conforme MROSC.',
                'legal_basis' => 'Lei 13.019/2014, art. 34',
                'items'       => [
                    'Estatuto Social Consolidado',
                    'Ata de Eleição e Posse da Diretoria',
                    'Relação Nominal da Diretoria com Qualificação (MROSC)',
                    'Comprovante de Endereço da Sede',
                    'Cartão CNPJ — Comprovante de Inscrição (RFB)',
                    'CND Federal — Certidão de Débitos Tributários Federais e Dívida Ativa da União (RFB/PGFN)',
                    'CRF — Certificado de Regularidade do FGTS (Caixa Econômica Federal)',
                    'CNDT — Certidão Negativa de Débitos Trabalhistas (TST)',
                    'Certidão de Regularidade Fiscal — Sefaz-PE',
                    'CND Municipal — ISS, IPTU, Dívida Ativa (Prefeitura de Jaboatão)',
                    'Balanço Patrimonial (assinado por contador CRC)',
                    'Prestação de Contas Anual / Relatório de Atividades',
                    'Declarações MROSC art. 39 (não impedimento)',
                    'Certidão CADIN/CAUC (Transferegov — convênios federais)',
                ],
            ],
            [
                'name'        => 'Inscrição / Renovação no CMDCA',
                'slug'        => 'cmdca',
                'description' => 'Documentação para inscrição ou renovação no Conselho Municipal dos Direitos da Criança e do Adolescente.',
                'legal_basis' => 'Lei 8.069/1990 (ECA); Resolução CONANDA 137/2010',
                'items'       => [
                    'Estatuto Social Consolidado',
                    'Ata de Eleição e Posse da Diretoria',
                    'Relação Nominal da Diretoria com Qualificação (MROSC)',
                    'Cartão CNPJ — Comprovante de Inscrição (RFB)',
                    'CND Federal — Certidão de Débitos Tributários Federais e Dívida Ativa da União (RFB/PGFN)',
                    'CRF — Certificado de Regularidade do FGTS (Caixa Econômica Federal)',
                    'CNDT — Certidão Negativa de Débitos Trabalhistas (TST)',
                    'Certidão de Regularidade Fiscal — Sefaz-PE',
                    'CND Municipal — ISS, IPTU, Dívida Ativa (Prefeitura de Jaboatão)',
                    'Certidão de Antecedentes Criminais — Polícia Federal (dirigentes)',
                    'Certidão de Antecedentes Criminais — Voluntários com Crianças (Polícia Civil-PE)',
                    'Balanço Patrimonial (assinado por contador CRC)',
                    'Prestação de Contas Anual / Relatório de Atividades',
                    'Registro no CMDCA — Conselho Municipal dos Direitos da Criança e do Adolescente',
                ],
            ],
            [
                'name'        => 'Inscrição no CMAS',
                'slug'        => 'cmas',
                'description' => 'Documentação para inscrição ou renovação no Conselho Municipal de Assistência Social.',
                'legal_basis' => 'Lei 8.742/1993 (LOAS); Resolução CNAS 14/2014',
                'items'       => [
                    'Estatuto Social Consolidado',
                    'Ata de Eleição e Posse da Diretoria',
                    'Cartão CNPJ — Comprovante de Inscrição (RFB)',
                    'Comprovante de Endereço da Sede',
                    'CND Federal — Certidão de Débitos Tributários Federais e Dívida Ativa da União (RFB/PGFN)',
                    'CRF — Certificado de Regularidade do FGTS (Caixa Econômica Federal)',
                    'CNDT — Certidão Negativa de Débitos Trabalhistas (TST)',
                    'Certidão de Regularidade Fiscal — Sefaz-PE',
                    'CND Municipal — ISS, IPTU, Dívida Ativa (Prefeitura de Jaboatão)',
                    'Balanço Patrimonial (assinado por contador CRC)',
                    'Prestação de Contas Anual / Relatório de Atividades',
                    'Inscrição no CMAS — Conselho Municipal de Assistência Social de Jaboatão',
                ],
            ],
            [
                'name'        => 'Edital de Fundo Municipal (FIA / FMAS)',
                'slug'        => 'fundo-municipal',
                'description' => 'Documentação típica para participação em editais do Fundo para Infância e Adolescência ou Fundo Municipal de Assistência Social.',
                'legal_basis' => 'Lei 8.069/1990 (ECA); Lei 8.742/1993 (LOAS)',
                'items'       => [
                    'Estatuto Social Consolidado',
                    'Ata de Eleição e Posse da Diretoria',
                    'Relação Nominal da Diretoria com Qualificação (MROSC)',
                    'Cartão CNPJ — Comprovante de Inscrição (RFB)',
                    'CND Federal — Certidão de Débitos Tributários Federais e Dívida Ativa da União (RFB/PGFN)',
                    'CRF — Certificado de Regularidade do FGTS (Caixa Econômica Federal)',
                    'CNDT — Certidão Negativa de Débitos Trabalhistas (TST)',
                    'Certidão de Regularidade Fiscal — Sefaz-PE',
                    'CND Municipal — ISS, IPTU, Dívida Ativa (Prefeitura de Jaboatão)',
                    'Certidão de Antecedentes Criminais — Polícia Federal (dirigentes)',
                    'Balanço Patrimonial (assinado por contador CRC)',
                    'Prestação de Contas Anual / Relatório de Atividades',
                    'Plano de Trabalho / Plano de Ação Anual',
                    'Registro no CMDCA — Conselho Municipal dos Direitos da Criança e do Adolescente',
                    'Inscrição no CMAS — Conselho Municipal de Assistência Social de Jaboatão',
                    'CNEAS — Cadastro Nacional de Entidades de Assistência Social (MDS)',
                    'Declarações MROSC art. 39 (não impedimento)',
                ],
            ],
            [
                'name'        => 'CEBAS — Certificação de Entidade Beneficente',
                'slug'        => 'cebas',
                'description' => 'Documentação para obtenção ou renovação do CEBAS (Certificação de Entidades Beneficentes de Assistência Social).',
                'legal_basis' => 'Lei 12.101/2009; Decreto 8.242/2014',
                'items'       => [
                    'Estatuto Social Consolidado',
                    'Ata de Fundação (registrada em cartório)',
                    'Ata de Eleição e Posse da Diretoria',
                    'Cartão CNPJ — Comprovante de Inscrição (RFB)',
                    'CND Federal — Certidão de Débitos Tributários Federais e Dívida Ativa da União (RFB/PGFN)',
                    'CRF — Certificado de Regularidade do FGTS (Caixa Econômica Federal)',
                    'CNDT — Certidão Negativa de Débitos Trabalhistas (TST)',
                    'Balanço Patrimonial (assinado por contador CRC)',
                    'DRE — Demonstração do Resultado do Exercício',
                    'DMPL, DFC e Notas Explicativas (ITG 2002)',
                    'Parecer do Conselho Fiscal (anual)',
                    'Prestação de Contas Anual / Relatório de Atividades',
                    'Inscrição no CMAS — Conselho Municipal de Assistência Social de Jaboatão',
                    'CNEAS — Cadastro Nacional de Entidades de Assistência Social (MDS)',
                ],
            ],
        ];

        foreach ($checklists as $clData) {
            $items = $clData['items'];
            unset($clData['items']);

            $checklist = Checklist::firstOrCreate(
                ['institution_id' => $iid, 'slug' => $clData['slug']],
                array_merge($clData, ['institution_id' => $iid, 'is_active' => true])
            );

            foreach ($items as $order => $typeName) {
                $typeId = $typeByName[$typeName] ?? null;
                if (!$typeId) continue;

                ChecklistItem::firstOrCreate(
                    ['checklist_id' => $checklist->id, 'document_type_id' => $typeId],
                    ['is_required' => true, 'sort_order' => $order]
                );
            }
        }
    }
}
