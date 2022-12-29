<?php

use Da\User\Helper\TimezoneHelper;
use Da\User\Validator\TimeZoneValidator;
use yii\helpers\ArrayHelper;

/**
 * Testing the Timezone generator functions
 */
class TimeZoneTest extends \Codeception\Test\Unit
{
    // Basic test to check the function works
    public function testTimezoneHelper()
    {
        $alltz = (new TimezoneHelper)->getAll();
        $this->assertTrue(in_array("Europe/Rome", ArrayHelper::getColumn($alltz, "timezone")));
        $this->assertTrue(in_array("0100", ArrayHelper::getColumn($alltz, "offset")));
    }

    // Test with minPasswordRequirements equal to an empty array (= password without requirements)
    public function testTimeZoneValidator()
    {
        $v = Yii::createObject(TimeZoneValidator::class, ["Europe/Rome"]);
        $this->assertTrue($v->validate());
    }
}
