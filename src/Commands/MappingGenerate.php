<?php


namespace ModifyRecord\Commands;


use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;

class MappingGenerate extends Command
{
    protected $signature = 'make:mapping {name : The name of the Mapping}
        {--path= : The location where the mapping file should be created}';
    
    protected $description = '自动生成模型映射类';
    
    protected $files;
    
    public function __construct(Filesystem $files)
    {
        $this->files = $files;
        
        parent::__construct();
    }
    
    public function handle()
    {
        $name = trim($this->input->getArgument('name'));
    
        try {
            $file = $this->create($name);
    
            $this->line("<info>Created Mapping:</info> {$file}");
        } catch (\Exception $exception) {
            $this->error($exception->getMessage());
        }
    }
    
    /**
     * 生成mapping文件
     * @param $name
     * @return string
     * @throws \Exception
     */
    public function create($name)
    {
        $stub = $this->getStub();
    
        $path = $this->getMappingPath($name);
        if ($this->files->exists($path)) {
            throw new \Exception($path. '已经存在');
        }
        
        $this->files->put($path, $this->populateStub($name, $stub));
        
        return $path;
    }
    
    public function getMappingPath($path)
    {
        $mappingPath = $this->laravel->path('Mappings');
        
        if (! $this->files->exists($mappingPath)) {
            $this->files->makeDirectory($mappingPath);
        }
        
        return $mappingPath.'/'.$path.'.php';
    }
    
    public function getStub()
    {
        return $this->files->get($this->stubPath().'/mapping.stub');
    }
    
    public function stubPath()
    {
        return __DIR__.'/stubs';
    }
    
    public function populateStub($name, $stub)
    {
        $stub = str_replace(
            ['DummyClass', '{{ class }}', '{{class}}'],
            $name, $stub
        );
        
        return $stub;
    }
}
