<?php

namespace ModifyRecord;

use Illuminate\Support\ServiceProvider;
use ModifyRecord\Commands\MappingGenerate;

class ModifyRecordServiceProvider extends ServiceProvider
{
    public function boot()
    {
        $this->loadMigrationsFrom(__DIR__.'/Migrations/');
        
        $this->loadRoutesFrom(__DIR__.'/Routes/api.php');
        
        $this->publishes([
            __DIR__ . '/Config/modify-record.php' => config_path('modify-record.php')
        ]);
        
        $this->commands(MappingGenerate::class);
    }
    
    public function register()
    {
        $this->app->singleton('record.config', function ($app) {
            return config('modify-record');
        });
        
        $this->app->bind('record', function ($app) {
            return new RecordHandle(app('record.config'));
        });
    }
}
