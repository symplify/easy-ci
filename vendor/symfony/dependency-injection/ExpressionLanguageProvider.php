<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace EasyCI20220115\Symfony\Component\DependencyInjection;

use EasyCI20220115\Symfony\Component\ExpressionLanguage\ExpressionFunction;
use EasyCI20220115\Symfony\Component\ExpressionLanguage\ExpressionFunctionProviderInterface;
/**
 * Define some ExpressionLanguage functions.
 *
 * To get a service, use service('request').
 * To get a parameter, use parameter('kernel.debug').
 *
 * @author Fabien Potencier <fabien@symfony.com>
 */
class ExpressionLanguageProvider implements \EasyCI20220115\Symfony\Component\ExpressionLanguage\ExpressionFunctionProviderInterface
{
    /**
     * @var \Closure|null
     */
    private $serviceCompiler;
    public function __construct(callable $serviceCompiler = null)
    {
        $this->serviceCompiler = null !== $serviceCompiler && !$serviceCompiler instanceof \Closure ? \Closure::fromCallable($serviceCompiler) : $serviceCompiler;
    }
    public function getFunctions() : array
    {
        return [new \EasyCI20220115\Symfony\Component\ExpressionLanguage\ExpressionFunction('service', $this->serviceCompiler ?? function ($arg) {
            return \sprintf('$this->get(%s)', $arg);
        }, function (array $variables, $value) {
            return $variables['container']->get($value);
        }), new \EasyCI20220115\Symfony\Component\ExpressionLanguage\ExpressionFunction('parameter', function ($arg) {
            return \sprintf('$this->getParameter(%s)', $arg);
        }, function (array $variables, $value) {
            return $variables['container']->getParameter($value);
        })];
    }
}