<?php


// src/Security/LoginSuccessHandler.php
namespace App\Security;

use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationSuccessHandlerInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\HttpFoundation\Request;

class LoginSuccessHandler implements AuthenticationSuccessHandlerInterface
{
    private $router;

    public function __construct(RouterInterface $router)
    {
        $this->router = $router;
    }

    public function onAuthenticationSuccess(Request $request, TokenInterface $token): RedirectResponse
    {
        // Get the logged-in user
        $user = $token->getUser();
        // Get the user's roles
        $roles = $user->getRoles();

        // Redirect based on user role
        if (in_array('ROLE_ADMIN', $roles)) {
            return new RedirectResponse($this->router->generate('app_admin_dashboard'));
        } elseif (in_array('ROLE_USER', $roles)) {
            return new RedirectResponse($this->router->generate('app_admin_user_show', ['username' => $user->getUsername()]));
        }

        // Default redirection if no roles matched
        return new RedirectResponse($this->router->generate('homepage'));
    }
}
