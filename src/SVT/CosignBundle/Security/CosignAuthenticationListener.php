<?php
/**
 * This file contains Cosign authentication listener.
 *
 * @copyright Copyright (c) 2011 The FMFI Anketa authors (see AUTHORS).
 * Use of this source code is governed by a license that can be
 * found in the LICENSE file in the project root directory.
 *
 * @package    CosignBundle
 * @subpackage Security
 * @author     Martin Sucha <anty.sk+svt@gmail.com>
 */

namespace SVT\CosignBundle\Security;

use Symfony\Component\Security\Http\Firewall\AbstractPreAuthenticatedListener;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Exception\BadCredentialsException;

/**
 * PreAuthenticatedListener that uses cosign environment to obtain credentials.
 */
class CosignAuthenticationListener extends AbstractPreAuthenticatedListener
{
    const USERNAME_KEY = 'REMOTE_USER';
    
    /**
     * Get pre-authenticated data for this listener
     * @param Request $request
     * @return array(string, string) array containing username and credential
     */
    protected function getPreAuthenticatedData(Request $request)
    {
        if (!$request->server->has(self::USERNAME_KEY)) {
            throw new BadCredentialsException('Cosign username not present');
        }

        return array($request->server->get(self::USERNAME_KEY), '');
    }
}