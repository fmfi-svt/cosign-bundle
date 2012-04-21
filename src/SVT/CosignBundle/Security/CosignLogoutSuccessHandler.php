<?php
/**
 * This file contains Cosign logout handler.
 *
 * @copyright Copyright (c) 2011-2012 The FMFI Anketa authors (see AUTHORS).
 * Use of this source code is governed by a license that can be
 * found in the LICENSE file in the project root directory.
 *
 * @package    CosignBundle
 * @subpackage Security
 * @author     Martin Sucha <anty.sk+svt@gmail.com>
 */

namespace SVT\CosignBundle\Security;

use Symfony\Component\Security\Http\Logout\LogoutSuccessHandlerInterface;
use Symfony\Component\Security\Http\HttpUtils;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\Cookie;

class CosignLogoutSuccessHandler implements LogoutSuccessHandlerInterface
{
    private $httpUtils;
    private $cosignLogoutPrefix;
    private $logger;

    public function __construct(HttpUtils $httpUtils, $cosignLogoutPrefix, LoggerInterface $logger = null)
    {
        $this->httpUtils = $httpUtils;
        $this->cosignLogoutPrefix = $cosignLogoutPrefix;
        $this->logger = $logger;
    }

    public function onLogoutSuccess(Request $request)
    {
        if ($this->logger) {
            $this->logger->info('Cosign logout success handler invoked');
        }
        
        $response = $this->httpUtils->createRedirectResponse($request,
            $this->cosignLogoutPrefix . $request->getUriForPath('/'));
        
        if ($request->server->has('COSIGN_SERVICE')) {
            $cookieName = $request->server->get('COSIGN_SERVICE');
            if ($this->logger) {
                $this->logger->info(sprintf('Clearing %s cookie', $cookieName));
            }
            // We need to use secure = true to correctly clear cookie
            $response->headers->setCookie(new Cookie($cookieName, null, 1, '/', null, true));
        }
        
        return $response;
    }
    
}
