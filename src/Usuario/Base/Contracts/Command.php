<?php


namespace Da\Usuario\Base\Contracts;


interface Command
{
    /**
     * @return array
     */
    public function toArray(): array;
}
