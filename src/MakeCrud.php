<?php

namespace dreamingincode\makecrud;

use Illuminate\Console\Command;

class MakeCrud extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'crud:make
                            {resourceName} : The name of the resource
                             {--fields=} : must be in the order typeOfField:nameOfField:isSelectThisIsTheListName';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Build out for simple crud';

    protected $path = null;

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $this->setUp();
        $partialForm = $this->createPartialForm($this->option('fields'));
        file_put_contents($this->path . 'partial/_form.blade.php', $partialForm);
        $create = $this->createCreate($this->argument('resourceName'));
        file_put_contents($this->path . 'create.blade.php', $create);
        $index = $this->createIndex();
        file_put_contents($this->path . 'index.blade.php', $index);
        $edit = $this->createEdit($this->argument('resourceName'));
        file_put_contents($this->path . 'edit.blade.php', $edit);
        $this->line('Done!');

    }


    private function checkForExistingFiles()
    {
        if (file_exists($this->path)){
            return true;
        }

        return false;

    }

    private function setPath($resourceName)
    {
        $this->path = base_path() . '/resources/views/' . $resourceName . '/';
    }

    private function setUp()
    {
        $this->setPath($this->argument('resourceName'));
        if ($this->checkForExistingFiles()) {
            $this->error('Some files already exist');
            die();
        }
        mkdir($this->path);
        mkdir($this->path . 'partial');
    }

    private function createPartialForm($fields)
    {
        $partialForm = $this->getBeginningOfForm();
        $fields = $this->separateFields($fields);
        foreach ($fields as $field) {
            $f = explode(':', $field);
            $fieldType = 'make' . ucfirst(array_shift($f));
            $partialForm .= $this->$fieldType($f);
        }

        $partialForm .= $this->getEndOfForm();

        return $partialForm;
    }


    private function makeText($options)
    {
        $stub = file_get_contents(__DIR__ . '/stubs/text-input.stub');
        return str_replace('FIELD_NAME', $options[0], $stub);

    }

    private function makeNumber($options)
    {
        $stub = file_get_contents(__DIR__ . '/stubs/number-input.stub');
        return str_replace('FIELD_NAME', $options[0], $stub);
    }

    private function makeSelect($options)
    {
        $stub = file_get_contents(__DIR__ . '/stubs/select-input.stub');
        $stub = str_replace('FIELD_NAME', $options[0], $stub);
        $stub = str_replace('LIST', $options[1], $stub);

        return $stub;
    }

    private function makeCheckbox($options)
    {

        $stub = file_get_contents(__DIR__ . '/stubs/checkbox.stub');
        return str_replace('FIELD_NAME', $options[0], $stub);
    }

    private function separateFields($fields)
    {
        return preg_split('/,\s?(?![^()]*\))/', $fields);
    }

    private function createCreate($name)
    {
        $stub = file_get_contents(__DIR__ . '/stubs/create.stub');
        return str_replace('MODEL_NAME', $name, $stub);

    }


    private function createIndex()
    {
        return file_get_contents(__DIR__ . '/stubs/index.stub');
    }

    private function createEdit($name)
    {
        $stub = file_get_contents(__DIR__ . '/stubs/edit.stub');
        return str_replace('MODEL_NAME', $name, $stub);
    }


}
