<?php

namespace Da\User\Traits;

trait AuthClientUserIdTrait
{
    /**
     * @see \Da\User\Contracts\AuthClientInterface::getUserId()
     */
    public function getUserId()
    {
        return $this->getUserAttributes()['id'] ?? null;
    }
}
