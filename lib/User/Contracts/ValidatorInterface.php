<?php

namespace Da\User\Contracts;

interface ValidatorInterface
{
    /**
     * @return bool
     */
    public function validate();
}
