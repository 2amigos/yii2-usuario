<?php
namespace Da\User\Validator;

use Da\User\Contracts\ValidatorInterface;

class TimeZoneValidator implements ValidatorInterface
{
    protected $timezone;

    public function __construct($timezone)
    {
        $this->timezone = $timezone;
    }

    public function validate()
    {
        return in_array($this->timezone, timezone_identifiers_list());
    }

}
