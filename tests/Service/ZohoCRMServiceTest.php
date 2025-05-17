<?php
// tests/Service/ZohoCRMServiceTest.php
namespace App\Tests\Service;

use App\Entity\Company;
use App\Entity\ZohoCRM;
use App\Service\ZohoCRMService;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpClient\MockHttpClient;
use Symfony\Component\HttpClient\Response\MockResponse;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use ReflectionClass;

class ZohoCRMServiceTest extends TestCase
{
    private EntityManagerInterface $entityManager;
    private ZohoCRM $zohoCRM;

    protected function setUp(): void
    {
        $this->entityManager = $this->createMock(EntityManagerInterface::class);
        
        // Create test company
        $company = new Company();
        $company->setName('Test Company');
        
        // Create test ZohoCRM entity
        $this->zohoCRM = new ZohoCRM();
        $this->zohoCRM->setCompany($company);
        $this->zohoCRM->setClientId('test_client_id');
        $this->zohoCRM->setClientSecret('test_client_secret');
        $this->zohoCRM->setApiToken('test_api_token');
        $this->zohoCRM->setRefreshToken('test_refresh_token');
        $this->zohoCRM->setIsActive(true);
    }
    
    public function testRefreshToken()
    {
        // Mock HTTP client with successful token refresh response
        $mockResponseJson = json_encode([
            'access_token' => 'new_access_token',
            'expires_in' => 3600
        ]);
        $mockResponse = new MockResponse($mockResponseJson, ['http_code' => 200]);
        $mockHttpClient = new MockHttpClient($mockResponse);
        
        // Create ZohoCRMService with mocked HTTP client
        $zohoCRMService = $this->createZohoCRMServiceWithMockedClient($mockHttpClient);

        // Test token refresh
        $result = $zohoCRMService->refreshToken($this->zohoCRM);
        
        $this->assertTrue($result);
        $this->assertEquals('new_access_token', $this->zohoCRM->getApiToken());
    }
    
    public function testGetContacts()
    {
        // Mock HTTP client with sample contacts response
        $mockResponseJson = json_encode([
            'data' => [
                [
                    'id' => '123456',
                    'First_Name' => 'John',
                    'Last_Name' => 'Doe',
                    'Email' => 'john.doe@example.com'
                ],
                [
                    'id' => '789012',
                    'First_Name' => 'Jane',
                    'Last_Name' => 'Smith',
                    'Email' => 'jane.smith@example.com'
                ]
            ]
        ]);
        $mockResponse = new MockResponse($mockResponseJson, ['http_code' => 200]);
        $mockHttpClient = new MockHttpClient($mockResponse);
        
        // Create ZohoCRMService with mocked HTTP client
        $zohoCRMService = $this->createZohoCRMServiceWithMockedClient($mockHttpClient);
        
        // Set last sync to a recent time to avoid token refresh
        $this->zohoCRM->setLastSynced(new \DateTime());
        
        // Test getting contacts
        $contacts = $zohoCRMService->getContacts($this->zohoCRM);
        
        $this->assertCount(2, $contacts);
        $this->assertEquals('John', $contacts[0]['First_Name']);
        $this->assertEquals('Jane', $contacts[1]['First_Name']);
    }
    
    public function testGetLeads()
    {
        // Mock HTTP client with sample leads response
        $mockResponseJson = json_encode([
            'data' => [
                [
                    'id' => '123456',
                    'Company' => 'Acme Corp',
                    'Last_Name' => 'Johnson',
                    'Email' => 'johnson@acme.com'
                ]
            ]
        ]);
        $mockResponse = new MockResponse($mockResponseJson, ['http_code' => 200]);
        $mockHttpClient = new MockHttpClient($mockResponse);
        
        // Create ZohoCRMService with mocked HTTP client
        $zohoCRMService = $this->createZohoCRMServiceWithMockedClient($mockHttpClient);
        
        // Set last sync to a recent time to avoid token refresh
        $this->zohoCRM->setLastSynced(new \DateTime());
        
        // Test getting leads
        $leads = $zohoCRMService->getLeads($this->zohoCRM);
        
        $this->assertCount(1, $leads);
        $this->assertEquals('Acme Corp', $leads[0]['Company']);
    }
    
    private function createZohoCRMServiceWithMockedClient(HttpClientInterface $mockHttpClient): ZohoCRMService
    {
        $zohoCRMService = new ZohoCRMService($this->entityManager);
        
        // Use Reflection to inject the mocked HTTP client
        $reflection = new ReflectionClass(ZohoCRMService::class);
        $clientProperty = $reflection->getProperty('client');
        $clientProperty->setAccessible(true);
        $clientProperty->setValue($zohoCRMService, $mockHttpClient);
        
        return $zohoCRMService;
    }
}
