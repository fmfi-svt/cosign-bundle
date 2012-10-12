<?php
/**
 * This file contains SVTCosignBundle.
 *
 * @copyright Copyright (c) 2011 The FMFI Anketa authors (see AUTHORS).
 * Use of this source code is governed by a license that can be
 * found in the LICENSE file in the project root directory.
 *
 * @package    CosignBundle
 * @author     Martin Sucha <anty.sk+svt@gmail.com>
 */

namespace SVT\CosignBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use SVT\CosignBundle\DependencyInjection\CosignSecurityFactory;

class SVTCosignBundle extends Bundle
{
    public function build(ContainerBuilder $container)
    {
        parent::build($container);

        $extension = $container->getExtension('security');
        $extension->addSecurityListenerFactory(new CosignSecurityFactory());
    }
}
