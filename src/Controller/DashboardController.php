<?php
// src/Controller/DashboardController.php
namespace App\Controller;

use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractDashboardController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class DashboardController extends AbstractDashboardController
{
    /**
     * Admin index page - redirects to dashboard
     */
    #[Route('/admin', name: 'admin')]
    public function index(): Response
    {
        return $this->redirectToRoute('admin_dashboard');
    }
    
    /**
     * Main admin dashboard
     */
    #[Route('/admin/dashboard', name: 'admin_dashboard')]
    public function dashboard(): Response
    {
        // Create a proper admin dashboard
        return $this->render('admin/dashboard.html.twig', [
            'user' => $this->getUser(),
        ]);
    }
}
