<?php

namespace Stfalcon\Bundle\PortfolioBundle\DependencyInjection;

use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\XmlFileLoader;
use Symfony\Component\Config\Definition\Processor;
use Symfony\Component\Config\FileLocator;

/**
 * This is the class that loads and manages StfalconPortfolioBundle configuration
 */
class StfalconPortfolioExtension extends Extension
{

    /**
     * Load configuration from services.xml
     *
     * @param array            $configs   An array of configuration values
     * @param ContainerBuilder $container A ContainerBuilder instance
     *
     * @throws \InvalidArgumentException When provided tag is not defined in this extension
     *
     * @return void
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $processor = new Processor();
        $configuration = new Configuration();

        $config = $processor->processConfiguration($configuration, $configs);

        $loader = new XmlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('orm.xml');
        $container->setParameter('stfalcon_portfolio.project.entity', $config['project']['entity']);
        $container->setAlias('stfalcon_portfolio.project.manager', $config['project']['manager']);
        unset($config['project']['manager']);

        $container->setParameter('stfalcon_portfolio.category.entity', $config['category']['entity']);
        $container->setAlias('stfalcon_portfolio.category.manager', $config['category']['manager']);
        unset($config['category']['manager']);

        $loader->load('admin.xml');

        $container->setParameter('stfalcon_portfolio.category.admin.class', $config['category']['admin']['class']);
        $container->setParameter('stfalcon_portfolio.category.admin.controller', $config['category']['admin']['controller']);

        $container->setParameter('stfalcon_portfolio.project.admin.class', $config['project']['admin']['class']);
        $container->setParameter('stfalcon_portfolio.project.admin.controller', $config['project']['admin']['controller']);

    }

}