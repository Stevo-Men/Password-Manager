<?php

namespace Models\Inventory\Entities;

use Models\Core\Entity;

class Credential extends Entity
{
    public int $id;
    public int $userId;
    public string $title;
    public string $url;
    public string $login;
    public string $passwordEncrypted;
    public string $notes;

    public string $lastLogin;
}