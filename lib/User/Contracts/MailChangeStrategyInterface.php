<?php

namespace Da\User\Contracts;

interface MailChangeStrategyInterface extends StrategyInterface
{
    const TYPE_INSECURE = 0;
    const TYPE_DEFAULT = 1;
    const TYPE_SECURE = 2;
}
