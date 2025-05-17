<?php
// src/Service/ZohoCRMService.php
namespace App\Service;

use App\Entity\ZohoCRM;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpClient\HttpClient;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class ZohoCRMService
{
    private HttpClientInterface $client;
    private EntityManagerInterface $entityManager;
    private const ZOHO_API_BASE_URL = 'https://www.zohoapis.com/crm/v2';
    private const ZOHO_ACCOUNTS_URL = 'https://accounts.zoho.com/oauth/v2/token';

    public function __construct(EntityManagerInterface $entityManager, ?HttpClientInterface $client = null)
    {
        $this->client = $client ?? HttpClient::create();
        $this->entityManager = $entityManager;
    }

    public function refreshToken(ZohoCRM $zohoCRM): bool
    {
        try {
            $response = $this->client->request('POST', self::ZOHO_ACCOUNTS_URL, [
                'body' => [
                    'refresh_token' => $zohoCRM->getRefreshToken(),
                    'client_id' => $zohoCRM->getClientId(),
                    'client_secret' => $zohoCRM->getClientSecret(),
                    'grant_type' => 'refresh_token'
                ]
            ]);

            $data = json_decode($response->getContent(), true);

            if (isset($data['access_token'])) {
                $zohoCRM->setApiToken($data['access_token']);
                $this->entityManager->flush();
                return true;
            }

            return false;
        } catch (TransportExceptionInterface $e) {
            // Log the exception
            return false;
        }
    }

    public function getContacts(ZohoCRM $zohoCRM): array
    {
        try {
            // Check if we need to refresh the token
            if ($this->shouldRefreshToken($zohoCRM)) {
                $this->refreshToken($zohoCRM);
            }

            $response = $this->client->request('GET', self::ZOHO_API_BASE_URL . '/Contacts', [
                'headers' => [
                    'Authorization' => 'Zoho-oauthtoken ' . $zohoCRM->getApiToken(),
                ]
            ]);

            $data = json_decode($response->getContent(), true);
            
            // Update last synced timestamp
            $zohoCRM->setLastSynced(new \DateTime());
            $this->entityManager->flush();
            
            return $data['data'] ?? [];
        } catch (TransportExceptionInterface $e) {
            // Log the exception
            return [];
        }
    }
    
    public function getLeads(ZohoCRM $zohoCRM): array
    {
        try {
            // Check if we need to refresh the token
            if ($this->shouldRefreshToken($zohoCRM)) {
                $this->refreshToken($zohoCRM);
            }

            $response = $this->client->request('GET', self::ZOHO_API_BASE_URL . '/Leads', [
                'headers' => [
                    'Authorization' => 'Zoho-oauthtoken ' . $zohoCRM->getApiToken(),
                ]
            ]);

            $data = json_decode($response->getContent(), true);
            
            // Update last synced timestamp
            $zohoCRM->setLastSynced(new \DateTime());
            $this->entityManager->flush();
            
            return $data['data'] ?? [];
        } catch (TransportExceptionInterface $e) {
            // Log the exception
            return [];
        }
    }
    
    private function shouldRefreshToken(ZohoCRM $zohoCRM): bool
    {
        // Check if last synced was more than 50 minutes ago (tokens typically expire after 1 hour)
        $lastSynced = $zohoCRM->getLastSynced();
        if (!$lastSynced) {
            return true;
        }
        
        $now = new \DateTime();
        $interval = $now->diff($lastSynced);
        $minutesSinceLastSync = ($interval->days * 24 * 60) + ($interval->h * 60) + $interval->i;
        
        return $minutesSinceLastSync > 50;
    }
}
