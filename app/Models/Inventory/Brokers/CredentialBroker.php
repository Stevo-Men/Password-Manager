<?php

namespace Models\Inventory\Brokers;

use Zephyrus\Database\DatabaseBroker;

class CredentialBroker extends DatabaseBroker
{

    public function findByUser(int $userId): array
    {
        return $this->select(
            "SELECT * FROM credentials WHERE user_id = ? ORDER BY title",
            [$userId]
        );
    }
}