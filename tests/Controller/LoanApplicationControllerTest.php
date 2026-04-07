<?php

declare(strict_types=1);

namespace App\Tests\Controller;

use App\Controller\LoanApplicationController;
use App\Entity\LoanApplication;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\TestCase;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use function var_dump;

class LoanApplicationControllerTest extends TestCase
{
    private $entityManager;
    private $mailer;
    private $controller;

    protected function setUp(): void
    {
        $this->entityManager = $this->createMock(EntityManagerInterface::class);
        $this->mailer = $this->createMock(MailerInterface::class);

        // We instantiate the controller directly.
        // Note: In a full integration test, you'd use WebTestCase.
        $this->controller = new LoanApplicationController();
        $this->controller->setContainer($this->createMock(ContainerInterface::class));
    }

    /**
     * Test a successful loan application process
     */
    public function testCreateSuccess(): void
    {
        $payload = [
            'term' => 12,
            'principal' => 5000.0,
            'repayment_frequency' => 'monthly',
            'rate' => 5.5,
            'currency' => 'USD',
            'consent_to_email' => true
        ];

        $request = new Request([], [], [], [], [], [], json_encode($payload));

        // Expect DB persistence
        $this->entityManager->expects($this->once())->method('persist')->with($this->isInstanceOf(LoanApplication::class));
        $this->entityManager->expects($this->once())->method('flush');

        // Expect Email to be sent
        $this->mailer->expects($this->once())->method('send')->with($this->isInstanceOf(Email::class));

        $response = $this->controller->create($request, $this->mailer, $this->entityManager);

        $this->assertEquals(Response::HTTP_OK, $response->getStatusCode());
        $data = json_decode($response->getContent(), true);

        $this->assertArrayHasKey('payment', $data);
        $this->assertEquals('USD', $data['currency']);
    }

    /**
     * Test validation failure for 'term' type
     */
    public function testCreateInvalidTermType(): void
    {
        $payload = [
            'term' => '12', // String instead of int
            'principal' => 5000.0,
            'repayment_frequency' => 'monthly',
            'rate' => 5.5,
            'currency' => 'USD',
            'consent_to_email' => true
        ];

        $request = new Request([], [], [], [], [], [], json_encode($payload));

        $response = $this->controller->create($request, $this->mailer, $this->entityManager);

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertStringContainsString('term: not int', $response->getContent());
    }

    /**
     * Test validation failure for invalid frequency enumeration
     */
    public function testCreateInvalidFrequency(): void
    {
        $payload = [
            'term' => 12,
            'principal' => 5000.0,
            'repayment_frequency' => 'yearly', // Invalid enum
            'rate' => 5.5,
            'currency' => 'USD',
            'consent_to_email' => true
        ];

        $request = new Request([], [], [], [], [], [], json_encode($payload));

        $response = $this->controller->create($request, $this->mailer, $this->entityManager);

        $this->assertStringContainsString('repayment frequency: invalid enumeration', $response->getContent());
    }

    /**
     * Test that email is NOT sent when consent is false
     */
    public function testNoEmailSentWithoutConsent(): void
    {
        $payload = [
            'term' => 24,
            'principal' => 1000.0,
            'repayment_frequency' => 'weekly',
            'rate' => 3.0,
            'currency' => 'GBP',
            'consent_to_email' => false
        ];

        $request = new Request([], [], [], [], [], [], json_encode($payload));

        $this->mailer->expects($this->never())->method('send');

        $this->controller->create($request, $this->mailer, $this->entityManager);
    }
}
