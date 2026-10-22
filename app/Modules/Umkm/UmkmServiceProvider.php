<?php
namespace App\Modules\Umkm;

use App\Modules\ModuleServiceProvider;

final class UmkmServiceProvider extends ModuleServiceProvider
{
    public function register(): void {}

    protected function moduleAlias(): string
    {
        return 'umkm';
    }
}