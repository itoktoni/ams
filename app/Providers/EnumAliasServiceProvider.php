<?php

namespace App\Providers;

use Illuminate\Support\Facades\File;
use Illuminate\Support\ServiceProvider;

class EnumAliasServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $enumsPath = app_path('Enums');

        if (! File::isDirectory($enumsPath)) {
            return;
        }

        foreach (File::allFiles($enumsPath) as $file) {
            $relativePath = $file->getRelativePathname();
            $className = 'App\\Enums\\'.str_replace(['/', '.php'], ['\\', ''], $relativePath);

            if (! class_exists($className)) {
                continue;
            }

            $alias = class_basename($className);

            if (! class_exists($alias)) {
                class_alias($className, $alias);
            }
        }
    }

    public function boot(): void
    {
    }
}
