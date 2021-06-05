<?php

declare (strict_types=1);
namespace RectorPrefix20210605\Symplify\SimplePhpDocParser\Bundle\DependencyInjection\Extension;

use RectorPrefix20210605\Symfony\Component\Config\FileLocator;
use RectorPrefix20210605\Symfony\Component\DependencyInjection\ContainerBuilder;
use RectorPrefix20210605\Symfony\Component\DependencyInjection\Extension\Extension;
use RectorPrefix20210605\Symfony\Component\DependencyInjection\Loader\PhpFileLoader;
final class SimplePhpDocParserExtension extends \RectorPrefix20210605\Symfony\Component\DependencyInjection\Extension\Extension
{
    /**
     * @param string[] $configs
     */
    public function load(array $configs, \RectorPrefix20210605\Symfony\Component\DependencyInjection\ContainerBuilder $containerBuilder) : void
    {
        $phpFileLoader = new \RectorPrefix20210605\Symfony\Component\DependencyInjection\Loader\PhpFileLoader($containerBuilder, new \RectorPrefix20210605\Symfony\Component\Config\FileLocator(__DIR__ . '/../../../../config'));
        $phpFileLoader->load('config.php');
    }
}
