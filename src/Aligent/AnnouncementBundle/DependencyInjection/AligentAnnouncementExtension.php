<?php
/**
 * @category  Aligent
 * @package
 * @author    Greg Ziborov <greg.ziborov@aligent.com.au>
 * @copyright 2021 Aligent Consulting.
 * @license
 * @link      http://www.aligent.com.au/
 */
namespace Aligent\AnnouncementBundle\DependencyInjection;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;

class AligentAnnouncementExtension extends Extension
{
    const ALIAS = 'aligent_announcement';

    /**
     * @param array<int,mixed> $configs
     * @param ContainerBuilder $container
     * @throws \Exception
     */
    public function load(array $configs, ContainerBuilder $container): void
    {
        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);
        $container->prependExtensionConfig($this->getAlias(), $config);

        $loader = new Loader\YamlFileLoader($container, new FileLocator(__DIR__ . '/../Resources/config'));
        $loader->load('services.yml');
    }

    public function getAlias(): string
    {
        return self::ALIAS;
    }
}
