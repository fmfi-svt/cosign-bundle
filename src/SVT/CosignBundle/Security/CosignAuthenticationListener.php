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

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Role\SwitchUserRole;
use Symfony\Component\Security\Core\SecurityContextInterface;
use Symfony\Component\Security\Core\Authentication\AuthenticationManagerInterface;
use Symfony\Component\Security\Core\Authentication\Token\PreAuthenticatedToken;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Http\Event\InteractiveLoginEvent;
use Symfony\Component\Security\Http\Events;
use Symfony\Component\Security\Http\Firewall\ListenerInterface;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\HttpKernel\Log\LoggerInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

/**
 * Pre-authenticated cosign listener to be used with Apache filter
 */
class CosignAuthenticationListener implements ListenerInterface
{
    const USERNAME_KEY = 'REMOTE_USER';

    private $logger;
    private $securityContext;
    private $authenticationManager;
    private $providerKey;
    private $dispatcher;

    public function __construct(SecurityContextInterface $securityContext,
            AuthenticationManagerInterface $authenticationManager,
            $providerKey, LoggerInterface $logger = null,
            EventDispatcherInterface $dispatcher = null)
    {
        $this->securityContext = $securityContext;
        $this->authenticationManager = $authenticationManager;
        $this->providerKey = $providerKey;
        $this->logger = $logger;
        $this->dispatcher = $dispatcher;
    }

    public function handle(GetResponseEvent $event)
    {
        $request = $event->getRequest();
        $token = $this->getOriginalToken($this->securityContext->getToken());

        $username = $request->server->get(self::USERNAME_KEY, null);

        if ($token === null && $username === null) {
            // The user was not logged in previously
            // and is not trying to log in, so just
            // do nothing in this case
            return;
        }

        if ($token !== null) {
            if ($token instanceof PreAuthenticatedToken &&
                    $token->getProviderKey() === $this->providerKey) {
                // user has previously logged in using cosign
                // so check if that hasn't changed
                if ($username === null) {
                    // user has logged out of cosign via remote site
                    // so remove token (don't emit logout event as
                    // that redirects user etc.)
                    if ($this->logger !== null) {
                        $this->logger->debug(sprintf("Cleaning up token due to remote logout: %s", $token));
                    }
                    $this->securityContext->setToken(null);
                    // as we don't emit logout event
                    // we need to invalidate session manually
                    $request->getSession()->invalidate();
                    return;
                }
                if ($token->getUsername() == $username) {
                    // the user is logged in and the username
                    // didn't change, so don't overwrite the token
                    return;
                }
                // user logged in previously, but the username has changed
                // so authenticate the user again
            }
        }

        if ($this->logger !== null) {
            $this->logger->debug(sprintf("Authenticating cosign user: %s", $username));
        }

        // authenticate the user
        try {
            $token = new PreAuthenticatedToken($username, null, $this->providerKey);
            $token = $this->authenticationManager->authenticate($token);

            $this->securityContext->setToken($token);

            if ($this->logger !== null) {
                $this->logger->debug(sprintf('Authentication success: %s', $token));
            }

            if ($this->dispatcher !== null) {
                $loginEvent = new InteractiveLoginEvent($request, $token);
                $this->dispatcher->dispatch(Events::onSecurityInteractiveLogin, $loginEvent);
            }
        } catch (AuthenticationException $failed) {
            $this->securityContext->setToken(null);

            if ($this->logger !== null) {
                $this->logger->debug(sprintf("Removed token due to exception: %s", $failed->getMessage()));
            }
        }
    }

    /**
     * Return original token that was used for authentication.
     *
     * This is required to cosign login working with switch user functionality,
     * it is truly a hack, but it does work.
     *
     * @param TokenInterface $token token to check
     * @return TokenInterface original token used for authentication
     */
    private function getOriginalToken(TokenInterface $token = null)
    {
        if ($token === null) return null;
        
        $roles = $token->getRoles();
        foreach ($roles as $role) {
            if ($role instanceof SwitchUserRole) {
                return $role->getSource();
            }
        }
        return $token;
    }
}