<?php namespace Models\Inventory\Entities;

use Models\Core\Entity;

class User extends Entity
{
    public int $id;
    public string $firstName;
    public string $lastName;
    public string $username;
    public string $email;
    public string $password_hash;
    public string $email_hash;
    public string $salt;
    public string $created_at;
    public string $updated_at;
    public ?string $last_login = null;
}