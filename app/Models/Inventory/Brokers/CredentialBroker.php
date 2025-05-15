<?php

namespace Models\Inventory\Brokers;

use Zephyrus\Database\DatabaseBroker;
use stdClass;

class CredentialBroker extends DatabaseBroker
{

    public function findByUser(int $userId): array
    {
        return $this->select(
            "SELECT * FROM credentials WHERE user_id = ? ORDER BY title",
            [$userId]
        );
    }

    public function insert(stdClass $credential): int
    {

        $row = $this->query(
            "INSERT INTO credentials 
                (user_id, title, url, login, password_encrypted, notes, created_at, updated_at)
             VALUES (?, ?, ?, ?, ?, ?, now(), now())
             RETURNING id",
            [
                $credential->user_id,
                $credential->title,
                $credential->url,
                $credential->login,
                $credential->password_encrypted,
                $credential->notes
            ]
        );
        return (int) $row->id;
    }

    public function delete(int $id): bool
    {
        $this->query(
            "DELETE FROM credentials WHERE id = ?",
            [$id]
        );
        return $this->getLastAffectedCount() > 0;
    }


}