<?php

namespace Models\Inventory\Brokers;

use Models\Inventory\Entities\Credential;
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

    public function findById(int $id): ?Credential
    {
        $data = $this->selectSingle(
            "SELECT
                id,
                user_id,
                title,
                url,
                login,
                password_encrypted,
                notes,
                created_at,
                updated_at
             FROM credentials
             WHERE id = ?",
            [$id]
        );
        return $data ? Credential::build($data) : null;
    }


    public function insertCredential(Credential $credential): int
    {

        $this->selectSingle(
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
        return $this->getLastAffectedCount() > 0;
    }

    public function updateCredential(Credential $credential): int
    {
        $this->selectSingle(
            "UPDATE credentials SET
                user_id = ?, title = ?, url = ?, login = ?, password_encrypted = ?, notes = ?, updated_at = NOW()
                    WHERE id = ?",
            [
                $credential->user_id,
                $credential->title,
                $credential->url,
                $credential->login,
                $credential->password_encrypted,
                $credential->notes,
                $credential->id
            ]
        );
        return $this->getLastAffectedCount() > 0;
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