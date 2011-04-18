<?php
/**
 * This file contains dependency injection extension for SVTCosignBundle.
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

use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\XmlFileLoader;
use Symfony\Component\Config\FileLocator;

/**
 * Dependency injection extension for SVTCosignBundle
 */
class SVTCosignExtension extends Extension
{
    public function load(array $configs, ContainerBuilder $container)
    {
        // load services
        $loader = new XmlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('security_listeners.xml');
    }
}