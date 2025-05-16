<?php

use App\Kernel;

// Chargement standard de l'autoloader
require_once dirname(__DIR__) . '/vendor/autoload.php';

// Chargement spécifique du runtime Symfony
$autoloadRuntime = dirname(__DIR__) . '/vendor/symfony/runtime/Internal/autoload_runtime.php';
if (file_exists($autoloadRuntime)) {
    require_once $autoloadRuntime;
} else {
    // Fallback si le fichier n'existe pas
    require_once dirname(__DIR__) . '/vendor/symfony/runtime/Internal/ComposerPlugin.php';
    require_once dirname(__DIR__) . '/vendor/symfony/runtime/Internal/SymfonyErrorHandler.php';
    require_once dirname(__DIR__) . '/vendor/symfony/runtime/Internal/Console/ApplicationRuntime.php';
}

return function (array $context) {
    return new Kernel($context['APP_ENV'], (bool) $context['APP_DEBUG']);
};
