<?php
/**
 * This file contains Cosign logout handler.
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

use Symfony\Component\Security\Http\Logout\LogoutHandlerInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;

class CosignLogoutHandler implements LogoutHandlerInterface
{
    public function __construct()
    {
        
    }

    public function logout(Request $request, Response $response, TokenInterface $token)
    {
        $response->headers->clearCookie($request->server->get('COSIGN_SERVICE'), '/');
    }
    
}