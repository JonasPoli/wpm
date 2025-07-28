<?php

namespace App\Command;

use App\Entity\CommemorativeDates;
use App\Repository\CommemorativeDatesRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'app:inject-commemorative',
    description: 'Inject commemorative dates',
)]
class InjectCommemorativeCommand extends Command
{
    public $io;

    public function __construct(private EntityManagerInterface $entityManager, private CommemorativeDatesRepository $commemorativeDatesRepository)
    {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->addArgument('arg1', InputArgument::OPTIONAL, 'Argument description')
            ->addOption('option1', null, InputOption::VALUE_NONE, 'Option description');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $this->io = new SymfonyStyle($input, $output);
        $arg1 = $input->getArgument('arg1');

        if ($arg1) {
            $this->io->note(sprintf('You passed an argument: %s', $arg1));
        }

        if ($input->getOption('option1')) {
            // ...
        }


        $commemorativeDates = [


            ['day' => 1, 'month' => 1, 'title' => 'Dia da Confraternização Universal', 'description' => 'Data comemorativa que marca o início do ano e celebra a paz mundial.'],
            ['day' => 8, 'month' => 1, 'title' => 'Dia do Fotógrafo', 'description' => 'Comemoração do dia dos fotógrafos.'],
            ['day' => 12, 'month' => 1, 'title' => 'Dia do Empresário Contábil', 'description' => 'Comemoração do dia dos empresários contábeis.'],
            ['day' => 20, 'month' => 1, 'title' => 'Dia do Farmacêutico', 'description' => 'Comemoração do dia dos farmacêuticos.'],
            ['day' => 24, 'month' => 1, 'title' => 'Dia da Previdência Social', 'description' => 'Comemoração do dia da Previdência Social.'],
            ['day' => 25, 'month' => 1, 'title' => 'Dia do Carteiro', 'description' => 'Comemoração do dia dos carteiros.'],
            ['day' => 27, 'month' => 1, 'title' => 'Dia do Orador', 'description' => 'Comemoração do dia dos oradores.'],
            ['day' => 2, 'month' => 2, 'title' => 'Dia do Agente Fiscal', 'description' => 'Comemoração do dia dos agentes fiscais.'],
            ['day' => 7, 'month' => 2, 'title' => 'Dia do Gráfico', 'description' => 'Comemoração do dia dos gráficos.'],
            ['day' => 9, 'month' => 2, 'title' => 'Dia do Zelador', 'description' => 'Comemoração do dia dos zeladores.'],
            ['day' => 11, 'month' => 2, 'title' => 'Dia do Enfermo', 'description' => 'Comemoração do dia dos enfermos.'],
            ['day' => 14, 'month' => 2, 'title' => 'Dia do Amor', 'description' => 'Comemoração do dia do amor.'],
            ['day' => 16, 'month' => 2, 'title' => 'Dia do Repórter', 'description' => 'Comemoração do dia dos repórteres.'],
            ['day' => 19, 'month' => 2, 'title' => 'Dia do Esportista', 'description' => 'Comemoração do dia dos esportistas.'],
            ['day' => 27, 'month' => 2, 'title' => 'Dia do Agente Fiscal da Receita Federal', 'description' => 'Comemoração do dia dos agentes fiscais da Receita Federal.'],
            ['day' => 2, 'month' => 3, 'title' => 'Dia Nacional do Turismo', 'description' => 'Comemoração do dia do turismo no Brasil.'],
            ['day' => 7, 'month' => 3, 'title' => 'Dia do Fuzileiro Naval', 'description' => 'Comemoração do dia dos fuzileiros navais.'],
            ['day' => 8, 'month' => 3, 'title' => 'Dia Internacional da Mulher', 'description' => 'Comemoração do dia internacional das mulheres.'],
            ['day' => 9, 'month' => 3, 'title' => 'Dia do DJ', 'description' => 'Comemoração do dia dos DJs.'],
            ['day' => 12, 'month' => 3, 'title' => 'Dia do Bibliotecário', 'description' => 'Comemoração do dia dos bibliotecários.'],
            ['day' => 14, 'month' => 3, 'title' => 'Dia do Vendedor de Livros', 'description' => 'Comemoração do dia dos vendedores de livros.'],
            ['day' => 15, 'month' => 3, 'title' => 'Dia da Escola', 'description' => 'Comemoração do dia das escolas.'],
            ['day' => 19, 'month' => 3, 'title' => 'Dia do Carpinteiro e do Marceneiro', 'description' => 'Comemoração do dia dos carpinteiros e marceneiros.'],
            ['day' => 20, 'month' => 3, 'title' => 'Dia do Contador de Histórias', 'description' => 'Comemoração do dia dos contadores de histórias.'],
            ['day' => 21, 'month' => 3, 'title' => 'Dia Internacional Contra a Discriminação Racial', 'description' => 'Comemoração do dia internacional contra a discriminação racial.'],
            ['day' => 22, 'month' => 3, 'title' => 'Dia Mundial da Água', 'description' => 'Comemoração do dia mundial da água.'],
            ['day' => 27, 'month' => 3, 'title' => 'Dia do Circo', 'description' => 'Comemoração do dia do circo.'],
            ['day' => 28, 'month' => 3, 'title' => 'Dia do Diagramador', 'description' => 'Comemoração do dia dos diagramadores.'],
            ['day' => 28, 'month' => 3, 'title' => 'Dia do Revisor', 'description' => 'Comemoração do dia dos revisores.'],
            ['day' => 7, 'month' => 4, 'title' => 'Dia do Jornalista', 'description' => 'Comemoração do dia dos jornalistas.'],
            ['day' => 8, 'month' => 4, 'title' => 'Dia da Natação', 'description' => 'Comemoração do dia da natação.'],
            ['day' => 12, 'month' => 4, 'title' => 'Dia do Obstetra', 'description' => 'Comemoração do dia dos obstetras.'],
            ['day' => 13, 'month' => 4, 'title' => 'Dia do Office-Boy', 'description' => 'Comemoração do dia dos office-boys.'],
            ['day' => 15, 'month' => 4, 'title' => 'Dia Mundial do Desenhista', 'description' => 'Comemoração do dia mundial dos desenhistas.'],
            ['day' => 19, 'month' => 4, 'title' => 'Dia do Índio', 'description' => 'Comemoração do dia dos índios.'],
            ['day' => 21, 'month' => 4, 'title' => 'Dia da Latinidade', 'description' => 'Comemoração do dia da latinidade.'],
            ['day' => 22, 'month' => 4, 'title' => 'Dia da Terra', 'description' => 'Comemoração do dia da terra.'],
            ['day' => 23, 'month' => 4, 'title' => 'Dia Mundial do Escoteiro', 'description' => 'Comemoração do dia mundial dos escoteiros.'],
            ['day' => 26, 'month' => 4, 'title' => 'Dia do Goleiro', 'description' => 'Comemoração do dia dos goleiros.'],
            ['day' => 28, 'month' => 4, 'title' => 'Dia da Educação', 'description' => 'Comemoração do dia da educação.'],
            ['day' => 30, 'month' => 4, 'title' => 'Dia do Ferroviário', 'description' => 'Comemoração do dia dos ferroviários.'],
            ['day' => 1, 'month' => 5, 'title' => 'Dia do Trabalho', 'description' => 'Comemoração do dia do trabalho.'],
            ['day' => 2, 'month' => 5, 'title' => 'Dia Nacional do Ex-combatente', 'description' => 'Comemoração do dia nacional dos ex-combatentes.'],
            ['day' => 3, 'month' => 5, 'title' => 'Dia do Sertanejo', 'description' => 'Comemoração do dia dos sertanejos.'],
            ['day' => 5, 'month' => 5, 'title' => 'Dia Nacional do Líder Comunitário', 'description' => 'Comemoração do dia nacional dos líderes comunitários.'],
            ['day' => 6, 'month' => 5, 'title' => 'Dia do Cartógrafo', 'description' => 'Comemoração do dia dos cartógrafos.'],
            ['day' => 7, 'month' => 5, 'title' => 'Dia do Oftalmologista', 'description' => 'Comemoração do dia dos oftalmologistas.'],
            ['day' => 8, 'month' => 5, 'title' => 'Dia do Profissional de Marketing', 'description' => 'Comemoração do dia dos profissionais de marketing.'],
            ['day' => 10, 'month' => 5, 'title' => 'Dia do Campo', 'description' => 'Comemoração do dia do campo.'],
            ['day' => 12, 'month' => 5, 'title' => 'Dia do Enfermeiro', 'description' => 'Comemoração do dia dos enfermeiros.'],
            ['day' => 13, 'month' => 5, 'title' => 'Dia do Automóvel', 'description' => 'Comemoração do dia do automóvel.'],
            ['day' => 15, 'month' => 5, 'title' => 'Dia do Assistente Social', 'description' => 'Comemoração do dia dos assistentes sociais.'],
            ['day' => 16, 'month' => 5, 'title' => 'Dia do Gari', 'description' => 'Comemoração do dia dos garis.'],
            ['day' => 18, 'month' => 5, 'title' => 'Dia dos Vidreiros', 'description' => 'Comemoração do dia dos vidreiros.'],
            ['day' => 19, 'month' => 5, 'title' => 'Dia do Defensor Público', 'description' => 'Comemoração do dia dos defensores públicos.'],
            ['day' => 20, 'month' => 5, 'title' => 'Dia do Pedagogo', 'description' => 'Comemoração do dia dos pedagogos.'],
            ['day' => 24, 'month' => 5, 'title' => 'Dia do Telegrafista', 'description' => 'Comemoração do dia dos telegrafistas.'],
            ['day' => 27, 'month' => 5, 'title' => 'Dia do Profissional Liberal', 'description' => 'Comemoração do dia dos profissionais liberais.'],
            ['day' => 29, 'month' => 5, 'title' => 'Dia do Estatístico', 'description' => 'Comemoração do dia dos estatísticos.'],
            ['day' => 29, 'month' => 5, 'title' => 'Dia do Geógrafo', 'description' => 'Comemoração do dia dos geógrafos.'],
            ['day' => 30, 'month' => 5, 'title' => 'Dia do Geólogo', 'description' => 'Comemoração do dia dos geólogos.'],
            ['day' => 5, 'month' => 6, 'title' => 'Dia Mundial do Meio Ambiente', 'description' => 'Comemoração do dia mundial do meio ambiente.'],
            ['day' => 6, 'month' => 6, 'title' => 'Dia Nacional do Teste do Pezinho', 'description' => 'Comemoração do dia nacional do teste do pezinho.'],
            ['day' => 7, 'month' => 6, 'title' => 'Dia Nacional da Liberdade de Imprensa', 'description' => 'Comemoração do dia nacional da liberdade de imprensa.'],
            ['day' => 9, 'month' => 6, 'title' => 'Dia do Porteiro', 'description' => 'Comemoração do dia dos porteiros.'],
            ['day' => 10, 'month' => 6, 'title' => 'Dia da Língua Portuguesa', 'description' => 'Comemoração do dia da língua portuguesa.'],
            ['day' => 11, 'month' => 6, 'title' => 'Dia da Marinha Brasileira', 'description' => 'Comemoração do dia da Marinha Brasileira.'],
            ['day' => 12, 'month' => 6, 'title' => 'Dia dos Namorados', 'description' => 'Comemoração do dia dos namorados.'],
            ['day' => 15, 'month' => 6, 'title' => 'Dia do Paleontólogo', 'description' => 'Comemoração do dia dos paleontólogos.'],
            ['day' => 18, 'month' => 6, 'title' => 'Dia do Químico', 'description' => 'Comemoração do dia dos químicos.'],
            ['day' => 21, 'month' => 6, 'title' => 'Dia da Mídia', 'description' => 'Comemoração do dia da mídia.'],
            ['day' => 24, 'month' => 6, 'title' => 'Dia do Caboclo', 'description' => 'Comemoração do dia dos caboclos.'],
            ['day' => 29, 'month' => 6, 'title' => 'Dia do Pescador', 'description' => 'Comemoração do dia dos pescadores.'],
            ['day' => 1, 'month' => 7, 'title' => 'Dia da Vacina BCG', 'description' => 'Comemoração do dia da vacina BCG.'],
            ['day' => 2, 'month' => 7, 'title' => 'Dia do Bombeiro Brasileiro', 'description' => 'Comemoração do dia dos bombeiros brasileiros.'],
            ['day' => 4, 'month' => 7, 'title' => 'Dia do Operador de Telemarketing', 'description' => 'Comemoração do dia dos operadores de telemarketing.'],
            ['day' => 10, 'month' => 7, 'title' => 'Dia da Pizza', 'description' => 'Comemoração do dia da pizza.'],
            ['day' => 13, 'month' => 7, 'title' => 'Dia do Cantor', 'description' => 'Comemoração do dia dos cantores.'],
            ['day' => 16, 'month' => 7, 'title' => 'Dia do Comerciante', 'description' => 'Comemoração do dia dos comerciantes.'],
            ['day' => 19, 'month' => 7, 'title' => 'Dia Nacional do Futebol', 'description' => 'Comemoração do dia nacional do futebol.'],
            ['day' => 20, 'month' => 7, 'title' => 'Dia do Amigo e Internacional da Amizade', 'description' => 'Comemoração do dia do amigo e internacional da amizade.'],
            ['day' => 25, 'month' => 7, 'title' => 'Dia do Escritor', 'description' => 'Comemoração do dia dos escritores.'],
            ['day' => 25, 'month' => 7, 'title' => 'Dia do Motorista', 'description' => 'Comemoração do dia dos motoristas.'],
            ['day' => 25, 'month' => 7, 'title' => 'Dia do Colono', 'description' => 'Comemoração do dia dos colonos.'],
            ['day' => 25, 'month' => 7, 'title' => 'Dia do Trabalhador Rural', 'description' => 'Comemoração do dia dos trabalhadores rurais.'],
            ['day' => 27, 'month' => 7, 'title' => 'Dia do Despachante', 'description' => 'Comemoração do dia dos despachantes.'],
            ['day' => 27, 'month' => 7, 'title' => 'Dia do Motociclista', 'description' => 'Comemoração do dia dos motociclistas.'],
            ['day' => 28, 'month' => 7, 'title' => 'Dia do Agricultor', 'description' => 'Comemoração do dia dos agricultores.'],
            ['day' => 1, 'month' => 8, 'title' => 'Dia Nacional dos Portadores de Vitiligo', 'description' => 'Comemoração do dia nacional dos portadores de vitiligo.'],
            ['day' => 3, 'month' => 8, 'title' => 'Dia do Capoeirista', 'description' => 'Comemoração do dia dos capoeiristas.'],
            ['day' => 5, 'month' => 8, 'title' => 'Dia Nacional da Saúde', 'description' => 'Comemoração do dia nacional da saúde.'],
            ['day' => 11, 'month' => 8, 'title' => 'Dia do Advogado', 'description' => 'Comemoração do dia dos advogados.'],
            ['day' => 11, 'month' => 8, 'title' => 'Dia do Estudante', 'description' => 'Comemoração do dia dos estudantes.'],
            ['day' => 11, 'month' => 8, 'title' => 'Dia do Garçom', 'description' => 'Comemoração do dia dos garçons.'],
            ['day' => 13, 'month' => 8, 'title' => 'Dia do Economista', 'description' => 'Comemoração do dia dos economistas.'],
            ['day' => 14, 'month' => 8, 'title' => 'Dia do Cardiologista', 'description' => 'Comemoração do dia dos cardiologistas.'],
            ['day' => 15, 'month' => 8, 'title' => 'Dia da Informática', 'description' => 'Comemoração do dia da informática.'],
            ['day' => 16, 'month' => 8, 'title' => 'Dia do Filósofo', 'description' => 'Comemoração do dia dos filósofos.'],
            ['day' => 19, 'month' => 8, 'title' => 'Dia do Artista de Teatro', 'description' => 'Comemoração do dia dos artistas de teatro.'],
            ['day' => 20, 'month' => 8, 'title' => 'Dia dos Maçons', 'description' => 'Comemoração do dia dos maçons.'],
            ['day' => 22, 'month' => 8, 'title' => 'Dia do Folclore', 'description' => 'Comemoração do dia do folclore.'],
            ['day' => 25, 'month' => 8, 'title' => 'Dia do Feirante', 'description' => 'Comemoração do dia dos feirantes.'],
            ['day' => 27, 'month' => 8, 'title' => 'Dia do Corretor de Imóveis', 'description' => 'Comemoração do dia dos corretores de imóveis.'],
            ['day' => 27, 'month' => 8, 'title' => 'Dia do Psicólogo', 'description' => 'Comemoração do dia dos psicólogos.'],
            ['day' => 3, 'month' => 9, 'title' => 'Dia do Biólogo', 'description' => 'Comemoração do dia dos biólogos.'],
            ['day' => 3, 'month' => 9, 'title' => 'Dia do Guarda Civil', 'description' => 'Comemoração do dia dos guardas civis.'],
            ['day' => 5, 'month' => 9, 'title' => 'Dia Oficial da Farmácia', 'description' => 'Comemoração do dia oficial da farmácia.'],
            ['day' => 6, 'month' => 9, 'title' => 'Dia do Alfaiate', 'description' => 'Comemoração do dia dos alfaiates.'],
            ['day' => 9, 'month' => 9, 'title' => 'Dia do Administrador', 'description' => 'Comemoração do dia dos administradores.'],
            ['day' => 9, 'month' => 9, 'title' => 'Dia do Médico Veterinário', 'description' => 'Comemoração do dia dos médicos veterinários.'],
            ['day' => 14, 'month' => 9, 'title' => 'Dia da Cruz', 'description' => 'Comemoração do dia da cruz.'],
            ['day' => 20, 'month' => 9, 'title' => 'Dia do Funcionário Municipal', 'description' => 'Comemoração do dia dos funcionários municipais.'],
            ['day' => 21, 'month' => 9, 'title' => 'Dia da Árvore', 'description' => 'Comemoração do dia da árvore.'],
            ['day' => 22, 'month' => 9, 'title' => 'Dia do Contador', 'description' => 'Comemoração do dia dos contadores.'],
            ['day' => 23, 'month' => 9, 'title' => 'Dia do Soldador', 'description' => 'Comemoração do dia dos soldadores.'],
            ['day' => 25, 'month' => 9, 'title' => 'Dia Nacional do Trânsito', 'description' => 'Comemoração do dia nacional do trânsito.'],
            ['day' => 26, 'month' => 9, 'title' => 'Dia dos Primos', 'description' => 'Comemoração do dia dos primos.'],
            ['day' => 27, 'month' => 9, 'title' => 'Dia de Cosme e Damião', 'description' => 'Comemoração do dia de Cosme e Damião.'],
            ['day' => 28, 'month' => 9, 'title' => 'Dia da Lei do Ventre Livre', 'description' => 'Comemoração do dia da lei do ventre livre.'],
            ['day' => 29, 'month' => 9, 'title' => 'Dia do Anunciante', 'description' => 'Comemoração do dia dos anunciantes.'],
            ['day' => 30, 'month' => 9, 'title' => 'Dia da Secretária', 'description' => 'Comemoração do dia das secretárias.'],
            ['day' => 30, 'month' => 9, 'title' => 'Dia do Jornaleiro', 'description' => 'Comemoração do dia dos jornaleiros.'],
            ['day' => 1, 'month' => 10, 'title' => 'Dia Internacional da Terceira Idade', 'description' => 'Comemoração do dia internacional da terceira idade.'],
            ['day' => 1, 'month' => 10, 'title' => 'Dia do Vendedor', 'description' => 'Comemoração do dia dos vendedores.'],
            ['day' => 3, 'month' => 10, 'title' => 'Dia Mundial do Dentista', 'description' => 'Comemoração do dia mundial dos dentistas.'],
            ['day' => 4, 'month' => 10, 'title' => 'Dia da Natureza', 'description' => 'Comemoração do dia da natureza.'],
            ['day' => 4, 'month' => 10, 'title' => 'Dia do Cão', 'description' => 'Comemoração do dia dos cães.'],
            ['day' => 4, 'month' => 10, 'title' => 'Dia do Barman', 'description' => 'Comemoração do dia dos barmans.'],
            ['day' => 5, 'month' => 10, 'title' => 'Dia da Ave', 'description' => 'Comemoração do dia das aves.'],
            ['day' => 11, 'month' => 10, 'title' => 'Dia do Deficiente Físico', 'description' => 'Comemoração do dia dos deficientes físicos.'],
            ['day' => 12, 'month' => 10, 'title' => 'Dia do Engenheiro Agrônomo', 'description' => 'Comemoração do dia dos engenheiros agrônomos.'],
            ['day' => 15, 'month' => 10, 'title' => 'Dia do Professor', 'description' => 'Comemoração do dia dos professores.'],
            ['day' => 16, 'month' => 10, 'title' => 'Dia Mundial da Alimentação', 'description' => 'Comemoração do dia mundial da alimentação.'],
            ['day' => 17, 'month' => 10, 'title' => 'Dia da Indústria Aeronáutica Brasileira', 'description' => 'Comemoração do dia da indústria aeronáutica brasileira.'],
            ['day' => 18, 'month' => 10, 'title' => 'Dia do Médico', 'description' => 'Comemoração do dia dos médicos.'],
            ['day' => 18, 'month' => 10, 'title' => 'Dia do Securitário', 'description' => 'Comemoração do dia dos securitários.'],
            ['day' => 20, 'month' => 10, 'title' => 'Dia do Arquivista', 'description' => 'Comemoração do dia dos arquivistas.'],
            ['day' => 23, 'month' => 10, 'title' => 'Dia do Aviador', 'description' => 'Comemoração do dia dos aviadores.'],
            ['day' => 24, 'month' => 10, 'title' => 'Dia das Nações Unidas', 'description' => 'Comemoração do dia das Nações Unidas.'],
            ['day' => 25, 'month' => 10, 'title' => 'Dia da Democracia', 'description' => 'Comemoração do dia da democracia.'],
            ['day' => 25, 'month' => 10, 'title' => 'Dia do Sapateiro', 'description' => 'Comemoração do dia dos sapateiros.'],
            ['day' => 27, 'month' => 10, 'title' => 'Dia Mundial de Oração pela Paz', 'description' => 'Comemoração do dia mundial de oração pela paz.'],
            ['day' => 28, 'month' => 10, 'title' => 'Dia do Funcionário Público', 'description' => 'Comemoração do dia dos funcionários públicos.'],
            ['day' => 30, 'month' => 10, 'title' => 'Dia do Comerciário', 'description' => 'Comemoração do dia dos comerciários.'],
            ['day' => 31, 'month' => 10, 'title' => 'Dia das Bruxas', 'description' => 'Comemoração do dia das bruxas (Halloween).'],
            ['day' => 3, 'month' => 11, 'title' => 'Dia do Direito de Voto para Mulheres', 'description' => 'Comemoração do dia do direito de voto para mulheres.'],
            ['day' => 5, 'month' => 11, 'title' => 'Dia do Cinema Brasileiro', 'description' => 'Comemoração do dia do cinema brasileiro.'],
            ['day' => 5, 'month' => 11, 'title' => 'Dia do Radioamador e Técnico Eletrônica', 'description' => 'Comemoração do dia dos radioamadores e técnicos eletrônica.'],
            ['day' => 8, 'month' => 11, 'title' => 'Dia do Radiologista', 'description' => 'Comemoração do dia dos radiologistas.'],
            ['day' => 12, 'month' => 11, 'title' => 'Dia do Diretor de Escola', 'description' => 'Comemoração do dia dos diretores de escola.'],
            ['day' => 17, 'month' => 11, 'title' => 'Dia da Criatividade', 'description' => 'Comemoração do dia da criatividade.'],
            ['day' => 20, 'month' => 11, 'title' => 'Dia do Auditor Interno', 'description' => 'Comemoração do dia dos auditores internos.'],
            ['day' => 22, 'month' => 11, 'title' => 'Dia do Músico', 'description' => 'Comemoração do dia dos músicos.'],
            ['day' => 27, 'month' => 11, 'title' => 'Dia do Técnico de Segurança no Trabalho', 'description' => 'Comemoração do dia dos técnicos de segurança no trabalho.'],
            ['day' => 3, 'month' => 12, 'title' => 'Dia Internacional do Portador de Deficiência', 'description' => 'Comemoração do dia internacional dos portadores de deficiência.'],
            ['day' => 4, 'month' => 12, 'title' => 'Dia do Pedicuro', 'description' => 'Comemoração do dia dos pedicuros.'],
            ['day' => 9, 'month' => 12, 'title' => 'Dia do Fonoaudiólogo', 'description' => 'Comemoração do dia dos fonoaudiólogos.'],
            ['day' => 11, 'month' => 12, 'title' => 'Dia do Arquiteto', 'description' => 'Comemoração do dia dos arquitetos.'],
            ['day' => 11, 'month' => 12, 'title' => 'Dia do Engenheiro', 'description' => 'Comemoração do dia dos engenheiros.'],
            ['day' => 13, 'month' => 12, 'title' => 'Dia do Engenheiro Avaliador e Perito de Engenharia', 'description' => 'Comemoração do dia dos engenheiros avaliadores e peritos de engenharia.'],
            ['day' => 15, 'month' => 12, 'title' => 'Dia do Jardineiro', 'description' => 'Comemoração do dia dos jardineiros.'],
            ['day' => 20, 'month' => 12, 'title' => 'Dia do Mecânico', 'description' => 'Comemoração do dia dos mecânicos.'],
            ['day' => 22, 'month' => 12, 'title' => 'Dia do Atleta', 'description' => 'Comemoração do dia dos atletas.'],


            // Feriados Nacionais Fixos
            ['day' => 1, 'month' => 1, 'title' => 'Ano Novo', 'description' => 'Confraternização Universal'],
            ['day' => 21, 'month' => 4, 'title' => 'Tiradentes', 'description' => 'Homenagem a Joaquim José da Silva Xavier, o Tiradentes'],
            ['day' => 1, 'month' => 5, 'title' => 'Dia do Trabalho', 'description' => 'Celebração do dia do trabalhador'],
            ['day' => 7, 'month' => 9, 'title' => 'Independência do Brasil', 'description' => 'Comemoração da independência do Brasil'],
            ['day' => 12, 'month' => 10, 'title' => 'Nossa Senhora Aparecida', 'description' => 'Padroeira do Brasil'],
            ['day' => 2, 'month' => 11, 'title' => 'Finados', 'description' => 'Homenagem aos mortos'],
            ['day' => 15, 'month' => 11, 'title' => 'Proclamação da República', 'description' => 'Comemoração da Proclamação da República'],
            ['day' => 25, 'month' => 12, 'title' => 'Natal', 'description' => 'Comemoração do nascimento de Jesus Cristo'],

            // Datas móveis para os próximos 20 anos
            ['day' => 13, 'month' => 2, 'year' => 2024, 'title' => 'Carnaval', 'description' => 'Festividade antes da Quaresma, data variável'],
            ['day' => 29, 'month' => 3, 'year' => 2024, 'title' => 'Sexta-feira Santa', 'description' => 'Paixão de Cristo, data variável'],
            ['day' => 31, 'month' => 3, 'year' => 2024, 'title' => 'Páscoa', 'description' => 'Ressurreição de Jesus, data variável'],
            ['day' => 30, 'month' => 5, 'year' => 2024, 'title' => 'Corpus Christi', 'description' => 'Celebração da Eucaristia, data variável'],

            ['day' => 4, 'month' => 3, 'year' => 2025, 'title' => 'Carnaval', 'description' => 'Festividade antes da Quaresma, data variável'],
            ['day' => 18, 'month' => 4, 'year' => 2025, 'title' => 'Sexta-feira Santa', 'description' => 'Paixão de Cristo, data variável'],
            ['day' => 20, 'month' => 4, 'year' => 2025, 'title' => 'Páscoa', 'description' => 'Ressurreição de Jesus, data variável'],
            ['day' => 19, 'month' => 6, 'year' => 2025, 'title' => 'Corpus Christi', 'description' => 'Celebração da Eucaristia, data variável'],

            ['day' => 17, 'month' => 2, 'year' => 2026, 'title' => 'Carnaval', 'description' => 'Festividade antes da Quaresma, data variável'],
            ['day' => 3, 'month' => 4, 'year' => 2026, 'title' => 'Sexta-feira Santa', 'description' => 'Paixão de Cristo, data variável'],
            ['day' => 5, 'month' => 4, 'year' => 2026, 'title' => 'Páscoa', 'description' => 'Ressurreição de Jesus, data variável'],
            ['day' => 4, 'month' => 6, 'year' => 2026, 'title' => 'Corpus Christi', 'description' => 'Celebração da Eucaristia, data variável'],

            ['day' => 9, 'month' => 2, 'year' => 2027, 'title' => 'Carnaval', 'description' => 'Festividade antes da Quaresma, data variável'],
            ['day' => 26, 'month' => 3, 'year' => 2027, 'title' => 'Sexta-feira Santa', 'description' => 'Paixão de Cristo, data variável'],
            ['day' => 28, 'month' => 3, 'year' => 2027, 'title' => 'Páscoa', 'description' => 'Ressurreição de Jesus, data variável'],
            ['day' => 27, 'month' => 5, 'year' => 2027, 'title' => 'Corpus Christi', 'description' => 'Celebração da Eucaristia, data variável'],

            ['day' => 29, 'month' => 2, 'year' => 2028, 'title' => 'Carnaval', 'description' => 'Festividade antes da Quaresma, data variável'],
            ['day' => 14, 'month' => 4, 'year' => 2028, 'title' => 'Sexta-feira Santa', 'description' => 'Paixão de Cristo, data variável'],
            ['day' => 16, 'month' => 4, 'year' => 2028, 'title' => 'Páscoa', 'description' => 'Ressurreição de Jesus, data variável'],
            ['day' => 15, 'month' => 6, 'year' => 2028, 'title' => 'Corpus Christi', 'description' => 'Celebração da Eucaristia, data variável'],

            ['day' => 13, 'month' => 2, 'year' => 2029, 'title' => 'Carnaval', 'description' => 'Festividade antes da Quaresma, data variável'],
            ['day' => 30, 'month' => 3, 'year' => 2029, 'title' => 'Sexta-feira Santa', 'description' => 'Paixão de Cristo, data variável'],
            ['day' => 1, 'month' => 4, 'year' => 2029, 'title' => 'Páscoa', 'description' => 'Ressurreição de Jesus, data variável'],
            ['day' => 31, 'month' => 5, 'year' => 2029, 'title' => 'Corpus Christi', 'description' => 'Celebração da Eucaristia, data variável'],

            ['day' => 5, 'month' => 3, 'year' => 2030, 'title' => 'Carnaval', 'description' => 'Festividade antes da Quaresma, data variável'],
            ['day' => 19, 'month' => 4, 'year' => 2030, 'title' => 'Sexta-feira Santa', 'description' => 'Paixão de Cristo, data variável'],
            ['day' => 21, 'month' => 4, 'year' => 2030, 'title' => 'Páscoa', 'description' => 'Ressurreição de Jesus, data variável'],
            ['day' => 20, 'month' => 6, 'year' => 2030, 'title' => 'Corpus Christi', 'description' => 'Celebração da Eucaristia, data variável'],

            ['day' => 25, 'month' => 2, 'year' => 2031, 'title' => 'Carnaval', 'description' => 'Festividade antes da Quaresma, data variável'],
            ['day' => 11, 'month' => 4, 'year' => 2031, 'title' => 'Sexta-feira Santa', 'description' => 'Paixão de Cristo, data variável'],
            ['day' => 13, 'month' => 4, 'year' => 2031, 'title' => 'Páscoa', 'description' => 'Ressurreição de Jesus, data variável'],
            ['day' => 12, 'month' => 6, 'year' => 2031, 'title' => 'Corpus Christi', 'description' => 'Celebração da Eucaristia, data variável'],

            ['day' => 10, 'month' => 2, 'year' => 2032, 'title' => 'Carnaval', 'description' => 'Festividade antes da Quaresma, data variável'],
            ['day' => 26, 'month' => 3, 'year' => 2032, 'title' => 'Sexta-feira Santa', 'description' => 'Paixão de Cristo, data variável'],
            ['day' => 28, 'month' => 3, 'year' => 2032, 'title' => 'Páscoa', 'description' => 'Ressurreição de Jesus, data variável'],
            ['day' => 27, 'month' => 5, 'year' => 2032, 'title' => 'Corpus Christi', 'description' => 'Celebração da Eucaristia, data variável'],

            ['day' => 1, 'month' => 3, 'year' => 2033, 'title' => 'Carnaval', 'description' => 'Festividade antes da Quaresma, data variável'],
            ['day' => 15, 'month' => 4, 'year' => 2033, 'title' => 'Sexta-feira Santa', 'description' => 'Paixão de Cristo, data variável'],
            ['day' => 17, 'month' => 4, 'year' => 2033, 'title' => 'Páscoa', 'description' => 'Ressurreição de Jesus, data variável'],
            ['day' => 16, 'month' => 6, 'year' => 2033, 'title' => 'Corpus Christi', 'description' => 'Celebração da Eucaristia, data variável'],

            ['day' => 21, 'month' => 2, 'year' => 2034, 'title' => 'Carnaval', 'description' => 'Festividade antes da Quaresma, data variável'],
            ['day' => 7, 'month' => 4, 'year' => 2034, 'title' => 'Sexta-feira Santa', 'description' => 'Paixão de Cristo, data variável'],
            ['day' => 9, 'month' => 4, 'year' => 2034, 'title' => 'Páscoa', 'description' => 'Ressurreição de Jesus, data variável'],
            ['day' => 8, 'month' => 6, 'year' => 2034, 'title' => 'Corpus Christi', 'description' => 'Celebração da Eucaristia, data variável'],

            ['day' => 6, 'month' => 3, 'year' => 2035, 'title' => 'Carnaval', 'description' => 'Festividade antes da Quaresma, data variável'],
            ['day' => 23, 'month' => 3, 'year' => 2035, 'title' => 'Sexta-feira Santa', 'description' => 'Paixão de Cristo, data variável'],
            ['day' => 25, 'month' => 3, 'year' => 2035, 'title' => 'Páscoa', 'description' => 'Ressurreição de Jesus, data variável'],
            ['day' => 24, 'month' => 5, 'year' => 2035, 'title' => 'Corpus Christi', 'description' => 'Celebração da Eucaristia, data variável'],

            ['day' => 26, 'month' => 2, 'year' => 2036, 'title' => 'Carnaval', 'description' => 'Festividade antes da Quaresma, data variável'],
            ['day' => 11, 'month' => 4, 'year' => 2036, 'title' => 'Sexta-feira Santa', 'description' => 'Paixão de Cristo, data variável'],
            ['day' => 13, 'month' => 4, 'year' => 2036, 'title' => 'Páscoa', 'description' => 'Ressurreição de Jesus, data variável'],
            ['day' => 12, 'month' => 6, 'year' => 2036, 'title' => 'Corpus Christi', 'description' => 'Celebração da Eucaristia, data variável'],

            ['day' => 17, 'month' => 2, 'year' => 2037, 'title' => 'Carnaval', 'description' => 'Festividade antes da Quaresma, data variável'],
            ['day' => 27, 'month' => 3, 'year' => 2037, 'title' => 'Sexta-feira Santa', 'description' => 'Paixão de Cristo, data variável'],
            ['day' => 29, 'month' => 3, 'year' => 2037, 'title' => 'Páscoa', 'description' => 'Ressurreição de Jesus, data variável'],
            ['day' => 28, 'month' => 5, 'year' => 2037, 'title' => 'Corpus Christi', 'description' => 'Celebração da Eucaristia, data variável'],

            ['day' => 9, 'month' => 3, 'year' => 2038, 'title' => 'Carnaval', 'description' => 'Festividade antes da Quaresma, data variável'],
            ['day' => 16, 'month' => 4, 'year' => 2038, 'title' => 'Sexta-feira Santa', 'description' => 'Paixão de Cristo, data variável'],
            ['day' => 18, 'month' => 4, 'year' => 2038, 'title' => 'Páscoa', 'description' => 'Ressurreição de Jesus, data variável'],
            ['day' => 17, 'month' => 6, 'year' => 2038, 'title' => 'Corpus Christi', 'description' => 'Celebração da Eucaristia, data variável'],

            ['day' => 22, 'month' => 2, 'year' => 2039, 'title' => 'Carnaval', 'description' => 'Festividade antes da Quaresma, data variável'],
            ['day' => 8, 'month' => 4, 'year' => 2039, 'title' => 'Sexta-feira Santa', 'description' => 'Paixão de Cristo, data variável'],
            ['day' => 10, 'month' => 4, 'year' => 2039, 'title' => 'Páscoa', 'description' => 'Ressurreição de Jesus, data variável'],
            ['day' => 9, 'month' => 6, 'year' => 2039, 'title' => 'Corpus Christi', 'description' => 'Celebração da Eucaristia, data variável'],

            ['day' => 14, 'month' => 2, 'year' => 2040, 'title' => 'Carnaval', 'description' => 'Festividade antes da Quaresma, data variável'],
            ['day' => 30, 'month' => 3, 'year' => 2040, 'title' => 'Sexta-feira Santa', 'description' => 'Paixão de Cristo, data variável'],
            ['day' => 1, 'month' => 4, 'year' => 2040, 'title' => 'Páscoa', 'description' => 'Ressurreição de Jesus, data variável'],
            ['day' => 31, 'month' => 5, 'year' => 2040, 'title' => 'Corpus Christi', 'description' => 'Celebração da Eucaristia, data variável'],

            ['day' => 1, 'month' => 1, 'title' => 'Ano Novo', 'description' => 'Celebração mundial do início do novo ano'],
            ['day' => 27, 'month' => 1, 'title' => 'Dia Internacional em Memória das Vítimas do Holocausto', 'description' => 'Dia em memória das vítimas do Holocausto'],
            ['day' => 4, 'month' => 2, 'title' => 'Dia Mundial do Câncer', 'description' => 'Dia de conscientização sobre o câncer'],
            ['day' => 11, 'month' => 2, 'title' => 'Dia Internacional das Mulheres e Meninas na Ciência', 'description' => 'Celebração das conquistas das mulheres na ciência'],
            ['day' => 21, 'month' => 2, 'title' => 'Dia Internacional da Língua Materna', 'description' => 'Celebração da diversidade linguística e cultural'],
            ['day' => 8, 'month' => 3, 'title' => 'Dia Internacional da Mulher', 'description' => 'Celebração das conquistas sociais, políticas e econômicas das mulheres'],
            ['day' => 20, 'month' => 3, 'title' => 'Dia Internacional da Felicidade', 'description' => 'Dia para promover a felicidade global'],
            ['day' => 21, 'month' => 3, 'title' => 'Dia Internacional para a Eliminação da Discriminação Racial', 'description' => 'Dia para combater a discriminação racial'],
            ['day' => 22, 'month' => 3, 'title' => 'Dia Mundial da Água', 'description' => 'Dia para conscientizar sobre a importância da água'],
            ['day' => 7, 'month' => 4, 'title' => 'Dia Mundial da Saúde', 'description' => 'Dia de conscientização sobre a saúde global'],
            ['day' => 22, 'month' => 4, 'title' => 'Dia da Terra', 'description' => 'Dia para promover a preservação ambiental'],
            ['day' => 1, 'month' => 5, 'title' => 'Dia do Trabalho', 'description' => 'Celebração dos trabalhadores e suas conquistas'],
            ['day' => 3, 'month' => 5, 'title' => 'Dia Mundial da Liberdade de Imprensa', 'description' => 'Celebração da liberdade de imprensa'],
            ['day' => 15, 'month' => 5, 'title' => 'Dia Internacional da Família', 'description' => 'Celebração da importância da família'],
            ['day' => 17, 'month' => 5, 'title' => 'Dia Internacional contra a Homofobia, Transfobia e Bifobia', 'description' => 'Dia para combater a discriminação contra LGBTQ+'],
            ['day' => 31, 'month' => 5, 'title' => 'Dia Mundial sem Tabaco', 'description' => 'Dia para conscientizar sobre os riscos do tabaco'],
            ['day' => 5, 'month' => 6, 'title' => 'Dia Mundial do Meio Ambiente', 'description' => 'Celebração da importância do meio ambiente'],
            ['day' => 20, 'month' => 6, 'title' => 'Dia Mundial do Refugiado', 'description' => 'Dia para conscientizar sobre a situação dos refugiados'],
            ['day' => 21, 'month' => 6, 'title' => 'Dia Internacional do Yoga', 'description' => 'Celebração do yoga e seus benefícios'],
            ['day' => 11, 'month' => 7, 'title' => 'Dia Mundial da População', 'description' => 'Dia para conscientizar sobre questões populacionais'],
            ['day' => 12, 'month' => 8, 'title' => 'Dia Internacional da Juventude', 'description' => 'Celebração das contribuições dos jovens'],
            ['day' => 8, 'month' => 9, 'title' => 'Dia Internacional da Alfabetização', 'description' => 'Dia para promover a alfabetização'],
            ['day' => 21, 'month' => 9, 'title' => 'Dia Internacional da Paz', 'description' => 'Celebração da paz mundial'],
            ['day' => 1, 'month' => 10, 'title' => 'Dia Internacional das Pessoas Idosas', 'description' => 'Celebração da contribuição dos idosos à sociedade'],
            ['day' => 10, 'month' => 10, 'title' => 'Dia Mundial da Saúde Mental', 'description' => 'Dia para conscientizar sobre a saúde mental'],
            ['day' => 16, 'month' => 10, 'title' => 'Dia Mundial da Alimentação', 'description' => 'Dia para promover a segurança alimentar'],
            ['day' => 20, 'month' => 11, 'title' => 'Dia Universal das Crianças', 'description' => 'Celebração dos direitos das crianças'],
            ['day' => 25, 'month' => 11, 'title' => 'Dia Internacional para a Eliminação da Violência contra as Mulheres', 'description' => 'Dia para combater a violência contra as mulheres'],
            ['day' => 1, 'month' => 12, 'title' => 'Dia Mundial de Combate à AIDS', 'description' => 'Dia de conscientização sobre a AIDS'],
            ['day' => 10, 'month' => 12, 'title' => 'Dia dos Direitos Humanos', 'description' => 'Celebração dos direitos humanos'],
            ['day' => 31, 'month' => 12, 'title' => 'Véspera de Ano Novo', 'description' => 'Celebração mundial da véspera de Ano Novo'],

            // Cidades dentro de 150 km de Araraquara
            ['day' => 16, 'month' => 6, 'title' => 'Aniversário de Bariri', 'description' => 'Cidade conhecida pela produção agrícola'],
            ['day' => 19, 'month' => 3, 'title' => 'Aniversário de Barra Bonita', 'description' => 'Famosa pelas eclusas do Rio Tietê'],
            ['day' => 24, 'month' => 6, 'title' => 'Aniversário de Bocaina', 'description' => 'Cidade das festas juninas'],
            ['day' => 19, 'month' => 3, 'title' => 'Aniversário de Boraceia', 'description' => 'Conhecida pela tranquilidade e qualidade de vida'],
            ['day' => 22, 'month' => 8, 'title' => 'Aniversário de Brotas', 'description' => 'Capital do turismo de aventura'],
            ['day' => 4, 'month' => 5, 'title' => 'Aniversário de Dois Córregos', 'description' => 'Terra das paisagens rurais'],
            ['day' => 12, 'month' => 12, 'title' => 'Aniversário de Igaraçu do Tietê', 'description' => 'Cidade banhada pelo Rio Tietê'],
            ['day' => 18, 'month' => 6, 'title' => 'Aniversário de Itaju', 'description' => 'Pequena e acolhedora cidade do interior paulista'],
            ['day' => 4, 'month' => 4, 'title' => 'Aniversário de Itapuí', 'description' => 'Cidade conhecida pelas suas águas'],
            ['day' => 15, 'month' => 8, 'title' => 'Aniversário de Jaú', 'description' => 'Capital do calçado feminino'],
            ['day' => 24, 'month' => 6, 'title' => 'Aniversário de Mineiros do Tietê', 'description' => 'Cidade com forte presença agrícola'],
            ['day' => 15, 'month' => 10, 'title' => 'Aniversário de Torrinha', 'description' => 'Cidade das trilhas e natureza exuberante'],
            ['day' => 22, 'month' => 3, 'title' => 'Aniversário de Américo Brasiliense', 'description' => 'Cidade conhecida pelo desenvolvimento industrial'],
            ['day' => 22, 'month' => 8, 'title' => 'Aniversário de Araraquara', 'description' => 'Comemoração do aniversário de Araraquara, a morada do sol'],
            ['day' => 19, 'month' => 3, 'title' => 'Aniversário de Boa Esperança do Sul', 'description' => 'Cidade tranquila do interior paulista'],
            ['day' => 2, 'month' => 8, 'title' => 'Aniversário de Borborema', 'description' => 'Conhecida pelo seu carnaval'],
            ['day' => 29, 'month' => 11, 'title' => 'Aniversário de Cândido Rodrigues', 'description' => 'Cidade pequena e acolhedora'],
            ['day' => 24, 'month' => 6, 'title' => 'Aniversário de Dobrada', 'description' => 'Terra do trabalho e da prosperidade'],
            ['day' => 19, 'month' => 3, 'title' => 'Aniversário de Gavião Peixoto', 'description' => 'Conhecida pela base aérea'],
            ['day' => 4, 'month' => 8, 'title' => 'Aniversário de Ibitinga', 'description' => 'Capital Nacional do Bordado'],
            ['day' => 20, 'month' => 10, 'title' => 'Aniversário de Itápolis', 'description' => 'Cidade das boas águas'],
            ['day' => 27, 'month' => 8, 'title' => 'Aniversário de Matão', 'description' => 'Conhecida pela indústria suco de laranja'],
            ['day' => 16, 'month' => 8, 'title' => 'Aniversário de Motuca', 'description' => 'Cidade com forte presença agrícola'],
            ['day' => 22, 'month' => 6, 'title' => 'Aniversário de Nova Europa', 'description' => 'Cidade com forte presença italiana'],
            ['day' => 29, 'month' => 12, 'title' => 'Aniversário de Rincão', 'description' => 'Pequena cidade do interior paulista'],
            ['day' => 19, 'month' => 3, 'title' => 'Aniversário de Santa Lúcia', 'description' => 'Cidade tranquila e hospitaleira'],
            ['day' => 13, 'month' => 6, 'title' => 'Aniversário de Tabatinga', 'description' => 'Cidade com forte presença indígena'],
            ['day' => 16, 'month' => 8, 'title' => 'Aniversário de Taquaritinga', 'description' => 'Cidade conhecida pelo carnaval'],
            ['day' => 9, 'month' => 7, 'title' => 'Aniversário de Trabiju', 'description' => 'Pequena e charmosa cidade do interior'],
            ['day' => 5, 'month' => 9, 'title' => 'Aniversário de Descalvado', 'description' => 'Cidade conhecida pelo turismo rural'],
            ['day' => 13, 'month' => 6, 'title' => 'Aniversário de Dourado', 'description' => 'Cidade das cachoeiras e trilhas'],
            ['day' => 24, 'month' => 6, 'title' => 'Aniversário de Ibaté', 'description' => 'Cidade das indústrias e agricultura'],
            ['day' => 10, 'month' => 7, 'title' => 'Aniversário de Itirapina', 'description' => 'Cidade das belezas naturais'],
            ['day' => 6, 'month' => 8, 'title' => 'Aniversário de Pirassununga', 'description' => 'Conhecida pela Academia da Força Aérea'],
            ['day' => 29, 'month' => 7, 'title' => 'Aniversário de Porto Ferreira', 'description' => 'Cidade das cerâmicas e artesanato'],
            ['day' => 5, 'month' => 8, 'title' => 'Aniversário de Ribeirão Bonito', 'description' => 'Cidade das festas tradicionais'],
            ['day' => 22, 'month' => 5, 'title' => 'Aniversário de Santa Rita do Passa Quatro', 'description' => 'Cidade das águas minerais'],
            ['day' => 29, 'month' => 7, 'title' => 'Aniversário de São Carlos', 'description' => 'Cidade universitária e tecnológica'],


            // State Capitals of Brazil
            ['day' => 9, 'month' => 12, 'title' => 'Aniversário de Aracaju', 'description' => 'Capital de Sergipe, conhecida pelas praias e manguezais'],
            ['day' => 21, 'month' => 4, 'title' => 'Aniversário de Brasília', 'description' => 'Capital do Brasil, famosa pela arquitetura moderna'],
            ['day' => 29, 'month' => 6, 'title' => 'Aniversário de Belém', 'description' => 'Capital do Pará, conhecida pelo Círio de Nazaré'],
            ['day' => 12, 'month' => 3, 'title' => 'Aniversário de Belo Horizonte', 'description' => 'Capital de Minas Gerais, conhecida pela Pampulha'],
            ['day' => 8, 'month' => 4, 'title' => 'Aniversário de Boa Vista', 'description' => 'Capital de Roraima, conhecida pela sua modernidade'],
            ['day' => 13, 'month' => 6, 'title' => 'Aniversário de Campo Grande', 'description' => 'Capital do Mato Grosso do Sul, conhecida pela hospitalidade'],
            ['day' => 9, 'month' => 10, 'title' => 'Aniversário de Cuiabá', 'description' => 'Capital do Mato Grosso, famosa pelo calor'],
            ['day' => 25, 'month' => 3, 'title' => 'Aniversário de Curitiba', 'description' => 'Capital do Paraná, conhecida pelas áreas verdes'],
            ['day' => 5, 'month' => 8, 'title' => 'Aniversário de Fortaleza', 'description' => 'Capital do Ceará, famosa pelas praias'],
            ['day' => 23, 'month' => 1, 'title' => 'Aniversário de Florianópolis', 'description' => 'Capital de Santa Catarina, conhecida pela ilha de Santa Catarina'],
            ['day' => 20, 'month' => 9, 'title' => 'Aniversário de Goiânia', 'description' => 'Capital de Goiás, famosa pelo agronegócio'],
            ['day' => 16, 'month' => 11, 'title' => 'Aniversário de João Pessoa', 'description' => 'Capital da Paraíba, conhecida pelo pôr do sol na Praia do Jacaré'],
            ['day' => 7, 'month' => 9, 'title' => 'Aniversário de Macapá', 'description' => 'Capital do Amapá, localizada na linha do Equador'],
            ['day' => 9, 'month' => 8, 'title' => 'Aniversário de Maceió', 'description' => 'Capital de Alagoas, famosa pelas lagoas e praias'],
            ['day' => 24, 'month' => 10, 'title' => 'Aniversário de Manaus', 'description' => 'Capital do Amazonas, porta de entrada para a Amazônia'],
            ['day' => 28, 'month' => 12, 'title' => 'Aniversário de Natal', 'description' => 'Capital do Rio Grande do Norte, conhecida pelas dunas'],
            ['day' => 20, 'month' => 12, 'title' => 'Aniversário de Palmas', 'description' => 'Capital do Tocantins, a cidade mais jovem do Brasil'],
            ['day' => 12, 'month' => 10, 'title' => 'Aniversário de Porto Alegre', 'description' => 'Capital do Rio Grande do Sul, conhecida pelo chimarrão'],
            ['day' => 8, 'month' => 9, 'title' => 'Aniversário de Porto Velho', 'description' => 'Capital de Rondônia, conhecida pela Estrada de Ferro Madeira-Mamoré'],
            ['day' => 4, 'month' => 3, 'title' => 'Aniversário de Recife', 'description' => 'Capital de Pernambuco, famosa pelo frevo e maracatu'],
            ['day' => 2, 'month' => 2, 'title' => 'Aniversário de Rio Branco', 'description' => 'Capital do Acre, conhecida pelo seringueiro Chico Mendes'],
            ['day' => 1, 'month' => 3, 'title' => 'Aniversário de Rio de Janeiro', 'description' => 'Cidade maravilhosa, famosa pelo Cristo Redentor'],
            ['day' => 2, 'month' => 7, 'title' => 'Aniversário de Salvador', 'description' => 'Primeira capital do Brasil, famosa pelo carnaval'],
            ['day' => 15, 'month' => 8, 'title' => 'Aniversário de São Luís', 'description' => 'Capital do Maranhão, famosa pelo reggae'],
            ['day' => 25, 'month' => 1, 'title' => 'Aniversário de São Paulo', 'description' => 'A maior cidade do Brasil, centro econômico do país'],
            ['day' => 13, 'month' => 4, 'title' => 'Aniversário de Teresina', 'description' => 'Capital do Piauí, conhecida pelo calor intenso'],
            ['day' => 8, 'month' => 9, 'title' => 'Aniversário de Vitória', 'description' => 'Capital do Espírito Santo, conhecida pelas praias'],

            // Brazilian cities with population over 200,000
            ['day' => 15, 'month' => 1, 'title' => 'Aniversário de Sorocaba', 'description' => 'Importante polo industrial no interior de São Paulo'],
            ['day' => 27, 'month' => 7, 'title' => 'Aniversário de São José dos Campos', 'description' => 'Cidade do Vale do Paraíba, famosa pela indústria aeronáutica'],
            ['day' => 26, 'month' => 1, 'title' => 'Aniversário de Santos', 'description' => 'Importante cidade portuária no litoral paulista'],
            ['day' => 14, 'month' => 12, 'title' => 'Aniversário de Jundiaí', 'description' => 'Cidade das frutas e vinhos no interior de São Paulo'],
            ['day' => 8, 'month' => 9, 'title' => 'Aniversário de Mogi das Cruzes', 'description' => 'Conhecida pela produção de hortaliças no estado de São Paulo'],
            ['day' => 8, 'month' => 12, 'title' => 'Aniversário de Diadema', 'description' => 'Importante polo industrial da Grande São Paulo'],
            ['day' => 8, 'month' => 12, 'title' => 'Aniversário de Guarulhos', 'description' => 'Cidade do maior aeroporto do Brasil'],
            ['day' => 19, 'month' => 2, 'title' => 'Aniversário de Osasco', 'description' => 'Importante centro econômico e industrial na Grande São Paulo'],
            ['day' => 30, 'month' => 8, 'title' => 'Aniversário de Ribeirão das Neves', 'description' => 'Cidade mineira conhecida pela agricultura'],
            ['day' => 31, 'month' => 8, 'title' => 'Aniversário de Uberlândia', 'description' => 'Polo de logística e agronegócio em Minas Gerais'],

            // Santos
            ['day' => 20, 'month' => 1, 'title' => 'Dia de São Sebastião', 'description' => 'Padroeiro dos atletas, arqueiros e protetor contra a peste'],
            ['day' => 25, 'month' => 1, 'title' => 'Dia de São Paulo Apóstolo', 'description' => 'Padroeiro dos jornalistas, escritores e teólogos'],
            ['day' => 2, 'month' => 2, 'title' => 'Dia de Nossa Senhora das Candeias', 'description' => 'Celebração da apresentação de Jesus no templo'],
            ['day' => 3, 'month' => 2, 'title' => 'Dia de São Brás', 'description' => 'Padroeiro das doenças da garganta'],
            ['day' => 14, 'month' => 2, 'title' => 'Dia de São Valentim', 'description' => 'Padroeiro dos namorados e dos apicultores'],
            ['day' => 19, 'month' => 3, 'title' => 'Dia de São José', 'description' => 'Padroeiro dos trabalhadores e das famílias'],
            ['day' => 23, 'month' => 4, 'title' => 'Dia de São Jorge', 'description' => 'Padroeiro dos escoteiros, soldados e agricultores'],
            ['day' => 1, 'month' => 5, 'title' => 'Dia de São José Operário', 'description' => 'Celebração do trabalho e dos trabalhadores'],
            ['day' => 13, 'month' => 6, 'title' => 'Dia de Santo Antônio', 'description' => 'Padroeiro dos pobres, dos casais e dos objetos perdidos'],
            ['day' => 24, 'month' => 6, 'title' => 'Dia de São João Batista', 'description' => 'Padroeiro dos alfaiates, sapateiros e pregadores'],
            ['day' => 29, 'month' => 6, 'title' => 'Dia de São Pedro e São Paulo', 'description' => 'São Pedro é o padroeiro dos pescadores, São Paulo é o padroeiro dos missionários'],
            ['day' => 16, 'month' => 7, 'title' => 'Dia de Nossa Senhora do Carmo', 'description' => 'Padroeira da Ordem Carmelita e dos motoristas'],
            ['day' => 25, 'month' => 7, 'title' => 'Dia de São Cristóvão', 'description' => 'Padroeiro dos motoristas e viajantes'],
            ['day' => 15, 'month' => 8, 'title' => 'Assunção de Nossa Senhora', 'description' => 'Celebração da Assunção de Maria ao céu'],
            ['day' => 29, 'month' => 8, 'title' => 'Dia do Martírio de São João Batista', 'description' => 'Celebração do martírio de João Batista'],
            ['day' => 8, 'month' => 9, 'title' => 'Natividade de Nossa Senhora', 'description' => 'Celebração do nascimento de Maria'],
            ['day' => 29, 'month' => 9, 'title' => 'Dia de São Miguel, São Gabriel e São Rafael', 'description' => 'Arcanjos, padroeiros dos enfermos, mensageiros e da polícia'],
            ['day' => 4, 'month' => 10, 'title' => 'Dia de São Francisco de Assis', 'description' => 'Padroeiro dos animais, dos ecologistas e dos pobres'],
            ['day' => 12, 'month' => 10, 'title' => 'Dia de Nossa Senhora Aparecida', 'description' => 'Padroeira do Brasil'],
            ['day' => 1, 'month' => 11, 'title' => 'Dia de Todos os Santos', 'description' => 'Celebração de todos os santos e mártires'],
            ['day' => 2, 'month' => 11, 'title' => 'Dia de Finados', 'description' => 'Homenagem a todos os fiéis falecidos'],
            ['day' => 30, 'month' => 11, 'title' => 'Dia de São André', 'description' => 'Padroeiro dos pescadores e da Escócia'],
            ['day' => 4, 'month' => 12, 'title' => 'Dia de Santa Bárbara', 'description' => 'Padroeira dos mineiros e protetora contra tempestades'],
            ['day' => 6, 'month' => 12, 'title' => 'Dia de São Nicolau', 'description' => 'Padroeiro das crianças, dos marinheiros e dos comerciantes'],
            ['day' => 8, 'month' => 12, 'title' => 'Imaculada Conceição', 'description' => 'Celebração da concepção de Maria sem pecado original'],
            ['day' => 13, 'month' => 12, 'title' => 'Dia de Santa Luzia', 'description' => 'Padroeira dos olhos e da visão'],

        ];


        foreach ($commemorativeDates as $index => $commemorativeDate) {

            // procurar pra ver se já não existe
            if (isset($commemorativeDate['year'])){
                $existentDate = $this->commemorativeDatesRepository->findBy(['title'=>$commemorativeDate['title'],'year'=>$commemorativeDate['year'],'month'=>$commemorativeDate['month'],'day'=>$commemorativeDate['day']]);
            } else {
                $existentDate = $this->commemorativeDatesRepository->findBy(['title'=>$commemorativeDate['title'],'month'=>$commemorativeDate['month'],'day'=>$commemorativeDate['day']]);
            }
            if (!$existentDate) {
                $newDate = new CommemorativeDates();
                $newDate->setDay($commemorativeDate['day']);
                $newDate->setMonth($commemorativeDate['month']);
                $newDate->setTitle($commemorativeDate['title']);
                $newDate->setDescription($commemorativeDate['description']);
                if (isset($commemorativeDate['year'])) {
                    $newDate->setYear($commemorativeDate['year']);
                }
                $this->entityManager->persist($newDate);
                $this->entityManager->flush();
                $this->io->writeln('Adicionado <info>'.$commemorativeDate['title'].'</info>');
            } else {
                $this->io->writeln('Já tinha, pulando <note>'.$commemorativeDate['title'].'</note>');
            }
        }

        $this->io->success('Intejado com sucesso');

        return Command::SUCCESS;
    }
}
