<?php

declare (strict_types=1);
namespace EasyCI20220529;

use EasyCI20220529\SebastianBergmann\Diff\Differ;
use EasyCI20220529\SebastianBergmann\Diff\Output\UnifiedDiffOutputBuilder;
use EasyCI20220529\Symfony\Component\Console\Application;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use EasyCI20220529\Symplify\PackageBuilder\Composer\VendorDirProvider;
use EasyCI20220529\Symplify\PackageBuilder\Yaml\ParametersMerger;
use EasyCI20220529\Symplify\SmartFileSystem\Json\JsonFileSystem;
use EasyCI20220529\Symplify\VendorPatches\Console\VendorPatchesApplication;
use function EasyCI20220529\Symfony\Component\DependencyInjection\Loader\Configurator\service;
return static function (\Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator $containerConfigurator) : void {
    $services = $containerConfigurator->services();
    $services->defaults()->public()->autowire()->autoconfigure();
    $services->load('EasyCI20220529\Symplify\VendorPatches\\', __DIR__ . '/../src')->exclude([__DIR__ . '/../src/Kernel', __DIR__ . '/../src/ValueObject']);
    $services->set(\EasyCI20220529\SebastianBergmann\Diff\Output\UnifiedDiffOutputBuilder::class)->args(['$addLineNumbers' => \true]);
    $services->set(\EasyCI20220529\SebastianBergmann\Diff\Differ::class)->args(['$outputBuilder' => \EasyCI20220529\Symfony\Component\DependencyInjection\Loader\Configurator\service(\EasyCI20220529\SebastianBergmann\Diff\Output\UnifiedDiffOutputBuilder::class)]);
    $services->set(\EasyCI20220529\Symplify\PackageBuilder\Composer\VendorDirProvider::class);
    $services->set(\EasyCI20220529\Symplify\SmartFileSystem\Json\JsonFileSystem::class);
    // for autowired commands
    $services->alias(\EasyCI20220529\Symfony\Component\Console\Application::class, \EasyCI20220529\Symplify\VendorPatches\Console\VendorPatchesApplication::class);
    $services->set(\EasyCI20220529\Symplify\PackageBuilder\Yaml\ParametersMerger::class);
};
