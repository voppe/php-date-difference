<?php

namespace Voppe\Tests\Models;

use \DateTime;
use PHPUnit_Framework_TestCase;
use Voppe\Models\VoppeDate;

class VoppeDateTest extends PHPUnit_Framework_TestCase {

    protected function setUp() {
    }

    protected function tearDown() {
    }
    
    public function testDaysSinceYearStart() {
        $this->assertSame(VoppeDate::calculateDaysSinceYearStart(2015, 1, 1), 0);
        $this->assertSame(VoppeDate::calculateDaysSinceYearStart(2015, 1, 21), 20);
        $this->assertSame(VoppeDate::calculateDaysSinceYearStart(2015, 3, 1), 31+28);
        $this->assertSame(VoppeDate::calculateDaysSinceYearStart(2016, 3, 1), 31+29);
    }

    public function testIsValidDateMonth() {
        for($month = 1; $month <= 12; $month++) {
            $this->assertTrue(VoppeDate::isValidDate(2015, $month, 25));
        }
        
        $this->assertFalse(VoppeDate::isValidDate(2015, 0, 25));
        $this->assertFalse(VoppeDate::isValidDate(2015, 13, 25));
    }

    public function testIsValidDateDay() {
        // Should always be valid between 1 and 28 (included)
        for($day = 1; $day <= 28; $day++) {
            $this->assertTrue(VoppeDate::isValidDate(2015, 1, $day));
        }
        
        $this->assertFalse(VoppeDate::isValidDate(2015, 1, 0));
        $this->assertFalse(VoppeDate::isValidDate(2015, 1, 32));
    }
    
    public function testIsValidDateMonthLength() {
        $months = [
            "long" => [1, 3, 5, 7, 8, 10, 12],
            "short" => [4, 6, 9, 11]
        ];
        
        foreach($months["long"] as $month) {
            $this->assertTrue(VoppeDate::isValidDate(2015, $month, 31));          
        }
        
        foreach($months["short"] as $month) {
            $this->assertTrue(VoppeDate::isValidDate(2015, $month, 30));  
            $this->assertFalse(VoppeDate::isValidDate(2015, $month, 31));
        }
        
        $this->assertFalse(VoppeDate::isValidDate(2015, 2, 30));
    }
    
    public function testIsValidDateLeapYear() {
        $this->assertFalse(VoppeDate::isValidDate(2015, 2, 29));
        $this->assertTrue(VoppeDate::isValidDate(2016, 2, 29));        
    }
    
    public function testCalculateLeapDaysBetween() {
        $this->assertSame(VoppeDate::calculateLeapDaysBetween(VoppeDate::parse('2012/02/28')->getDaysSinceEpoch(), VoppeDate::parse('2012/03/01')->getDaysSinceEpoch()), 1);
        $this->assertSame(VoppeDate::calculateLeapDaysBetween(VoppeDate::parse('2013/02/28')->getDaysSinceEpoch(), VoppeDate::parse('2013/03/01')->getDaysSinceEpoch()), 0);
        $this->assertSame(VoppeDate::calculateLeapDaysBetween(VoppeDate::parse('2016/02/28')->getDaysSinceEpoch(), VoppeDate::parse('2016/03/01')->getDaysSinceEpoch()), 1);
        $this->assertSame(VoppeDate::calculateLeapDaysBetween(VoppeDate::parse('2016/03/01')->getDaysSinceEpoch(), VoppeDate::parse('2016/03/01')->getDaysSinceEpoch()), 0);
        $this->assertSame(VoppeDate::calculateLeapDaysBetween(VoppeDate::parse('2011/01/01')->getDaysSinceEpoch(), VoppeDate::parse('2016/03/01')->getDaysSinceEpoch()), 2);
    }
}
