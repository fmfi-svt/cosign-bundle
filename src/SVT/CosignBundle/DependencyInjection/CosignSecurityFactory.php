<?php
/**
 * This file contains Cosign security factory.
 *
 * @copyright Copyright (c) 2011 The FMFI Anketa authors (see AUTHORS).
 * Use of this source code is governed by a license that can be
 * found in the LICENSE file in the project root directory.
 *
 * @package    CosignBundle
 * @subpackage DependencyInjection
 * @author     Martin Sucha <anty.sk+svt@gmail.com>
 */

namespace SVT\CosignBundle\DependencyInjection;

use Symfony\Bundle\SecurityBundle\DependencyInjection\Security\Factory\SecurityFactoryInterface;
use Symfony\Component\Config\Definition\Builder\NodeDefinition;
use Symfony\Component\DependencyInjection\DefinitionDecorator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

/**
 * A security factory to register cosign listener in symfony.
 */
class CosignSecurityFactory implements SecurityFactoryInterface
{
    public function create(ContainerBuilder $container, $id, $config, $userProvider, $defaultEntryPoint)
    {
        $provider = 'security.authentication.provider.pre_authenticated.'.$id;
        $container
            ->setDefinition($provider, new DefinitionDecorator('security.authentication.provider.pre_authenticated'))
            ->setArgument(0, new Reference($userProvider))
            ->addArgument($id)
        ;

        $listenerId = 'security.authentication.listener.cosign.'.$id;
        $listener = $container->setDefinition($listenerId, new DefinitionDecorator('security.authentication.listener.cosign'));
        $listener->setArgument(2, $id);

        return array($provider, $listenerId, $defaultEntryPoint);
    }

    public function getPosition()
    {
        return 'pre_auth';
    }

    public function getKey()
    {
        return 'cosign';
    }

    public function addConfiguration(NodeDefinition $node)
    {
        $node
            ->children()
                ->scalarNode('provider')->end()
            ->end()
        ;
    }
}