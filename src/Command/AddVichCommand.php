<?php

namespace App\Command;

use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\Question;
use Symfony\Component\Console\Style\SymfonyStyle;

use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\File\File;
use Vich\UploaderBundle\Form\Type\VichFileType;


#[AsCommand(
    name: 'app:add-vich',
    description: 'Checks for CRUD Twig files in a specified directory and updates them if necessary.',
)]
class AddVichCommand extends Command
{
    public $entityName;
    public $entityFile;
    public $formName;
    public $formFile;
    public $controllerPath;
    public $templatePath;
    public $humanName;
    public $io;
    public $fields;
    public $fieldId;
    public $fieldsHuman;

    public $oldFieldName;
    public $newFieldName;
    public $maapingName;


    public function __construct()
    {
        parent::__construct();
    }

    protected function configure(): void
    {

    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $this->io = new SymfonyStyle($input, $output);

        //$this->detectDataFromController($input, $output);

        $this->verifyVich();

        $files = $this->getFilesFromDirectory('src/Controller');
        dump($files);

        $helper = $this->getHelper('question');
        $question = new Question('Por favor, informa o número do controller: ');
        $controllerPathId = $helper->ask($input, $output, $question);
        $output->writeln('Você digitou: ' . $controllerPathId);
        $output->writeln('Abrindo o Controller: ' . $files[$controllerPathId]);
        $this->controllerPath = $files[$controllerPathId];

        $this->detectDataFromController($input, $output);

        dump($this->fields);

        $helper = $this->getHelper('question');
        $question = new Question('Por favor, informa o número do controller: ');
        $this->fieldId = $helper->ask($input, $output, $question);
        $output->writeln('Você digitou: ' . $this->fieldId);
        $output->writeln('Campo: ' . $this->fields[$this->fieldId]);

        //Adicionar campo novo
        $this->oldFieldName = $this->fields[$this->fieldId];
        $this->newFieldName = $this->fields[$this->fieldId].'File';
        $this->maapingName = $this->fields[$this->fieldId].'Image';


        $this->changeForm();
        $this->changeEntity();
        $this->change_Form();
        $this->changeIndex();

        $output->writeln('Agora, você precisa rodar...');
        $output->writeln('<info>symfony console make:migration</info>');


        return Command::SUCCESS;
    }
    public function detectDataFromController($input, $output)
    {
        $filesystem = new Filesystem();
        if (!$filesystem->exists($this->controllerPath)) {
            $this->io->error("O Controller não foi encontrado no Path");
            $this->io->writeln($this->controllerPath);
            return Command::FAILURE;
        }

        $content = file_get_contents($this->controllerPath);

//        dump($content);

        // Entity
        $this->entityName = $this->extractTextByOccurrence($content,' = new ', '$form = ',1);
        $this->entityName = str_replace('();','',$this->entityName);
        $this->entityName = str_replace("\n",'',$this->entityName);
        $this->entityName = trim($this->entityName);
        $this->entityFile = 'src/Entity/'.$this->entityName.'.php';
        dump($this->entityName);
        dump($this->entityFile);

        if (!$filesystem->exists($this->entityFile)) {
            $this->io->error("A entity não foi encontrado no Path");
            $this->io->writeln($this->entityFile);
            return Command::FAILURE;
        }

        // Form
        $this->formName = $this->extractTextByOccurrence($content,'$form = $this->createForm(', '::class,',1);
        $this->formFile = 'src/Form/'.$this->formName.'.php';
        dump($this->formName);
        dump($this->formFile);

        if (!$filesystem->exists($this->formFile)) {
            $this->io->error("O Form não foi encontrado no Path");
            $this->io->writeln($this->formFile);
            return Command::FAILURE;
        }

        // templates
        $this->templatePath = $this->extractTextByOccurrence($content,'return $this->render(\'', '\', [',1);
        $this->templatePath = str_replace('/index.html.twig','',$this->templatePath);
        $this->templatePath = 'templates/'.$this->templatePath;

        dump($this->templatePath);



        // Extracting fields from FormType
        $formTypeContent = file_get_contents($this->formFile);
        if (!preg_match_all('/->add\(\'(\w+)\'/', $formTypeContent, $matches)) {
            $this->io->error("No fields found in FormType.");
            return Command::FAILURE;
        }

        $this->fields = $matches[1];
        dump($this->fields);





    }

    public function verifyVich()
    {
        $content = file_get_contents('config/bundles.php');
        $pos = strrpos($content, 'Vich\UploaderBundle');
        if ($pos === false) {
            $this->io->error("O Vich\UploaderBundle não foi encontrado no Bundle.php");
            $this->io->writeln("Rode o:");
            $this->io->writeln("composer require vich/uploader-bundle");
            exit;
        } else {
            $this->io->writeln("O Vich\UploaderBundle foi <info>encontrado</info>");
        }

    }

    public function changeForm()
    {
        $filesystem = new Filesystem();

        // Abrindo
        $content = file_get_contents($this->formFile);
        $this->io->writeln("abrindo o ".$this->formFile);

        // Procurar nas uses.
        $pos = strrpos($content, 'use Vich\UploaderBundle\Form\Type\VichFileType;');
        if ($pos === false) {
            $this->io->writeln("precisa adicionar a USE do Vich\UploaderBundle\Form\Type\VichFileType;");
            $nameSpaceLine = $this->findStringInTextLine($content,'namespace App\Form;');
            $content = $this->addLineBelowTarget($content,$nameSpaceLine+2,'use Vich\UploaderBundle\Form\Type\VichFileType;');
            $this->io->writeln("USE adicionado");
        } else {
            $this->io->writeln("Ja existia");
            $this->io->writeln("use Vich\UploaderBundle\Form\Type\VichFileType;");
        }


        $this->oldFieldName = $this->fields[$this->fieldId];
        $this->newFieldName = $this->fields[$this->fieldId].'File';
        $content = str_replace('->add(\''.$this->oldFieldName.'\')','->add(\''.$this->newFieldName.'\',VichFileType::class,[
                \'required\' => false,
                \'allow_delete\' => false,
                \'asset_helper\' => false,
                \'download_uri\' => false,

            ])',$content);

        file_put_contents($this->formFile, $content);
        $this->io->success($this->formFile." - Atualizado ");

        return Command::SUCCESS;
    }
    public function change_Form()
    {
        // Abrindo
        $content = file_get_contents($this->templatePath.'/_form.html.twig');
        $this->io->writeln("abrindo o ".$this->templatePath.'/_form.html.twig');
        $content = str_replace('form.'.$this->oldFieldName,'form.'.$this->newFieldName,$content);
        $search = 'form_widget(form.'.$this->oldFieldName;
        dump($search);
        $line = $this->findStringInTextLine($content,$search);
        if ($line == 0){
            $this->io->writeln("Não achei ");
            exit;
        }
        $this->io->writeln("Linha: ".$line);
        $this->removeLineContainingSubstring($content,$search);
$newPart = "
        {{ form_widget(form.".$this->newFieldName.") }}
        {% if ". $this->mb_dcfirst( $this->entityName).".".$this->newFieldName." is not empty %}
            <img class=\"mt-3\" src=\"{{ vich_uploader_asset(".$this->mb_dcfirst( $this->entityName).",'".$this->newFieldName."') }}\" width=\"150\" />
        {% endif %}
";
        $content = $this->addLineBelowTarget($content,$line,$newPart);

        // remover de novo
        $content = str_replace('{{ form_widget(form.'.$this->newFieldName.') }}','',$content);


        file_put_contents($this->templatePath.'/_form.html.twig', $content);
        $this->io->success($this->templatePath.'/_form.html.twig'." - Atualizado ");

        return Command::SUCCESS;
    }
    public function changeEntity()
    {
        $filesystem = new Filesystem();

        // Abrindo
        $content = file_get_contents($this->entityFile);
        $this->io->writeln("abrindo o ".$this->entityFile);

        // Procurar nas uses.
        $pos = strrpos($content, 'use Vich\UploaderBundle\Mapping\Annotation as Vich;');
        if ($pos === false) {
            $this->io->writeln("precisa adicionar a use Vich\UploaderBundle\Mapping\Annotation as Vich;");
            $nameSpaceLine = $this->findStringInTextLine($content,'use Doctrine\ORM\Mapping as ORM;');
            $content = $this->addLineBelowTarget($content,$nameSpaceLine+1,'use Vich\UploaderBundle\Mapping\Annotation as Vich;');
            $this->io->writeln("USE adicionado");
        } else {
            $this->io->writeln("Ja existia");
            $this->io->writeln("use Vich\UploaderBundle\Mapping\Annotation as Vich;");
        }

        $pos = strrpos($content, 'use Symfony\Component\HttpFoundation\File\File;');
        if ($pos === false) {
            $this->io->writeln("precisa adicionar a use Symfony\Component\HttpFoundation\File\File;");
            $nameSpaceLine = $this->findStringInTextLine($content,'use Doctrine\ORM\Mapping as ORM;');
            $content = $this->addLineBelowTarget($content,$nameSpaceLine+1,'use Symfony\Component\HttpFoundation\File\File;');
            $this->io->writeln("USE adicionado");
        } else {
            $this->io->writeln("Ja existia");
            $this->io->writeln("use Symfony\Component\HttpFoundation\File\File;");
        }


        // Procurar nas #[Vich\Uploadable].
        $pos = strrpos($content, '#[Vich\Uploadable]');
        if ($pos === false) {
            $this->io->writeln("precisa adicionar #[Vich\Uploadable]");
            $nameSpaceLine = $this->findStringInTextLine($content,'class '.$this->entityName);
            $content = $this->addLineBelowTarget($content,$nameSpaceLine-1,'#[Vich\Uploadable]');
            $this->io->writeln("adicionado");
        } else {
            $this->io->writeln("#[Vich\Uploadable]");
            $this->io->writeln("Ja existia");
        }



        $pos = strrpos($content, $this->newFieldName);
        if ($pos === false) {
            $this->io->writeln("precisa adicionar novo campo ".$this->newFieldName);
            dump(' $'.$this->oldFieldName.' = ');
            $nameSpaceLine = $this->findStringInTextLine($content,' $'.$this->oldFieldName.' = ');
            $this->io->writeln("linha ".$nameSpaceLine);
            $content = $this->addLineBelowTarget($content,$nameSpaceLine+1,'    #[Vich\UploadableField(mapping: \''.$this->maapingName.'\',fileNameProperty: \''.$this->oldFieldName.'\')]
    private ?File $'.$this->newFieldName.' = null;

    #[ORM\Column(nullable: true)]
    private ?\DateTimeImmutable $imgUpdatedAt = null;

');
            $this->io->writeln("adicionado");
        } else {
            $this->io->writeln("#[Vich\Uploadable]");
            $this->io->writeln("Ja existia");
        }

        // verificar se adiciona os Getters and Setters
        $searchFor = 'public function get'.$this->mb_ucfirst($this->newFieldName);
        $pos = strrpos($content, $searchFor);
        if ($pos === false) {
            $geterAndSeters='
    public function get'.$this->newFieldName.'(): ?File
    {
        return $this->'.$this->newFieldName.';
    }

    public function set'.$this->newFieldName.'(?File $'.$this->newFieldName.' = null):void
    {
        $this->'.$this->newFieldName.' = $'.$this->newFieldName.';
        if (null !== $'.$this->newFieldName.'){
            $this->imgUpdatedAt = new \DateTimeImmutable();
        }
    }

    public function getImgUpdatedAt(): ?\DateTimeImmutable
    {
        return $this->imgUpdatedAt;
    }

    public function setImgUpdatedAt(?\DateTimeImmutable $imgUpdatedAt): void
    {
        $this->imgUpdatedAt = $imgUpdatedAt;
    }
';

            $this->io->writeln("Adicionando os Getters and Setters ");
            $searchFor = 'public function set'.$this->mb_ucfirst($this->oldFieldName);
            $nameSpaceLine = $this->findStringInTextLine($content,$searchFor);
            dump($searchFor);
            $this->io->writeln("linha ".$nameSpaceLine);
            $content = $this->addLineBelowTarget($content,$nameSpaceLine+6,$geterAndSeters);
            $this->io->writeln("adicionado");
        } else {
            $this->io->writeln("Getters and Setters já existiam");
        }

        file_put_contents($this->entityFile, $content);
        $this->io->success($this->entityFile." Atualizado com sucesso");







        // Atualizar o config/packages/vich_uploader.yaml
        $vickFile = "config/packages/vich_uploader.yaml";

        if (!$filesystem->exists($vickFile)) {
            $this->io->error("O arquivo não foi encontrado");
            $this->io->error($vickFile);
            return Command::FAILURE;
        }

        $content = file_get_contents($vickFile);
        $this->io->writeln("abrindo o ".$vickFile);
        $newPart = '
        '.$this->maapingName.':
            uri_prefix: /images/'.$this->maapingName.'
            upload_destination: \'%kernel.project_dir%/public/images/'.$this->maapingName.'\'
            namer: Vich\UploaderBundle\Naming\SmartUniqueNamer
            inject_on_load: true
    
    ';
        $pos = strrpos($content, $this->maapingName.':');
        if ($pos === false) {
            $this->io->writeln("precisa adicionar o mapa");
            $content.=$newPart;
        } else {
            $this->io->writeln("O mapa em ".$vickFile);
            $this->io->writeln("Ja existia");
        }

        $content = str_replace('    #mappings:','    mappings:',$content);
        file_put_contents($vickFile, $content);
        $this->io->success($vickFile." Atualizado com sucesso");


        return Command::SUCCESS;
    }
    public function changeIndex()
    {
        // Abrindo
        $content = file_get_contents($this->templatePath.'/index.html.twig');
        $this->io->writeln("abrindo o ".$this->templatePath.'/index.html.twig');
        //dump($content);

$newPart = '
                        <td>
                            {% if '.$this->mb_dcfirst($this->entityName).'.'.$this->newFieldName.' is not empty %}
                            <img src="{{ vich_uploader_asset('.$this->mb_dcfirst($this->entityName).',\''.$this->newFieldName.'\') }}" width="75">
                            {% endif %}
                        </td>
';

        $search = '<td>{{ '.$this->mb_dcfirst($this->entityName).'.'.$this->oldFieldName.' }}</td>';
        dump($search);
        $content = str_replace($search,$newPart,$content);


        file_put_contents($this->templatePath.'/index.html.twig', $content);
        $this->io->success($this->templatePath.'/index.html.twig'." - Atualizado ");

        return Command::SUCCESS;
    }
    private function formatLabel(string $fieldName): string
    {
        return ucwords(str_replace('_', ' ', $fieldName));
    }
    // Função para converter strings para CamelCase
    public function toCamelCase($filename) {
        $result = preg_replace_callback('/(?:^|_)([a-z])/', function ($match) {
            return strtoupper($match[1]);
        }, $filename);

        return $result;
    }


    function extractTextByOccurrence($fullString, $startString, $endString, $occurrence) {
        // Posição inicial para busca
        $offset = 0;

        // Encontra a n-ésima ocorrência de $startString
        for ($i = 0; $i < $occurrence; $i++) {
            $startPos = strpos($fullString, $startString, $offset);
            // Se não encontrar a string, retorna falso
            if ($startPos === false) {
                return false;
            }

            // Ajusta o offset para a próxima busca ser depois desta ocorrência
            $offset = $startPos + strlen($startString);
        }

        $part2 = substr($fullString,$offset);


        // Encontra a primeira ocorrência de $endString após a última $startString encontrada
        $endPos = strpos($part2, $endString);

        if ($endPos === false) {
            return false;
        }

        // Calcula o comprimento do texto a ser extraído
        $length = $endPos - $offset;

        // Extrai e retorna o conteúdo
        return substr($part2, 0, $endPos);
    }

    function removeLineContainingSubstring($text, $substring) {
        // Divide o texto em um array de linhas
        $lines = explode("\n", $text);

        // Itera sobre as linhas para encontrar a substring
        foreach ($lines as $index => $line) {
            if (strpos($line, $substring) !== false) {
                // Remove a linha se a substring for encontrada
                unset($lines[$index]);
                break; // Remove apenas a primeira ocorrência da substring
            }
        }

        // Junta o array de volta em um texto, sem a linha removida
        return implode("\n", $lines);
    }

    function mb_ucfirst($string, $encoding = 'UTF-8')
    {
        $firstChar = mb_substr($string, 0, 1, $encoding);
        $rest = mb_substr($string, 1, null, $encoding);
        return mb_strtoupper($firstChar, $encoding) . $rest;
    }

    function mb_dcfirst($string, $encoding = 'UTF-8')
    {
        $firstChar = mb_substr($string, 0, 1, $encoding);
        $rest = mb_substr($string, 1, null, $encoding);
        return mb_strtolower($firstChar, $encoding) . $rest;
    }

    public function getFilesFromDirectory(string $directory): array
    {
        $files = [];

        // Cria um iterador recursivo para percorrer o diretório
        $directoryIterator = new \RecursiveDirectoryIterator($directory, \RecursiveDirectoryIterator::SKIP_DOTS);
        $iterator = new \RecursiveIteratorIterator($directoryIterator, \RecursiveIteratorIterator::LEAVES_ONLY);

        foreach ($iterator as $file) {
            if ($file->isFile()) {
                $files[] = $file->getRealPath();
            }
        }

        return $files;
    }

    function findStringInTextLine(string $text, string $searchString): ?int
    {
        // Quebra o texto em um array de linhas
        $lines = explode("\n", $text);

        // Percorre cada linha
        foreach ($lines as $lineNumber => $line) {
            // Verifica se a string está presente na linha atual
            //dump($line,$searchString);
            if (strpos($line, $searchString) !== false) {
                // Retorna o número da linha (adiciona 1 para ser 1-indexado)
                return $lineNumber + 1;
            }
        }

        // Se a string não foi encontrada, retorna null
        return null;
    }
    function addLineBelowTarget(string $text, int $targetLine, string $newLine): string
    {
        // Divide o texto em um array de linhas
        $lines = explode("\n", $text);

        // Verifica se a linha-alvo existe no texto
        if ($targetLine < 1 || $targetLine > count($lines)) {
            throw new InvalidArgumentException("O número da linha-alvo é inválido.");
        }

        // Adiciona a nova linha abaixo da linha-alvo
        array_splice($lines, $targetLine, 0, $newLine);

        // Junta as linhas de volta em uma única string
        return implode("\n", $lines);
    }

}

