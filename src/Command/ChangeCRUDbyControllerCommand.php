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


#[AsCommand(
    name: 'app:change-crud-by-controller',
    description: 'Checks for CRUD Twig files in a specified directory and updates them if necessary.',
)]
class ChangeCRUDbyControllerCommand extends Command
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
    public $fieldsHuman;
    public function __construct()
    {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->addArgument('controllerPath', InputArgument::REQUIRED, 'The path to Controller')
            ->addArgument('humanName', InputArgument::REQUIRED, 'The human name of the CRUD');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $this->io = new SymfonyStyle($input, $output);
        $this->controllerPath = $input->getArgument('controllerPath');

        $this->humanName = $input->getArgument('humanName');
        $this->humanName = str_replace('-',' ',$this->humanName );
        $this->humanName = str_replace('_',' ',$this->humanName );

        if ($this->humanName == ""){
            // Ask the user for the input
            $this->humanName = $this->io->askQuestion('Informe o nome humano no singular');
        }

        $filesystem = new Filesystem();

        // Obtém o diretório de trabalho atual
        $currentPath = getcwd();
        $path = $currentPath.'/'.$this->controllerPath;

        // Exibe o diretório de trabalho atual
        $this->io->writeln('Current working directory: ' . $currentPath);
        $this->io->writeln('path: ' . $this->controllerPath);

        if (!$filesystem->exists($this->controllerPath)) {
            $this->io->error("The specified path does not exist.");
            return Command::FAILURE;
        }

        $this->detectDataFromController($input, $output);

        $requiredFiles = [
            'index.html.twig',
            'edit.html.twig',
            'show.html.twig',
            '_delete_form.html.twig'
        ];

        foreach ($requiredFiles as $file) {
            $fullPath = $this->templatePath . DIRECTORY_SEPARATOR . $file;
            if (!$filesystem->exists($fullPath)) {
                $this->io->error("Required file missing: {$file}");
                return Command::FAILURE;
            }
        }

        if ($this->humanName == ""){
            $this->humanName = $this->entityName;
        }


        $this->changeDelete();
        $this->changeForm($path);
        $this->changeEdit();
        $this->changeIndex();
        $this->changeNew();

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


        foreach ($this->fields as $index => $field) {
            $helper = $this->getHelper('question');
            $question = new Question('Por favor, digite o home humano para o campo '.$index.'- '.$field.': ');
            $this->fieldsHuman[$index] = $helper->ask($input, $output, $question);
            $output->writeln('Você digitou: ' . $this->fieldsHuman[$index]);
        }

        dump($this->fieldsHuman);


    }

    public function changeDelete(){

        // Update _delete_form.html.twig
        $deleteFormPath = $this->templatePath . DIRECTORY_SEPARATOR . '_delete_form.html.twig';
        $content = file_get_contents($deleteFormPath);
        $newContent = str_replace(
            "Are you sure you want to delete this item?",
            "Quer mesmo apagar este item?",
            $content
        );
        $newContent = str_replace(
            '<button class="btn">',
            '<button class="btn btn-danger text-white ms-3 mt-3">',
            $newContent
        );

        file_put_contents($deleteFormPath, $newContent);
        $this->io->success("The file _delete_form.html.twig has been updated successfully.");

    }

    public function changeForm()
    {
        $filesystem = new Filesystem();


        // Updating the _form.html.twig file
        $formTwigPath = $this->templatePath . DIRECTORY_SEPARATOR . '_form.html.twig';
        $newContent = "{{ form_start(form) }}\n<div class=\"row\">\n<div class=\"text-danger\">\n        {{ form_errors(form) }}\n    </div>\n";



        foreach ($this->fields as $index => $field) {
            $newContent .= <<<FIELD
    <div class="mb-3 col-12">
        <label for="{{ form.$field.vars.id }}" class="form-label">{$this->formatLabel($this->fieldsHuman[$index])}</label>
        {{ form_widget(form.$field, {'attr':{'class':'form-control'}}) }}
    </div>

FIELD;
        }

        $newContent .= "</div>\n<button class=\"btn btn-info text-white float-start mt-3\">Salvar</button>\n{{ form_end(form) }}";

        file_put_contents($formTwigPath, $newContent);
        $this->io->success("_form.html.twig has been updated successfully.");

        return Command::SUCCESS;
    }

    public function changeEdit(){
        $filesystem = new Filesystem();
        $editTwigPath = $this->templatePath . DIRECTORY_SEPARATOR . 'edit.html.twig';
        if (!$filesystem->exists($editTwigPath)) {
            $this->io->error("edit.html.twig file does not exist in the specified directory.");
            return Command::FAILURE;
        }

        // Reading and modifying the content of edit.html.twig
        $content = file_get_contents($editTwigPath);
        $newContent = str_replace(
            ['Edit', 'back to list', $this->entityName],
            ['Editar', 'Voltar', $this->humanName],
            $content
        );

        $newContent = preg_replace(
            '/<h1>.*?<\/h1>/',
            '<h1>Editar '.$this->humanName.'</h1>',
            $newContent
        );

        $newContent = str_replace(
            '">Voltar</a>',
            '" class="btn btn-secondary float-start ms-3 mt-3 text-white">Voltar</a>',
            $newContent
        );



        file_put_contents($editTwigPath, $newContent);
        $this->io->success("edit.html.twig has been updated successfully.");
    }

    public function changeIndex(){
        $indexTwigPath = $this->templatePath . DIRECTORY_SEPARATOR . 'index.html.twig';
        $filesystem = new Filesystem();
        if (!$filesystem->exists($indexTwigPath)) {
            $this->io->error("index.html.twig file does not exist in the specified directory.");
            return Command::FAILURE;
        }

        // Reading and modifying the content of index.html.twig
        $content = file_get_contents($indexTwigPath);

        $pathToNew = $this->extractTextByOccurrence($content, "<a href=\"{{ path('","') }}\">Create new</a>",3);



        $newContent = str_replace(
            ['index',    'back to list', '>edit<',                                      'no records found', '<th>actions</th>'],
            ['Listatem', 'Voltar',     ' class="btn btn-primary float-end" >Editar<', 'Sem registros',    '<th></th>'],
            $content
        );
        $newContent = preg_replace(
            '/<h1>.*?<\/h1>/',
            '',
            $newContent
        );

        // Adding DataTables script and changing table structure
        $newContent = str_replace(
            '{% block body %}',
            "
{% block javascripts %}
    <script src=\"https://code.jquery.com/jquery-3.7.1.min.js\" integrity=\"sha256-/JqT3SQfawRcv/BIHPThkBvs0OEvtFFmqPF/lYI/Cxo=\" crossorigin=\"anonymous\"></script>
    <script src=\"https://cdn.datatables.net/2.0.0/js/dataTables.js\"></script>
    <script>
        $(document).ready(function () {
            $('#tabela').DataTable({
                \"lengthMenu\": [[ 10, 25, 50, 100, -1], [ 10, 25, 50, 100, \"Todos\"]],
                \"language\": {
                    \"lengthMenu\": \"Mostrando _MENU_ registros por página\",
                    \"zeroRecords\": \"Nada encontrado\",
                    \"info\": \"Mostrando página _PAGE_ de _PAGES_\",
                    \"infoEmpty\": \"Nenhum registro disponível\",
                    \"infoFiltered\": \"(filtrado de _MAX_ registros no total)\",
                    \"search\": \" Pesquisar\",
                    \"paginate\": {
                        \"first\": \"Primeira\",
                        \"last\": \"Última\",
                        \"next\": \"Próxima\",
                        \"previous\": \"Anterior\"
                    },
                }
            });
        });
    </script>
{% endblock %}

{% block body %}
",
            $newContent
        );

        $newContent = str_replace(
            '{% block body %}',
            '{% block body %}
    <link rel="stylesheet" href="https://cdn.datatables.net/2.0.0/css/dataTables.dataTables.css" />
    <script src="https://cdn.datatables.net/2.0.0/js/dataTables.js"></script>  
    

    <div class="row text-end">
        <div class="col">
            <a class="mb-3 btn btn-success text-white" href="{{ path(\''.$pathToNew.'\') }}">Novo</a>
        </div>
    </div>           
            ',
            $newContent
        );


        $newContent = str_replace('<table class="table">','
    <div class="card mb-5">
        <h5 class="card-header">'.$this->humanName.'s</h5>
        <div class="card-body">
            <table class="table" id="tabela">        
        ',$newContent);

        $newContent = str_replace(' </table>','
            </table>
        </div>
    </div>  
        ',$newContent);

        $newContent = preg_replace(
            '/<h1>.*?<\/h1>/',
            '<h1>'.$this->humanName.'</h1>',
            $newContent
        );

        $newContent = $this->removeLineContainingSubstring($newContent, '_show');
        $newContent = $this->removeLineContainingSubstring($newContent, '>Create new<');
        $newContent = $this->removeLineContainingSubstring($newContent, '<th>Id</th>');
        $newContent = $this->removeLineContainingSubstring($newContent, '.id }}</td>');

        // trocar os nomes dos campos
        foreach ($this->fields as $index => $field) {
            $oldName = $this->mb_ucfirst($field);
            $newName = $this->mb_ucfirst($this->fieldsHuman[$index]);
            $newContent = str_replace('<th>'.$oldName.'</th>','<th>'.$newName.'</th>',$newContent);
        }

        file_put_contents($indexTwigPath, $newContent);
        $this->io->success("index.html.twig has been updated successfully.");

        return Command::SUCCESS;

    }

    public function changeNew(){
        $filesystem = new Filesystem();
        $newTwigPath = $this->templatePath . DIRECTORY_SEPARATOR . 'new.html.twig';
        if (!$filesystem->exists($newTwigPath)) {
            $this->io->error("new.html.twig file does not exist in the specified directory.");
            return Command::FAILURE;
        }

        // Reading and modifying the content of new.html.twig
        $content = file_get_contents($newTwigPath);
        $newContent = str_replace(
            ['New', 'Create new', 'back to list'],
            ['Novo', 'Criar novo', 'Voltar'],
            $content
        );
        $newContent = str_replace(
            '">Voltar</a>',
            '" class="btn btn-secondary float-start ms-3 mt-3 text-white">Voltar</a>',
            $newContent
        );

        $newContent = preg_replace(
            '/<h1>.*?<\/h1>/',
            '<h1>Criar novo '.$this->humanName.'</h1>',
            $newContent
        );



        file_put_contents($newTwigPath, $newContent);
        $this->io->success("new.html.twig has been updated successfully.");

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

    public function detectEntityFromPath(string $path): string {
        // Extrai a última parte após a última barra
        $lastPart = basename($path);

        // Converte a string para CamelCase
        $camelCase = str_replace(' ', '', ucwords(str_replace('_', ' ', $lastPart)));

        return $camelCase;
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


}

