<?php

namespace Voppe\Tests\Models;

use \DateTime;
use PHPUnit_Framework_TestCase;
use Voppe\Models\VoppeDateModel;

class VoppeDateModelTest extends PHPUnit_Framework_TestCase {

    private $voppeDate;
    private $startDate = '2012/02/29';
    private $endDate = '2016/12/25';

    protected function setUp() {
        $this->voppeDate = new VoppeDateModel($this->startDate, $this->endDate);
    }

    protected function tearDown() {
        unset($this->voppeDate);
    }

    public function testGetterDates() {
        $this->assertSame($this->voppeDate->getStartDate(), $this->startDate);
        $this->assertSame($this->voppeDate->getEndDate(), $this->endDate);
    }
    
    public function testSetterDates() {
        $newStartDate = '2011/02/12';
        $newEndDate = '2010/01/10';
        $this->voppeDate->setStartDate($newStartDate);
        $this->voppeDate->setEndDate($newEndDate);
        $this->assertSame($this->voppeDate->getStartDate(), $newStartDate);
        $this->assertSame($this->voppeDate->getEndDate(), $newEndDate);
    }

    public function testIsValidDate() {
        $this->assertTrue($this->voppeDate->isValidDate('2015/12/25'));   
        $this->assertFalse($this->voppeDate->isValidDate('FAKE/12/25'));
        $this->assertFalse($this->voppeDate->isValidDate('2015/FA/25'));
        $this->assertFalse($this->voppeDate->isValidDate('2015/12/KE'));    
    }

    public function testIsValidDateFormat() {
        $this->assertTrue($this->voppeDate->isValidDate('0001/12/25'));
        $this->assertTrue($this->voppeDate->isValidDate('2015/01/01'));
        $this->assertTrue($this->voppeDate->isValidDate('2015/01/25'));        
        $this->assertFalse($this->voppeDate->isValidDate('1/12/25'));
        $this->assertFalse($this->voppeDate->isValidDate('2015/1/25'));
        $this->assertFalse($this->voppeDate->isValidDate('2015/01/1')); 
    }

    public function testIsValidDateSeparator() {
        $this->assertTrue($this->voppeDate->isValidDate('2015/12/25'));      
        $this->assertFalse($this->voppeDate->isValidDate('2015-12-25'));        
    }
    
    public function testDiffSame() {
        $this->_testDiff(new VoppeDateModel('2012/02/29', '2012/02/29'));
    }
    
    public function testDiffYears() {
        $this->_testDiff(new VoppeDateModel('2011/01/01', '2012/01/01'));
        $this->_testDiff(new VoppeDateModel('2011/01/01', '2012/12/30'));
        $this->_testDiff(new VoppeDateModel('2010/01/01', '2012/12/31'));
        $this->_testDiff(new VoppeDateModel('2012/12/31', '2013/01/01'));
    }
    
    public function testDiffMonths() {
        $this->_testDiff(new VoppeDateModel('2011/01/01', '2012/01/31'));
        $this->_testDiff(new VoppeDateModel('2011/01/01', '2012/02/01'));
        $this->_testDiff(new VoppeDateModel('2011/01/01', '2012/02/28'));
    }
    
    public function testDiffFebruary() {
        $this->_testDiff(new VoppeDateModel('2012/02/28', '2012/03/01'));
        $this->_testDiff(new VoppeDateModel('2013/02/28', '2013/03/01'));
        $this->_testDiff(new VoppeDateModel('2016/02/28', '2016/03/01'));
        $this->_testDiff(new VoppeDateModel('2016/03/01', '2016/03/01'));
    }
    
    public function testDiffLeapYear() {
        $this->_testDiff(new VoppeDateModel('2012/02/29', '2016/02/29'));
    }
    
    public function testDiffLong() {
        $this->_testDiff(new VoppeDateModel('1992/01/02', '2016/02/01'));
    }
    
    
    public function testDiffInvert() {
        $this->_testDiff(new VoppeDateModel('2013/01/01', '2014/12/10'));
        $this->_testDiff(new VoppeDateModel('2014/01/01', '2013/12/10'));
    }

    private function _testDiff($voppeDate) {
        $voppeDateDiff = $voppeDate->diff();
        $dateDiff = $this->diff($voppeDate->getStartDate(), $voppeDate->getEndDate());

        $this->assertSame($voppeDateDiff->years, $dateDiff->y);
        $this->assertSame($voppeDateDiff->months, $dateDiff->m);
        $this->assertSame($voppeDateDiff->days, $dateDiff->d);
        $this->assertSame($voppeDateDiff->total_days, $dateDiff->days);
        $this->assertSame($voppeDateDiff->invert, $dateDiff->invert === 1);
    }

    private function diff($startDate, $endDate) {
        $startDate = DateTime::createFromFormat('Y/m/d', $startDate);
        $endDate = DateTime::createFromFormat('Y/m/d', $endDate);

        return $startDate->diff($endDate);
    }
}
