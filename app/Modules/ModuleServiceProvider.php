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
        $viewPath = $path . '/resources/views';
        $livewirePath = $viewPath . '/livewire';

        // Daftarkan view hanya jika direktori tersedia.
        if (is_dir($viewPath)) {
            $this->loadViewsFrom($viewPath, $this->moduleAlias());
        }

        // Daftarkan namespace Livewire hanya jika direktori tersedia.
        if (is_dir($livewirePath)) {
            Livewire::addNamespace(
                namespace: $this->moduleAlias(),
                viewPath: $livewirePath,
            );
        }

        // Muat seluruh file route yang tersedia.
        foreach (glob($path . '/routes/*.php') ?: [] as $routeFile) {
            $this->loadRoutesFrom($routeFile);
        }
    }

    protected function modulePath(): string
    {
        return dirname((string) (new ReflectionClass($this))->getFileName());
    }
}
