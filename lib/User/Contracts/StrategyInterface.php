<?php
namespace Da\User\Contracts;

interface StrategyInterface
{
    /**
     * @return bool
     */
    public function run();
}
