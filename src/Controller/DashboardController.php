<?php
// src/Controller/DashboardController.php
namespace App\Controller;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
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
    public function dashboard(EntityManagerInterface $entityManager): Response
    {
        $user = $this->getUser();
        $company = $user->getCompany();
        
        // Count users in the current company
        $userCount = 0;
        $adminCount = 0;
        
        if ($company) {
            // Count total users in this company
            $userCount = $entityManager->getRepository(User::class)
                ->count(['company' => $company]);
                
            // Count admin users in this company - SQLite compatible approach
            // Fetch all users from this company and filter in PHP
            $companyUsers = $entityManager->getRepository(User::class)
                ->findBy(['company' => $company]);
                
            $adminCount = 0;
            foreach ($companyUsers as $companyUser) {
                if (in_array('ROLE_ADMIN', $companyUser->getRoles())) {
                    $adminCount++;
                }
            }
        }
        
        // Create a proper admin dashboard
        return $this->render('admin/dashboard.html.twig', [
            'user' => $user,
            'userCount' => $userCount,
            'adminCount' => $adminCount,
            'activeCount' => $userCount, // For simplicity, all users are considered active
        ]);
    }
    
    /**
     * Integrations page
     */
    #[Route('/admin/integrations', name: 'admin_integrations')]
    public function integrations(): Response
    {
        // Show available integrations
        return $this->render('admin/integrations.html.twig', [
            'user' => $this->getUser(),
        ]);
    }
}
