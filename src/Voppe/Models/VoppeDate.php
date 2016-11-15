<?php

namespace Voppe\Models;

class VoppeDate {

    // The separator can be easily replaced, or multiple separators can be allowed
    const DatePattern = '/^([0-9]{4})\/([0-9]{2})\/([0-9]{2})$/';
    // The epoch must be a year after a leap year for the leap year math to work
    const EpochYear = 1901;
    const MonthLengths = [
        VoppeMonths::January => 31,
        VoppeMonths::February => 28.25,
        VoppeMonths::March => 31,
        VoppeMonths::April => 30,
        VoppeMonths::May => 31,
        VoppeMonths::June => 30,
        VoppeMonths::July => 31,
        VoppeMonths::August => 31,
        VoppeMonths::September => 30,
        VoppeMonths::October => 31,
        VoppeMonths::November => 30,
        VoppeMonths::December => 31,
    ];
    const DaysSinceYearStartInMonth = [
        VoppeMonths::January => 0,
        VoppeMonths::February => 31,
        VoppeMonths::March => 59,
        VoppeMonths::April => 90,
        VoppeMonths::May => 120,
        VoppeMonths::June => 151,
        VoppeMonths::July => 181,
        VoppeMonths::August => 212,
        VoppeMonths::September => 243,
        VoppeMonths::October => 273,
        VoppeMonths::November => 304,
        VoppeMonths::December => 334
    ];
    const DaysInYear = 365.25;
    const DaysInLeapYear = 366;
    const DaysBetweenLeapDay = VoppeDate::DaysInYear * 4;
    const DaysFromYearStartToLeapDay = 31 + 29;
    const LeapOffset = VoppeDate::DaysInLeapYear - VoppeDate::DaysFromYearStartToLeapDay;

    private $year;
    private $month;
    private $day;

    public function getYear(): int {
        return $this->year;
    }

    public function getMonth(): int {
        return $this->month;
    }

    public function getDay(): int {
        return $this->day;
    }

    private function __construct($year, $month, $day) {
        $this->year = intval($year);
        $this->month = intval($month);
        $this->day = intval($day);
    }

    /**
     * Returns the total amount of days since a defined epoch
     * 
     * @return int 
     */
    public function getDaysSinceEpoch(): int {
        // Calculate days from previous years (adds a day every four years to account for leap years)
        $days = floor(($this->getYear() - static::EpochYear) * 365.25);

        // Add days from current year
        $days += static::calculateDaysSinceYearStart($this->getYear(), $this->getMonth(), $this->getDay());

        return $days;
    }

    /**
     * Parses the string to a valid date entity
     * 
     * @param type $dateStr 
     * @return \Voppe\Models\VoppeDate
     */
    public static function parse($dateStr) {
        $date = null;
        $matches = array();
        $isValid = preg_match(static::DatePattern, $dateStr, $matches) === 1;
        if ($isValid === true) {
            list($_, $year, $month, $day) = $matches;
            $year = intval($year);
            $month = intval($month);
            $day = intval($day);

            if (static::isValidDate($year, $month, $day)) {
                $date = new VoppeDate($year, $month, $day);
            }
        }

        return $date;
    }

    /**
     * Check if the specified date exists on the calendar
     *
     * @param type $year
     * @param type $month
     * @param type $day
     * @return bool
     */
    public static function isValidDate($year, $month, $day): bool {
        $isValidMonth = static::isValidMonth($month);
        $isValidDay = static::isValidDay($year, $month, $day);

        return $isValidMonth === true && $isValidDay === true;
    }

    public static function isValidMonth($month): bool {
        return $month >= 1 && $month <= 12;
    }

    public static function isValidDay($year, $month, $day): bool {
        return $day >= 1 && $day <= static::calculateDaysInMonth($year, $month);
    }

    public static function isLeapYear($year): bool {
        return ($year % 4) === 0;
    }

    /**
     * Returns the amount of days in the specified month of the specified year
     * 
     * @param type $year
     * @param type $month
     * @return int
     */
    public static function calculateDaysInMonth($year, $month): int {
        $maxDay = 0;
        if ($month === VoppeMonths::February) {
            $maxDay = static::isLeapYear($year) ? 29 : 28;
        } elseif (array_key_exists($month, static::MonthLengths)) {
            $maxDay = static::MonthLengths[$month];
        }

        return $maxDay;
    }

    /**
     * Returns the amount of days since the year start (starts from 0 at January 1st)
     * 
     * @param type $year
     * @param type $month
     * @param type $day
     * @return int
     */
    public static function calculateDaysSinceYearStart($year, $month, $day): int {
        $days = $day - 1;
        $days += static::DaysSinceYearStartInMonth[$month];
        if ($month > VoppeMonths::February && static::isLeapYear($year)) {
            $days += 1;
        }

        return $days;
    }

    /**
     * Calculates the amount of leap days (29th February) between the two dates
     * 
     * @param type $daysSinceEpochStart
     * @param type $daysSinceEpochEnd
     * @return int
     */
    public static function calculateLeapDaysBetween($daysSinceEpochStart, $daysSinceEpochEnd): int {
        // By dividing the offsetted day by the days between leap days, we get a value that, if floored, can tell us which leap "interval" that day belongs to.
        $leapStart = floor(($daysSinceEpochStart + VoppeDate::LeapOffset) / VoppeDate::DaysBetweenLeapDay);
        $leapEnd = floor(($daysSinceEpochEnd + VoppeDate::LeapOffset) / VoppeDate::DaysBetweenLeapDay);

        // By subtracting the interval values between them, we get the amount of leap days.
        return abs($leapEnd - $leapStart);
    }

}
