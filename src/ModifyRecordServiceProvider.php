<?php

namespace ModifyRecord;

use Illuminate\Contracts\Support\DeferrableProvider;
use Illuminate\Support\ServiceProvider;
use ModifyRecord\Commands\MappingGenerate;

class ModifyRecordServiceProvider extends ServiceProvider implements DeferrableProvider
{
    public function boot()
    {
        $this->loadMigrationsFrom(__DIR__.'/Migrations/');
        
        $this->loadRoutesFrom(__DIR__.'/Routes/api.php');
        
        $this->publishes([
            __DIR__.'/Config/modify_record.php' => config_path('modify_record.php')
        ]);
        
        $this->commands(MappingGenerate::class);
    }
    
    public function register()
    {
        $this->app->bind('record', function ($app) {
            return new RecordHandle(config('modify_record'));
        });
    }
    
    public function provides()
    {
        return ['record'];
    }
}
