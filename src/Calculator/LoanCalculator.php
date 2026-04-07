<?php

declare(strict_types=1);

namespace App\Calculator;

trait LoanCalculator
{
    public function calculateLoan($loan)
    {
        $years = (int)$loan['term'];
        $annualRate = (float)$loan['rate'];
        $repaymentFrequency = $loan['repayment_frequency'];

        $monthlyRate = ($annualRate / 100) / 12;

        $f = 0;

        switch ($repaymentFrequency) {
            case 'monthly':
                $f = 12;
                break;
            case 'weekly':
                $f = 52;
                break;
            case 'fortnightly':
                $f = 26;
                break;
        }

        $numberOfPayments = $years * $f;

        if ($monthlyRate <= 0) {
            $payment = ((float)$loan['principal']) / $numberOfPayments;
        } else {
            $compound = pow(1 + $monthlyRate, $numberOfPayments);
            $payment = ((float)$loan['principal']) * ($monthlyRate * $compound) / ($compound - 1);
        }

        $totalCost = $payment * $numberOfPayments;
        $totalInterest = $totalCost - ((float)$loan['principal']);

        return [
            'payment' => round($payment, 2),
            'total_interest' => round($totalInterest, 2),
            'total_cost' => round($totalCost, 2),
        ];
    }
}
