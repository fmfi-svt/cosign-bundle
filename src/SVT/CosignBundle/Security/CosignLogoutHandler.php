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
use Symfony\Component\HttpKernel\Log\LoggerInterface;

class CosignLogoutHandler implements LogoutHandlerInterface
{
    private $logger;

    public function __construct(LoggerInterface $logger = null)
    {
        $this->logger = $logger;
    }

    public function logout(Request $request, Response $response, TokenInterface $token)
    {
        if ($this->logger) {
            $this->logger->info('Cosign logout handler invoked');
        }
        if ($request->server->has('COSIGN_SERVICE')) {
            $cookieName = $request->server->get('COSIGN_SERVICE');
            if ($this->logger) {
                $this->logger->info(sprintf('Clearing %s cookie', $cookieName));
            }
            $response->headers->clearCookie($cookieName);
        }
    }
    
}