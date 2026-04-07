<?php

declare(strict_types=1);

namespace App\Tests\Web;

use App\Entity\LoanApplication;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;

class LoanApplicationControllerTest extends WebTestCase
{
    public function testCreateLoanApplicationSuccessfullyexit(): void
    {
        $client = static::createClient();
        $container = static::getContainer();
        $entityManager = $container->get('doctrine.orm.entity_manager');

        // 1. Prepare valid input data
        $payload = [
            'term' => 12,
            'principal' => 5000.00,
            'repayment_frequency' => 'monthly',
            'rate' => 5.5,
            'currency' => 'USD',
            'consent_to_email' => true,
        ];

        // 2. Execute the request
        $client->request(
            'POST',
            '/loan-application/create',
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            json_encode($payload)
        );

        // 3. Assert Response
        $this->assertResponseIsSuccessful();
        $responseContent = json_decode($client->getResponse()->getContent(), true);
        $this->assertArrayHasKey('payment', $responseContent);
        $this->assertArrayHasKey('total_interest', $responseContent);

        // 4. Assert Database Persistence
        $repo = $entityManager->getRepository(LoanApplication::class);
        $application = $repo->findOneBy(['currency' => 'USD']);

        $this->assertNotNull($application, 'Loan application should be saved in DB');
        $this->assertEquals(12, $application->getTerm());

//        // 5. Assert File Creation
//        $expectedFilePath = __DIR__ . '/../../../var/batch/loanApplication' . $application->getId() . '.json';
//        $this->assertFileExists($expectedFilePath);

//        // Cleanup file after test
//        unlink($expectedFilePath);

        // 6. Assert Email Sent
        $this->assertQueuedEmailCount(1);
    }

    public function testValidationFailureReturnsError(): void
    {
        $client = static::createClient();

        // Send invalid term (string instead of int)
        $client->request(
            'POST',
            '/loan-application/create',
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            json_encode(['term' => 'not-an-int'])
        );

        $this->assertEquals(Response::HTTP_OK, $client->getResponse()->getStatusCode());
        $this->assertStringContainsString('term: not int', $client->getResponse()->getContent());
    }
}
