<?php

declare (strict_types=1);
namespace EasyCI20220115\Symplify\EasyTesting\Finder;

use EasyCI20220115\Symfony\Component\Finder\Finder;
use EasyCI20220115\Symplify\SmartFileSystem\Finder\FinderSanitizer;
use EasyCI20220115\Symplify\SmartFileSystem\SmartFileInfo;
final class FixtureFinder
{
    /**
     * @var \Symplify\SmartFileSystem\Finder\FinderSanitizer
     */
    private $finderSanitizer;
    public function __construct(\EasyCI20220115\Symplify\SmartFileSystem\Finder\FinderSanitizer $finderSanitizer)
    {
        $this->finderSanitizer = $finderSanitizer;
    }
    /**
     * @return SmartFileInfo[]
     */
    public function find(array $sources) : array
    {
        $finder = new \EasyCI20220115\Symfony\Component\Finder\Finder();
        $finder->files()->in($sources)->name('*.php.inc')->path('Fixture')->sortByName();
        return $this->finderSanitizer->sanitize($finder);
    }
}