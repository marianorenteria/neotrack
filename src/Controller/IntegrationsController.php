<?php
// src/Controller/IntegrationsController.php
namespace App\Controller;

use App\Entity\ZohoCRM;
use App\Form\ZohoCRMType;
use App\Service\ZohoCRMService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/admin')]
class IntegrationsController extends AbstractController
{
    #[Route('/integrations', name: 'admin_integrations')]
    public function index(): Response
    {
        $user = $this->getUser();
        $company = $user->getCompany();
        
        // Get status of integrations
        $zohoConnected = false;
        $zohoLastSynced = null;
        
        if ($company && $company->getZohoCRM()) {
            $zohoCRM = $company->getZohoCRM();
            $zohoConnected = $zohoCRM->isIsActive();
            $zohoLastSynced = $zohoCRM->getLastSynced();
        }
        
        return $this->render('admin/integrations.html.twig', [
            'user' => $user,
            'zohoConnected' => $zohoConnected,
            'zohoLastSynced' => $zohoLastSynced
        ]);
    }
    
    #[Route('/zohocrm/connect', name: 'admin_zohocrm_connect')]
    public function connectZohoCRM(Request $request, EntityManagerInterface $entityManager): Response
    {
        $user = $this->getUser();
        $company = $user->getCompany();
        
        if (!$company) {
            $this->addFlash('error', 'You must be associated with a company to connect Zoho CRM.');
            return $this->redirectToRoute('admin_integrations');
        }
        
        // Check if company already has Zoho CRM configured
        $zohoCRM = $company->getZohoCRM();
        if (!$zohoCRM) {
            $zohoCRM = new ZohoCRM();
            $zohoCRM->setCompany($company);
        }
        
        $form = $this->createForm(ZohoCRMType::class, $zohoCRM);
        $form->handleRequest($request);
        
        if ($form->isSubmitted() && $form->isValid()) {
            // Set the integration as active
            $zohoCRM->setIsActive(true);
            
            $entityManager->persist($zohoCRM);
            $entityManager->flush();
            
            $this->addFlash('success', 'Zoho CRM integration has been successfully configured.');
            return $this->redirectToRoute('admin_integrations');
        }
        
        return $this->render('admin/zohocrm/connect.html.twig', [
            'user' => $user,
            'form' => $form->createView(),
        ]);
    }
    
    #[Route('/zohocrm/disconnect', name: 'admin_zohocrm_disconnect')]
    public function disconnectZohoCRM(EntityManagerInterface $entityManager): Response
    {
        $user = $this->getUser();
        $company = $user->getCompany();
        
        if (!$company) {
            $this->addFlash('error', 'You must be associated with a company to manage integrations.');
            return $this->redirectToRoute('admin_integrations');
        }
        
        $zohoCRM = $company->getZohoCRM();
        if ($zohoCRM) {
            $entityManager->remove($zohoCRM);
            $entityManager->flush();
            
            $this->addFlash('success', 'Zoho CRM integration has been successfully disconnected.');
        } else {
            $this->addFlash('error', 'No active Zoho CRM integration found.');
        }
        
        return $this->redirectToRoute('admin_integrations');
    }
    
    #[Route('/zohocrm/sync', name: 'admin_zohocrm_sync')]
    public function syncZohoCRM(EntityManagerInterface $entityManager, ZohoCRMService $zohoCRMService): Response
    {
        $user = $this->getUser();
        $company = $user->getCompany();
        
        if (!$company) {
            $this->addFlash('error', 'You must be associated with a company to sync Zoho CRM data.');
            return $this->redirectToRoute('admin_integrations');
        }
        
        $zohoCRM = $company->getZohoCRM();
        if (!$zohoCRM || !$zohoCRM->isIsActive()) {
            $this->addFlash('error', 'No active Zoho CRM integration found.');
            return $this->redirectToRoute('admin_integrations');
        }
        
        // Sync contacts
        $contacts = $zohoCRMService->getContacts($zohoCRM);
        $leads = $zohoCRMService->getLeads($zohoCRM);
        
        if (empty($contacts) && empty($leads)) {
            $this->addFlash('error', 'Failed to sync data from Zoho CRM. Please check your integration credentials.');
        } else {
            $totalRecords = count($contacts) + count($leads);
            $this->addFlash('success', "Successfully synced $totalRecords records from Zoho CRM.");
        }
        
        return $this->redirectToRoute('admin_integrations');
    }
}
