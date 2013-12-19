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
        $config = $configs[0];

        $container->setParameter('stfalcon_portfolio.project.entity', $config['project']['entity']);
        $container->setParameter('stfalcon_portfolio.category.entity', $config['category']['entity']);

        $loader = new XmlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        if (isset($config['project']['repository'])) {
            $container->setParameter('stfalcon_portfolio.project.repository', $config['project']['repository']);
        }
        if (isset($config['category']['repository'])) {
            $container->setParameter('stfalcon_portfolio.category.repository', $config['category']['repository']);
        }
        $loader->load('orm.xml');
        $loader->load('admin.xml');

        if (isset($config['category']['admin']['class'])) {
            $container->setParameter('stfalcon_portfolio.category.admin.class', $config['category']['admin']['class']);
        }
        if (isset($config['category']['admin']['controller'])) {
            $container->setParameter('stfalcon_portfolio.category.admin.controller', $config['category']['admin']['controller']);
        }
        if (isset($config['project']['admin']['class'])) {
            $container->setParameter('stfalcon_portfolio.project.admin.class', $config['project']['admin']['class']);
        }
        if (isset($config['project']['admin']['controller'])) {
            $container->setParameter('stfalcon_portfolio.project.admin.controller', $config['project']['admin']['controller']);
        }
    }

}