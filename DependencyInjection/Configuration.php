<?php

namespace Draw\PaymentBundle\DependencyInjection;


use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

class Configuration implements ConfigurationInterface
{
    /**
     * {@inheritdoc}
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $treeBuilder->root('draw_payment_bundle')
            ->children()
                ->scalarNode('stripe_api_key')->isRequired()->end()
            ->end();

        return $treeBuilder;
    }
}
