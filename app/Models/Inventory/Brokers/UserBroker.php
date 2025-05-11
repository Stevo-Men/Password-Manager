<?php

namespace Models\Inventory\Brokers;

use Models\Inventory\Entities\User;
use Zephyrus\Database\DatabaseBroker;

class UserBroker extends DatabaseBroker
{
    public function findById(int $id): ?User
    {
        $data = $this->selectSingle("SELECT * FROM users WHERE id = ?", [$id]);

        $user = User::build($data);
        return $user;
    }

    public function findByEmailHash(string $emailHash): ?User
    {
        $data = $this->selectSingle(
            "SELECT * FROM users WHERE email_hash = ? LIMIT 1",
            [$emailHash]
        );

        if (!$data) {
            return null;
        }

        return User::build($data);
    }

    public function findByUsername(string $username): ?User
    {
        $data = $this->selectSingle(
            "SELECT * FROM users WHERE username = ? LIMIT 1",
            [$username]
        );

        $user = User::build($data);
        return $user;
    }

    public function findByEmail(string $email): ?User
    {
        $data = $this->selectSingle(
            "SELECT * FROM users WHERE email = ? LIMIT 1",
            [$email]
        );
        $user = User::build($data);
        return $user;
    }
    public function insert(User $user): int
    {
        $data = $this->selectSingle(
            "INSERT INTO users (username, firstname, lastname, email, password_hash, email_hash, salt) VALUES (?, ?, ?, ?, ?, ?, ?) RETURNING id",
            [$user->username, $user->firstName, $user->lastName, $user->email, $user->password_hash, $user->email_hash, $user->salt]
        );

        if (!$data) {
            throw new \Exception("User insertion failed");
        }

        return $data->id;
    }

    public function update(User $user): bool
    {
        $data = $this->selectSingle(
            "UPDATE users SET username = ?, email = ? WHERE id = ?",
            [$user->username, $user->email, $user->id]
        );

        if(!$data)
        {
            return false;
        }

        User::build($data);

        return true;
    }

    public function delete(int $id): bool
    {
        $data = $this->selectSingle("DELETE FROM users WHERE id = ?", [$id]);

        if(!$data)
        {
            return false;
        }

        User::build($data);

        return true;
    }
}