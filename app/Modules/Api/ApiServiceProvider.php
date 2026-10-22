<?php
namespace App\Modules\Api;

use App\Modules\ModuleServiceProvider;

final class ApiServiceProvider extends ModuleServiceProvider
{
    public function register(): void {}

    protected function moduleAlias(): string
    {
        return 'api';
    }
}