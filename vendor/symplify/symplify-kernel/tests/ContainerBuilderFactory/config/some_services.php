<?php

declare (strict_types=1);
namespace EasyCI202211;

use EasyCI202211\Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use EasyCI202211\Symplify\SmartFileSystem\SmartFileSystem;
return static function (ContainerConfigurator $containerConfigurator) : void {
    $services = $containerConfigurator->services();
    $services->set(SmartFileSystem::class);
};
