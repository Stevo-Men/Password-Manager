<?php

namespace Models\Inventory\Entities;

use Models\Core\Entity;

class Credential extends Entity
{
    public int $id;
    public int $user_id;
    public string $title;
    public string $url;
    public string $login;
    public string $password_encrypted;
    public string $notes;
    public string $created_at;
    public string $updated_at;
    public string $last_login;
}