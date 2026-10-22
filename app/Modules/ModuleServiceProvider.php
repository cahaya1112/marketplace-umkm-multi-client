<?php
namespace App\Modules;

use Illuminate\Support\ServiceProvider;
use Livewire\Livewire;
use ReflectionClass;

abstract class ModuleServiceProvider extends ServiceProvider
{
    abstract protected function moduleAlias(): string;

    public function boot(): void
    {
        $path = $this->modulePath();
        $this->loadViewsFrom($path.'/resources/views', $this->moduleAlias());
        
        Livewire::addNamespace(
            namespace: $this->moduleAlias(),
            viewPath: $path.'/resources/views/livewire',
        );

        foreach (glob($path.'/routes/*.php') ?: [] as $routeFile) {
            $this->loadRoutesFrom($routeFile);
        }
    }

    protected function modulePath(): string
    {
        return dirname((string) (new ReflectionClass($this))->getFileName());
    }
}