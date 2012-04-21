<?php
/**
 * This file contains an entry point for Cosign authentication.
 *
 * @copyright Copyright (c) 2012 The FMFI Anketa authors (see AUTHORS).
 * Use of this source code is governed by a license that can be
 * found in the LICENSE file in the project root directory.
 *
 * @package    CosignBundle
 * @subpackage DependencyInjection
 */

namespace SVT\CosignBundle\Security;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Http\EntryPoint\AuthenticationEntryPointInterface;
use Symfony\Component\Security\Http\HttpUtils;
use Symfony\Component\HttpKernel\HttpKernelInterface;

/**
 * An entry point that redirects unauthenticated requests to a path which has
 * "CosignAllowPublicAccess Off".
 */
class CosignAuthenticationEntryPoint implements AuthenticationEntryPointInterface
{
    private $loginRoute;
    private $router;
    private $httpKernel;
    private $httpUtils;

    /**
     * Constructor
     *
     * @param HttpKernelInterface $kernel
     * @param HttpUtils           $httpUtils  An HttpUtils instance
     * @param RouterInterface     $router     An RouterInterface instance
     * @param string              $loginRoute The route of the non-public page
     */
    public function __construct(HttpKernelInterface $kernel, HttpUtils $httpUtils, RouterInterface $router, $loginRoute)
    {
        $this->httpKernel = $kernel;
        $this->httpUtils = $httpUtils;
        $this->router = $router;
        $this->loginRoute = $loginRoute;
    }

    /**
     * {@inheritdoc}
     */
    public function start(Request $request, AuthenticationException $authException = null)
    {
        return $this->httpUtils->createRedirectResponse($request,
            $this->router->generate($this->loginRoute, array('to' => $request->getUri()), true));
    }
}
