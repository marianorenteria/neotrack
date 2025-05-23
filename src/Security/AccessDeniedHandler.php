<?php
// src/Security/AccessDeniedHandler.php
namespace App\Security;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\Security\Http\Authorization\AccessDeniedHandlerInterface;

class AccessDeniedHandler implements AccessDeniedHandlerInterface
{
    private $urlGenerator;

    public function __construct(UrlGeneratorInterface $urlGenerator) 
    {
        $this->urlGenerator = $urlGenerator;
    }


    public function handle(Request $request, AccessDeniedException $accessDeniedException): RedirectResponse
    {
        // Add a flash message
        $session = $request->getSession();
        if ($session) {
            // Direct flash method for Symfony 7+
            $session->getFlashBag()->add('error', 'Access Denied');
        }

        // Redirect to login
        return new RedirectResponse($this->urlGenerator->generate('app_login'));
    }
}