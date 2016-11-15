<?php

namespace Voppe\Models;

use Voppe\Interfaces\VoppeDateInterface;

class VoppeDateModel implements VoppeDateInterface {

    private $startDate;
    private $endDate;

    public function __construct($startDate, $endDate) {
        $this->setStartDate($startDate);
        $this->setEndDate($endDate);
    }

    public function setStartDate($date) {
        if (!$this->isValidDate($date)) {
            throw new \Exception('Start date is not a valid date');
        }

        $this->startDate = $date;
    }

    public function getStartDate() {
        return $this->startDate;
    }

    public function setEndDate($date) {
        if (!$this->isValidDate($date)) {
            throw new \Exception('End date is not a valid date');
        }

        $this->endDate = $date;
    }

    public function getEndDate() {
        return $this->endDate;
    }

    public function isValidDate($dateStr) {
        return VoppeDate::parse($dateStr) !== null;
    }

    public function diff() {
        $isInvert = false;
        $startDate = VoppeDate::parse($this->startDate);
        $endDate = VoppeDate::parse($this->endDate);

        $daysFromEpochStart = $startDate->getDaysSinceEpoch();
        $daysFromEpochEnd = $endDate->getDaysSinceEpoch();

        // Invert the values if needed
        if ($daysFromEpochStart > $daysFromEpochEnd) {
            $isInvert = true;
            list($startDate, $endDate) = array($endDate, $startDate);
            list($daysFromEpochStart, $daysFromEpochEnd) = array($daysFromEpochEnd, $daysFromEpochStart);
        }

        $leapDays = VoppeDate::calculateLeapDaysBetween($daysFromEpochStart, $daysFromEpochEnd);
        $diffTotalDays = $daysFromEpochEnd - $daysFromEpochStart;
        $diffTotalMinusLeap = $diffTotalDays - $leapDays;

        // Subtracting the leap days from the total days should normalize the situation to allow us to simply divide by 365
        $diffYears = floor($diffTotalMinusLeap / 365);

        // Now we get the remainder of the previous operation
        $remainingDays = $diffTotalMinusLeap % 365;

        // Account for the remaining leap days
        $remainingLeapDays = VoppeDate::calculateLeapDaysBetween($daysFromEpochStart + $diffYears * 365, $daysFromEpochEnd);

        // Calculate the months using the remaining days ($diffDays gets decreased accordingly, leaving the remaining days)
        $diffDays = $remainingDays + $remainingLeapDays;
        $diffMonths = $this->calculateMonthsInDays($diffDays, $startDate->getMonth(), $startDate->getYear() + $diffYears);

        return (object) array(
                    'years' => intval(abs($diffYears)),
                    'months' => intval(abs($diffMonths)),
                    'days' => intval(abs($diffDays)),
                    'total_days' => intval(abs($diffTotalDays)),
                    'invert' => $isInvert
        );
    }

    private function calculateMonthsInDays(&$diffDays, $currentMonth, $currentYear) {
        $months = 0;

        $daysInCurrentMonth = VoppeDate::calculateDaysInMonth($currentYear, $currentMonth);
        if ($diffDays >= $daysInCurrentMonth) {

            $diffDays -= $daysInCurrentMonth;
            $currentMonth++;
            if ($currentMonth > VoppeMonths::December) {
                $currentMonth = VoppeMonths::January;
                $currentYear++;
            }

            $months = $this->calculateMonthsInDays($diffDays, $currentMonth, $currentYear) + 1;
        }

        return $months;
    }

}
