<?php namespace Models\Inventory\Entities;

use Models\Core\Entity;
use Models\Inventory\Services\Cryptography\CryptographyService;

class User extends Entity
{
    public int $id;
    public string $firstname;
    public string $lastname;
    public string $username;
    public string $email;
    public string $password_hash;
    public string $email_hash;
    public string $salt;
    public string $created_at;
    public string $updated_at;
    public ?string $last_login = null;





    public function decryptUserInfo(User $user,string $userKey): void
    {
        $this->firstname = CryptographyService::decrypt($this->firstname, $userKey);
        $this->lastname = CryptographyService::decrypt($this->lastname, $userKey);
        $this->username = CryptographyService::decrypt($this->username, $userKey);
        $this->email = CryptographyService::decrypt($this->email, $userKey);
    }

}