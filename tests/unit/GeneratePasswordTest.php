<?php

use Da\User\Helper\SecurityHelper;
use yii\base\Security;

/**
 * Testing for the `SecurityHelper.generatePassword()` function.
 * Note that this test considers these sets of characters:
 * $sets = [
 *      'lower' => 'abcdefghjkmnpqrstuvwxyz',
 *      'upper' => 'ABCDEFGHJKMNPQRSTUVWXYZ',
 *      'digit' => '123456789',
 *      'special' => '!#$%&*+,-.:;<=>?@_~'
 * ];
 */
class GeneratePasswordTest extends \Codeception\Test\Unit
{
    const ITERATIONS = 10000;

    // Test with minPasswordRequirements equal to null (get default value/parameter)
    public function testNullParameter ()
    {
        $length = 8;
        $minPasswordRequirements = null;
        // Helper
        $securityHelper = new SecurityHelper(new Security()); // Empty security (it does not matter)
        // Check password correctness
        $ok = true;
        for ($i = 0; $i < self::ITERATIONS; $i++) {
            $password = $securityHelper->generatePassword($length, $minPasswordRequirements);
            $result = preg_match('/\A(?=(.*\d){1})(?=(?:[^a-z]*[a-z]){1})(?=(?:[^A-Z]*[A-Z]){1})[0-9a-zA-Z!#$%&*+,-.:;<=>?@_~]{8,}\z/', $password);
            if ($result === 0) {
                $ok = false;
                break;
            }
        }
        $this->assertTrue($ok);
    }

    // Test with minPasswordRequirements equal to an empty array (= password without requirements)
    public function testEmptyParameter ()
    {
        $length = 8;
        $minPasswordRequirements = [];
        // Helper
        $securityHelper = new SecurityHelper(new Security()); // Empty security (it does not matter)
        // Check password correctness
        $ok = true;
        for ($i = 0; $i < self::ITERATIONS; $i++) {
            $password = $securityHelper->generatePassword($length, $minPasswordRequirements);
            $result = preg_match('/\A[0-9a-zA-Z!#$%&*+,-.:;<=>?@_~]{8,}\z/', $password);
            if ($result === 0) {
                $ok = false;
                break;
            }
        }
        $this->assertTrue($ok);
    }

    // Test with many lowercase characters, one uppercase character, one digit and one special character
    public function testManyLowercaseCharacter ()
    {
        // Function parameters
        $length = 8;
        $minPasswordRequirements = [
            'min' => 10,
            'special' => 1,
            'digit' => 1,
            'upper' => 1,
            'lower' => 5
        ];
        // Helper
        $securityHelper = new SecurityHelper(new Security()); // Empty security (it does not matter)
        // Check password correctness
        $ok = true;
        for ($i = 0; $i < self::ITERATIONS; $i++) {
            $password = $securityHelper->generatePassword($length, $minPasswordRequirements);
            $result = preg_match('/\A(?=(.*\d){1})(?=(?:[^a-z]*[a-z]){5})(?=(?:[^A-Z]*[A-Z]){1})(?=(?:[0-9a-zA-Z]*[!#$%&*+,-.:;<=>?@_~]){1})[0-9a-zA-Z!#$%&*+,-.:;<=>?@_~]{10,}\z/', $password);
            if ($result === 0) {
                $ok = false;
                break;
            }
        }
        $this->assertTrue($ok);
    }

    // Test with many special characters, one uppercase character, one digit
    public function testManySpecialCharacter ()
    {
        // Function parameters
        $length = 10;
        $minPasswordRequirements = [
            'min' => 10,
            'special' => 6,
            'digit' => 1,
            'upper' => 1,
        ];
        // Helper
        $securityHelper = new SecurityHelper(new Security()); // Empty security (it does not matter)
        // Check password correctness
        $ok = true;
        for ($i = 0; $i < self::ITERATIONS; $i++) {
            $password = $securityHelper->generatePassword($length, $minPasswordRequirements);
            $result = preg_match('/\A(?=(.*\d){1})(?=(?:[^A-Z]*[A-Z]){1})(?=(?:[0-9a-zA-Z]*[!#$%&*+,-.:;<=>?@_~]){6})[0-9a-zA-Z!#$%&*+,-.:;<=>?@_~]{10,}\z/', $password);
            if ($result === 0) {
                $ok = false;
                break;
            }
        }
        $this->assertTrue($ok);
    }

    // Test with a long password and no requirements
    public function testLongPassword ()
    {
        // Function parameters
        $length = 20;
        $minPasswordRequirements = [];
        // Helper
        $securityHelper = new SecurityHelper(new Security()); // Empty security (it does not matter)
        // Check password correctness
        $ok = true;
        for ($i = 0; $i < self::ITERATIONS; $i++) {
            $password = $securityHelper->generatePassword($length, $minPasswordRequirements);
            $result = preg_match('/\A[0-9a-zA-Z!#$%&*+,-.:;<=>?@_~]{20,}\z/', $password);
            if ($result === 0) {
                $ok = false;
                break;
            }
        }
        $this->assertTrue($ok);
    }

    // Test with random requirements
    public function testRandomRequirements ()
    {
        // Function parameters
        $length = 8;
        $minPasswordRequirements = [
            'min' => 10,
            'special' => 4,
            'digit' => 3,
            'upper' => 2,
            'lower' => 1
        ];
        // Helper
        $securityHelper = new SecurityHelper(new Security()); // Empty security (it does not matter)
        // Check password correctness
        $ok = true;
        for ($i = 0; $i < self::ITERATIONS; $i++) {
            $password = $securityHelper->generatePassword($length, $minPasswordRequirements);
            $result = preg_match('/\A(?=(.*\d){3})(?=(?:[^a-z]*[a-z]){1})(?=(?:[^A-Z]*[A-Z]){2})(?=(?:[0-9a-zA-Z]*[!#$%&*+,-.:;<=>?@_~]){4})[0-9a-zA-Z!#$%&*+,-.:;<=>?@_~]{10,}\z/', $password);
            if ($result === 0) {
                $ok = false;
                break;
            }
        }
        $this->assertTrue($ok);
    }
}