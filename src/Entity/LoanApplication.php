<?php

namespace App\Entity;

use App\Repository\LoanApplicationRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: LoanApplicationRepository::class)]
class LoanApplication
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column]
    private ?int $term = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 4)]
    private ?string $principal = null;
    /**
     * - Weekly
     * - Monthly
     * - Fortnightly
     */
    #[ORM\Column]
    private ?string $repaymentFrequency = null;

    #[ORM\Column(type: Types::STRING, length: 3)]
    private ?string $currency = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 12, scale: 10)]
    private ?string $rate = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 4)]
    private ?string $repayment = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 4)]
    private ?string $totalInterest = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 4)]
    private ?string $totalCost = null;

    public function getId()
    {
        return $this->id;
    }

    public function getTerm()
    {
        return $this->term;
    }

    public function setTerm($term)
    {
        $this->term = $term;

        return $this;
    }

    public function getRepaymentFrequency()
    {
        return $this->repaymentFrequency;
    }

    /**
     * @param $repaymentFrequency
     * @return $this
     */
    public function setRepaymentFrequency($repaymentFrequency)
    {
        $this->repaymentFrequency = $repaymentFrequency;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getCurrency()
    {
        return $this->currency;
    }

    public function setCurrency(?string $currency)
    {
        $this->currency = $currency;

        return $this;
    }

    public function getRate()
    {
        return $this->rate;
    }

    public function setRate(string $rate)
    {
        $this->rate = $rate;

        return $this;
    }

    public function getRepayment()
    {
        return $this->repayment;
    }

    public function setRepayment($repayment)
    {
        $this->repayment = $repayment;

        return $this;
    }

    public function getTotalInterest()
    {
        return $this->totalInterest;
    }

    public function setTotalInterest($totalInterest)
    {
        $this->totalInterest = $totalInterest;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getTotalCost()
    {
        return $this->totalCost;
    }

    public function setTotalCost($totalCost)
    {
        $this->totalCost = $totalCost;

        return $this;
    }

    public function setPrincipal($principal)
    {
        $this->principal = $principal;
    }

    public function getPrincipal(): ?string
    {
        return $this->principal;
    }
}
