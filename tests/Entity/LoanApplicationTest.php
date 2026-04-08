<?php

declare(strict_types=1);

namespace App\Tests\Entity;

use App\Entity\LoanApplication;
use PHPUnit\Framework\TestCase;

class LoanApplicationTest extends TestCase
{
    public function testGettersAndSetters(): void
    {
        $la = new LoanApplication();

        $la->setTerm(12);
        $this->assertEquals(12, $la->getTerm());

        $la->setPrincipal(5000.0);
        $this->assertEquals(5000.0, $la->getPrincipal());

        $la->setRepaymentFrequency('monthly');
        $this->assertEquals('monthly', $la->getRepaymentFrequency());

        $la->setRate('5.5');
        $this->assertEquals('5.5', $la->getRate());

        $la->setCurrency('USD');
        $this->assertEquals('USD', $la->getCurrency());

        $la->setRepayment('100.00');
        $this->assertEquals('100.00', $la->getRepayment());

        $la->setTotalInterest('50.00');
        $this->assertEquals('50.00', $la->getTotalInterest());

        $la->setTotalCost('5050.00');
        $this->assertEquals('5050.00', $la->getTotalCost());

        // Id is usually set by Doctrine, but we can check it's null by default
        $this->assertNull($la->getId());
    }
}
