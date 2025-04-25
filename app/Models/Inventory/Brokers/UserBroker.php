<?php

namespace Models\Inventory\Brokers;

use Zephyrus\Database\DatabaseBroker;

class UserBroker extends DatabaseBroker
{
    public function findAll(): array
    {
        return $this->select("SELECT * FROM users ORDER BY id");
    }

    public function findById(int $id): ?\stdClass
    {
        return $this->selectSingle("SELECT * FROM users WHERE id = ?", [$id]);
    }



    public function findByUsername(string $username): ?\stdClass
    {
        return $this->selectSingle(
            "SELECT * FROM users WHERE username = ? LIMIT 1",
            [$username]
        );
    }

    public function findByEmail(string $email): ?\stdClass
    {
        return $this->selectSingle(
            "SELECT * FROM users WHERE email = ? LIMIT 1",
            [$email]
        );
    }
    public function insert(\stdClass $user): int
    {
        $row = $this->query(
            "INSERT INTO users (username, email, password_hash) VALUES (?, ?, ?) RETURNING id",
            [$user->username, $user->email, $user->password_hash]
        );
        return $row->id;
    }

    public function update(\stdClass $user): bool
    {
        return $this->execute(
            "UPDATE users SET username = ?, email = ? WHERE id = ?",
            [$user->username, $user->email, $user->id]
        );
    }

    public function delete(int $id): bool
    {
        return $this->execute("DELETE FROM users WHERE id = ?", [$id]);
    }
}