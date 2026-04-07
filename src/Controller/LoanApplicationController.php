<?php

declare(strict_types=1);

namespace App\Controller;

use App\Calculator\LoanCalculator;
use App\Entity\LoanApplication;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Component\Routing\Attribute\Route;
use function array_merge;
use function file_put_contents;
use function is_array;
use function is_bool;
use function is_float;
use function is_int;
use function is_string;
use function json_decode;
use function json_encode;
use function serialize;
use function strlen;

class LoanApplicationController extends AbstractController
{
    use LoanCalculator;

    #[Route('/loan-application/create', methods: ['POST'])]
    public function create(Request $request, MailerInterface $mailer, EntityManagerInterface $entityManager)
    {
        $body = json_decode($request->getContent(), true);
        if (is_array($body)) {
            $term = $body['term'];
            if (!is_int($term)) {
                return new JsonResponse('term: not int');
            }
            $principal = $body['principal'];
            if (!is_float($principal) && !is_int($principal)) {
                return new JsonResponse('principal: not float');
            }
            $repaymentFrequency = $body['repayment_frequency'];
            if (is_string($repaymentFrequency)) {
                if ($repaymentFrequency !== 'monthly' && $repaymentFrequency !== 'weekly' && $repaymentFrequency !== 'fortnightly') {
                    return new JsonResponse('repayment frequency: invalid enumeration');
                }
            } else {
                return new JsonResponse('repayment frequency: not string');
            }
            $rate = $body['rate'];
            if (!is_float($rate)) {
                return new JsonResponse('rate: not float');
            }
            $currency = $body['currency'];
            if (!is_string($currency) || strlen($currency) !== 3) {
                return new JsonResponse('repayment frequency: not currency code');
            }
            $consentToEmail = $body['consent_to_email'];
            if (!is_bool($consentToEmail)) {
                return new JsonResponse('consent to email: not boolean');
            }

            $la = new LoanApplication();
            $la->setPrincipal($principal);
            $la->setTerm($term);
            $la->setRepaymentFrequency($repaymentFrequency);
            $la->setCurrency($currency);
            $la->setRate((string)$rate);

            $calculations = $this->calculateLoan($body);
            $la->setRepayment((string)$calculations['payment']);
            $la->setTotalInterest((string)$calculations['total_interest']);
            $la->setTotalCost((string)$calculations['total_cost']);

            $entityManager->persist($la);
            $entityManager->flush();

            $response = array_merge($body, $calculations);

            file_put_contents(__DIR__ . '/../../var/batch/loanApplication' . $la->getId() . '.json', serialize($response));

            if ($consentToEmail) {
                $email = (new Email())
                    ->from('no-reply@example.com')
                    ->to($body['email'] ?? 'customer@example.com')
                    ->subject('Loan Application Received')
                    ->text(
                        <<<TXT
                        Hi there,

                        Thanks for applying for a loan with us. It is now being considered.
                        TXT,
                    );
                $mailer->send($email);
            }
            return new Response(json_encode($response));
        }
        return new Response(json_encode(['Invalid JSON']));
    }
}
