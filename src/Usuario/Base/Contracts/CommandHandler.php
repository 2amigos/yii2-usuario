<?php


namespace Da\Usuario\Base\Contracts;

interface CommandHandler
{
    /**
     * @param Command $command
     */
    public function handle(Command $command): void;
}
