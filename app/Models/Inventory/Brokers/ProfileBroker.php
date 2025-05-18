<?php

namespace Models\Inventory\Brokers;

use Models\Inventory\Entities\User;
use Zephyrus\Database\DatabaseBroker;

class ProfileBroker extends DataBaseBroker
{


    public function findUserById(int $id): ?User
    {
        $data = $this->selectSingle(
            "SELECT firstname, lastname, username, email, password_hash, avatar_path,  created_at, updated_at, last_login FROM users WHERE id = ?",
            [$id]
        );

        $user = User::build($data);
        return $user;
    }



    public function update(User $user): bool
    {
        $data = $this->selectSingle(
            "UPDATE users SET firstname = ?, lastname = ?, username = ?  WHERE id = ?",
            [$user->firstName,$user->lastName,$user->username, $user->id]
        );

        if(!$data)
        {
            return false;
        }

        User::build($data);

        return true;
    }

    public function updateUsername(int $id, string $encryptedUsername): bool
    {
        $this->selectSingle(
            "UPDATE users SET username = ?, updated_at = now() WHERE id = ?",
            [$encryptedUsername, $id]
        );
        return $this->getLastAffectedCount() > 0;
    }

    public function updateEncryptedProfile(User $user, int $userId): bool
    {
        $this->selectSingle(
            "UPDATE users SET
                firstname = ?,
                lastname = ?,
                username = ?,
                email = ?,
                password_hash = ?,
                salt = ?,
                updated_at = now()
             WHERE id = ?",
            [
                $user->firstname,
                $user->lastname,
                $user->username,
                $user->email,
                $user->password_hash,
                $user->salt,
                $userId
            ]
        );
        return $this->getLastAffectedCount() > 0;
    }

    public function updateAvatarPath(int $id, string $path): bool
    {
        $this->rawQuery(
            "UPDATE users SET avatar_path = ?, updated_at = now() WHERE id = ?",
            [$path, $id]
        );
        return $this->getLastAffectedCount() > 0;
    }



}