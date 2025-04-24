<?php namespace Models\Inventory\Entities;

use DateTimeImmutable;
use Models\Core\Entity;

class User extends Entity
{
    public int $id;
    public string $username;
    public string $email;
    public string $passwordHash;
    public string $createdAt;
    public string $updatedAt;
    public string $lastLogin;
}