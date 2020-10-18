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
        $this->app->singleton('record.config', function ($app) {
            return config('modify_record');
        });
        
        $this->app->bind('record', function ($app) {
            return new RecordHandle(app('record.config'));
        });
    }
    
    public function provides()
    {
        return ['record'];
    }
}
