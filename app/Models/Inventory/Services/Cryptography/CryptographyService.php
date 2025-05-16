<?php

namespace Models\Inventory\Services\Cryptography;

use Zephyrus\Security\Cryptography;
use Zephyrus\Core\Session;

class CryptographyService
{
    public function encrypt(string $text, string $key): string
    {
        return Cryptography::encrypt($text, $key);
    }

    public static function decrypt(string $ciphertext, string $key): string
    {
        return Cryptography::decrypt($ciphertext, $key);
    }

    public function generateSalt(): string
    {
        return Cryptography::randomHex(32);
    }

    public function createUserKey(string $password, string $salt): string
    {
        return Cryptography::deriveEncryptionKey($password, $salt);
    }

    public static function setUserContext($userId, $key): void
    {
        Session::set('userId', $userId);
        Session::set('userKey', $key);
    }


    public static function getUserId(){
        return Session::get('userId');
    }
    public static function getAesKey(){
        return Session::get('userKey');
    }
    public function simpleHash(string $text): string
    {
        return Cryptography::hash($text, 'sha256');
    }

}