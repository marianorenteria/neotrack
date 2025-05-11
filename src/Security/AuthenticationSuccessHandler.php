<?php
// src/Security/AuthenticationSuccessHandler.php
namespace App\Security;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationSuccessHandlerInterface;

class AuthenticationSuccessHandler implements AuthenticationSuccessHandlerInterface
{
    private $urlGenerator;

    public function __construct(UrlGeneratorInterface $urlGenerator)
    {
        $this->urlGenerator = $urlGenerator;
    }

    public function onAuthenticationSuccess(Request $request, TokenInterface $token): RedirectResponse
    {
        // Get user roles
        $roles = $token->getRoleNames();

        // Redirect based on roles
        if (in_array('ROLE_ADMIN', $roles)) {
            // Admin users go to admin dashboard
            return new RedirectResponse($this->urlGenerator->generate('admin_dashboard'));
        }
        
        // Default redirect for other users
        return new RedirectResponse($this->urlGenerator->generate('hello_world'));
    }
}
