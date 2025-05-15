<?php

namespace Models\Inventory\Brokers;

use Models\Inventory\Entities\User;
use Zephyrus\Database\DatabaseBroker;

class ProfileBroker extends DataBaseBroker
{

    public function findById(int $id): ?User
    {
        $data = $this->selectSingle("SELECT * FROM users WHERE id = ?", [$id]);

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

}