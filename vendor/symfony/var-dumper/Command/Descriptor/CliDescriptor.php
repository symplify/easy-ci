<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace EasyCI202307\Symfony\Component\VarDumper\Command\Descriptor;

use EasyCI202307\Symfony\Component\Console\Input\ArrayInput;
use EasyCI202307\Symfony\Component\Console\Output\OutputInterface;
use EasyCI202307\Symfony\Component\Console\Style\SymfonyStyle;
use EasyCI202307\Symfony\Component\VarDumper\Cloner\Data;
use EasyCI202307\Symfony\Component\VarDumper\Dumper\CliDumper;
/**
 * Describe collected data clones for cli output.
 *
 * @author Maxime Steinhausser <maxime.steinhausser@gmail.com>
 *
 * @final
 */
class CliDescriptor implements DumpDescriptorInterface
{
    /**
     * @var \Symfony\Component\VarDumper\Dumper\CliDumper
     */
    private $dumper;
    /**
     * @var mixed
     */
    private $lastIdentifier = null;
    public function __construct(CliDumper $dumper)
    {
        $this->dumper = $dumper;
    }
    public function describe(OutputInterface $output, Data $data, array $context, int $clientId) : void
    {
        $io = $output instanceof SymfonyStyle ? $output : new SymfonyStyle(new ArrayInput([]), $output);
        $this->dumper->setColors($output->isDecorated());
        $rows = [['date', \date('r', (int) $context['timestamp'])]];
        $lastIdentifier = $this->lastIdentifier;
        $this->lastIdentifier = $clientId;
        $section = "Received from client #{$clientId}";
        if (isset($context['request'])) {
            $request = $context['request'];
            $this->lastIdentifier = $request['identifier'];
            $section = \sprintf('%s %s', $request['method'], $request['uri']);
            if ($controller = $request['controller']) {
                $rows[] = ['controller', \rtrim($this->dumper->dump($controller, \true), "\n")];
            }
        } elseif (isset($context['cli'])) {
            $this->lastIdentifier = $context['cli']['identifier'];
            $section = '$ ' . $context['cli']['command_line'];
        }
        if ($this->lastIdentifier !== $lastIdentifier) {
            $io->section($section);
        }
        if (isset($context['source'])) {
            $source = $context['source'];
            $sourceInfo = \sprintf('%s on line %d', $source['name'], $source['line']);
            if ($fileLink = $source['file_link'] ?? null) {
                $sourceInfo = \sprintf('<href=%s>%s</>', $fileLink, $sourceInfo);
            }
            $rows[] = ['source', $sourceInfo];
            $file = $source['file_relative'] ?? $source['file'];
            $rows[] = ['file', $file];
        }
        $io->table([], $rows);
        $this->dumper->dump($data);
        $io->newLine();
    }
}
