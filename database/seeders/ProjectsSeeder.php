<?php

namespace Database\Seeders;

use App\Models\Institution;
use App\Models\Project;
use Illuminate\Database\Seeder;

class ProjectsSeeder extends Seeder
{
    public function run(): void
    {
        $institution = Institution::where('slug', 'promessa')->firstOrFail();

        $projects = [
            [
                'title'           => 'Promessa Clic',
                'area'            => 'Cultura',
                'status'          => 'em_elaboracao',
                'valor_pleiteado' => 38000.00,
                'description'     => 'Curso de fotografia digital para jovens de 14 a 24 anos de comunidades de Jaboatão dos Guararapes. Os participantes aprendem técnicas fotográficas, edição de imagem e narrativa visual, com foco no registro da própria comunidade como ferramenta de protagonismo e comunicação. Ao final, realizam uma exposição fotográfica comunitária e recebem certificado.

Atividades previstas:
- 60h de aulas teóricas e práticas (fotografia, composição, luz, edição)
- Saídas fotográficas pelo território
- Produção de ensaio fotográfico individual
- Exposição itinerante com catálogo impresso
- Formatura com entrega de certificados

Itens orçamentários principais: instrutor/oficineiro, câmeras e acessórios (softbox, tripé), smartphones para edição, material didático, designer e gestão de redes sociais, impulsionamento Meta, local/logística, formatura e exposição.',
                'notes'           => 'Fontes prioritárias: Lei Paulo Gustavo, FUNCULTURA-PE, Fundo de Cultura Municipal (Jaboatão), patrocínio privado via Lei Rouanet. Turma prevista: 20 jovens. Parceria possível com escolas municipais para seleção dos participantes.',
            ],
            [
                'title'           => 'Promessa em Cena',
                'area'            => 'Cultura',
                'status'          => 'em_elaboracao',
                'valor_pleiteado' => 48000.00,
                'description'     => 'Oficinas de produção audiovisual e podcast para jovens de 14 a 24 anos. Os participantes aprendem roteiro, filmagem, edição de vídeo e produção de áudio para criar conteúdos sobre a realidade, cultura e potencial das suas comunidades.

Atividades previstas:
- 80h de oficinas (roteiro, direção, câmera, edição, podcast)
- Produção de 3 curta-metragens coletivos
- Criação de 10 episódios de podcast
- Lançamento em plataformas digitais e exibição comunitária
- Formatura com mostra audiovisual

Itens orçamentários principais: instrutor audiovisual, equipamentos (câmera, microfone, iluminação), softwares de edição, gravação de podcast, streaming e divulgação, gestão de projeto.',
                'notes'           => 'Fontes prioritárias: Lei Paulo Gustavo, ANCINE, Fundo Setorial do Audiovisual, editais de cultura digital. Potencial de parceria com emissoras locais e plataformas regionais de streaming.',
            ],
            [
                'title'           => 'Promessa Bem-Viver',
                'area'            => 'Saúde Mental',
                'status'          => 'em_elaboracao',
                'valor_pleiteado' => 28000.00,
                'description'     => 'Programa de saúde mental comunitária para adolescentes e jovens de 12 a 22 anos, com foco em escuta, prevenção e fortalecimento de vínculos. Utiliza rodas de conversa, arte-terapia leve e grupos temáticos como metodologia, com facilitação de profissional de psicologia.

Atividades previstas:
- Rodas de conversa semanais (12 encontros por turma)
- 2 turmas simultâneas de 15 participantes
- Grupos temáticos: ansiedade, autoestima, violência, redes sociais
- Formação de lideranças juvenis em escuta ativa
- Relatório e devolutiva para famílias

Itens orçamentários principais: psicólogo/facilitador, material de apoio, lanche e transporte dos participantes, divulgação, gestão do projeto.',
                'notes'           => 'Fontes prioritárias: FIA (Fundo da Infância e Adolescência), FUMCAD, editais da Secretaria de Saúde, fundações privadas (Abrinq, Robert Bosch). Alta relevância pós-pandemia — boa receptividade em editais de saúde.',
            ],
            [
                'title'           => 'Promessa Digital',
                'area'            => 'Tecnologia',
                'status'          => 'em_elaboracao',
                'valor_pleiteado' => 42000.00,
                'description'     => 'Curso de iniciação em programação, design digital e lógica computacional para jovens de 15 a 24 anos, visando inserção no mercado de trabalho de tecnologia. Foco em habilidades práticas com ferramentas acessíveis e projetos reais.

Atividades previstas:
- 100h de formação (lógica, HTML/CSS, Python básico, Canva/Figma, pacote Office)
- Projetos práticos: site da ONG, artes para redes sociais, planilhas
- Mentoria com profissionais de TI da região
- Certificação e portfólio digital individual
- Conexão com empresas parceiras para oportunidades de emprego/estágio

Itens orçamentários principais: instrutor de TI, notebooks ou tablets, acesso à internet, plataformas educacionais, material didático, certificação, gestão e divulgação.',
                'notes'           => 'Fontes prioritárias: Editais de inclusão digital (Governo Federal), Instituto Itaú, Instituto iFood, Microsoft TEALS, patrocínio de empresas de tecnologia. Modelo escalável e com alto impacto mensurável (empregabilidade).',
            ],
            [
                'title'           => 'Empreendedor Promessa',
                'area'            => 'Geração de Renda',
                'status'          => 'em_elaboracao',
                'valor_pleiteado' => 32000.00,
                'description'     => 'Programa de capacitação em empreendedorismo, economia criativa e marketing digital para jovens e adultos de 18 a 35 anos que desejam formalizar ou iniciar um negócio. Combina formação teórica com mentorias práticas e suporte ao plano de negócios.

Atividades previstas:
- 60h de formação (empreendedorismo, MEI, finanças pessoais e empresariais, marketing digital)
- Elaboração de plano de negócios individual
- Mentoria coletiva com empreendedores locais
- Feira de negócios comunitária (pitchs e exposição)
- Acompanhamento pós-curso por 3 meses

Itens orçamentários principais: mentor/instrutor, material didático, planilhas e ferramentas online, organização da feira, divulgação, gestão do projeto.',
                'notes'           => 'Fontes prioritárias: Sebrae (editais de empreendedorismo social), FIA, Banco do Nordeste (BNB Fundo de Cultura), editais de geração de renda municipais e estaduais. Parceria natural com Sebrae-PE para co-facilitação.',
            ],
            [
                'title'           => 'Promessa em Campo',
                'area'            => 'Esporte e Lazer',
                'status'          => 'em_elaboracao',
                'valor_pleiteado' => 34000.00,
                'description'     => 'Escolinha esportiva multiesportiva (futebol, futsal e vôlei) para crianças e adolescentes de 8 a 17 anos, utilizando o esporte como ferramenta de desenvolvimento social, prevenção ao uso de drogas e fortalecimento da permanência escolar.

Atividades previstas:
- Treinos 3x por semana, 2 turmas (8–12 anos e 13–17 anos)
- 40 vagas no total
- Componente educativo mensal: direitos, saúde, cidadania
- Torneio comunitário no final do semestre
- Acompanhamento da frequência escolar dos participantes

Itens orçamentários principais: treinador/educador, materiais esportivos (bolas, coletes, redes, uniformes), aluguel/manutenção do espaço, lanche pós-treino, gestão e divulgação.',
                'notes'           => 'Fontes prioritárias: Lei de Incentivo ao Esporte (Federal), Ministério do Esporte, Governo do Estado de PE (SEJESP), patrocínio corporativo local. Parceria com escolas municipais para seleção e acompanhamento escolar.',
            ],
            [
                'title'           => 'Promessa do Amanhã',
                'area'            => 'Juventude',
                'status'          => 'em_elaboracao',
                'valor_pleiteado' => 26000.00,
                'description'     => 'Programa de preparação para o primeiro emprego voltado a jovens de 16 a 24 anos, com foco em habilidades comportamentais, elaboração de currículo, simulações de entrevista, direitos trabalhistas e ferramentas digitais para busca de vagas.

Atividades previstas:
- 40h de formação presencial (soft skills, currículo, LinkedIn, entrevistas, CLT e MEI)
- 2 turmas de 20 jovens
- Simulação de entrevistas com empresas parceiras
- Rodada de empregabilidade com RHs locais
- Acompanhamento por 60 dias após conclusão do curso

Itens orçamentários principais: instrutor/facilitador, material didático, internet para acesso às plataformas, organização da rodada de empregabilidade, certificação, gestão e divulgação.',
                'notes'           => 'Fontes prioritárias: Lei do Aprendiz (responsabilidade social empresarial), FIA, Secretaria Municipal de Trabalho, editais de empregabilidade juvenil. Parceria com CIEE, IEL-PE e empresas do polo industrial de Jaboatão.',
            ],
            [
                'title'           => 'Promessa do Saber',
                'area'            => 'Educação',
                'status'          => 'em_elaboracao',
                'valor_pleiteado' => 40000.00,
                'description'     => 'Programa de reforço escolar e cursos de idioma (inglês e espanhol básico) para crianças e adolescentes de 10 a 17 anos em situação de defasagem de aprendizagem, com foco em combate à evasão e melhoria no desempenho escolar.

Atividades previstas:
- Reforço em Matemática e Português (3x por semana)
- Curso de inglês básico (60h) e espanhol básico (60h)
- 3 turmas de 15 alunos cada
- Avaliação diagnóstica no início e ao final
- Formatura com exibição das produções dos alunos

Itens orçamentários principais: professores de reforço, professor de inglês, professor de espanhol, material didático e apostilas, plataformas de idioma online, lanche, certificação, gestão e divulgação.',
                'notes'           => 'Fontes prioritárias: FIA/FUMCAD, Secretaria Municipal de Educação (Jaboatão), fundações (Lemann, Itaú Social, Bradesco), editais federais de educação. Parceria com escolas públicas do entorno para encaminhamento de alunos.',
            ],
            [
                'title'           => 'Meninas Promessa',
                'area'            => 'Tecnologia',
                'status'          => 'em_elaboracao',
                'valor_pleiteado' => 36000.00,
                'description'     => 'Programa de iniciação tecnológica exclusivo para meninas e jovens mulheres de 13 a 22 anos, visando reduzir o gap de gênero na área de tecnologia. As participantes aprendem programação, design e empreendedorismo digital com mentoria de mulheres que atuam no setor de TI.

Atividades previstas:
- 80h de formação (programação criativa, design, criação de conteúdo digital)
- Mentoria com profissionais mulheres de TI (presencial e online)
- Visitas técnicas a empresas de tecnologia
- Hackathon final com desafios reais de empresas parceiras
- Certificação e portfólio digital

Itens orçamentários principais: instrutora de TI, equipamentos (tablets/notebooks), plataformas de aprendizagem, transporte para visitas técnicas, organização do hackathon, divulgação, gestão.',
                'notes'           => 'Fontes prioritárias: ONU Mulheres, UNDP Brasil, editais de equidade de gênero, Instituto Natura, Google.org, patrocínio de empresas com metas de diversidade. Diferencial competitivo: foco em gênero + periferia + tecnologia — tríade de alta atratividade para fundos internacionais.',
            ],
            [
                'title'           => 'Promessa Verde',
                'area'            => 'Meio Ambiente',
                'status'          => 'em_elaboracao',
                'valor_pleiteado' => 24000.00,
                'description'     => 'Implantação de hortas comunitárias urbanas integradas ao projeto Mesa Cheia, combinando educação ambiental, segurança alimentar e geração de renda. As famílias participantes cultivam e consomem os alimentos, com excedente destinado ao projeto de distribuição já existente.

Atividades previstas:
- Implantação de 4 hortas comunitárias (escolas, UBS e sedes comunitárias)
- Oficinas de agricultura urbana, compostagem e agrofloresta (30h)
- 60 famílias beneficiadas diretamente
- Integração com o Mesa Cheia: abastecimento com produção local
- Oficina de aproveitamento integral dos alimentos
- Feira de troca de sementes e mudas

Itens orçamentários principais: instrutor agrícola/ambientalista, insumos (sementes, terra, vasos/canteiros, ferramentas), estrutura das hortas, material educativo, integração com Mesa Cheia, gestão e divulgação.',
                'notes'           => 'Fontes prioritárias: Petrobras Socioambiental, editais ESG de empresas, Fundo Amazônia (ações urbanas), editais de segurança alimentar (MDS), fundações ambientais. Potencial para captação junto a supermercados e redes de varejo com programas de ESG.',
            ],
        ];

        foreach ($projects as $data) {
            Project::firstOrCreate(
                ['institution_id' => $institution->id, 'title' => $data['title']],
                array_merge($data, ['institution_id' => $institution->id])
            );
        }

        $this->command->info('10 projetos da Promessa criados com sucesso.');
    }
}
