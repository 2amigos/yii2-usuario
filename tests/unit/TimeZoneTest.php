<?php

use Da\User\Helper\TimezoneHelper;
use Da\User\Validator\TimeZoneValidator;

/**
 * Testing the Timezone generator functions
 */
class TimeZoneTest extends \Codeception\Test\Unit
{
    // Basic test to check the function works
    public function testTimezoneHelper()
    {
        $alltz = (new TimezoneHelper)->getAll();
        $this->assertTrue(in_array("Europe/Rome", array_keys($alltz)));
    }

    // Test with minPasswordRequirements equal to an empty array (= password without requirements)
    public function testTimeZoneValidator()
    {
        $v = Yii::createObject(TimeZoneValidator::class, ["Europe/Rome"]);
        $this->assertTrue($v->validate());
    }
}
