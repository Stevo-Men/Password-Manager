<?php

namespace Models\Inventory\Services\Encyryption;

use Zephyrus\Security\Cryptography;
use Zephyrus\Core\Session;

class EncyrptionService
{
    public function encrypt(string $text, string $key): string
    {
        return Cryptography::encrypt($text, $key);
    }

    public function decrypt(string $ciphertext, string $key): string
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

    public function simpleHash(string $text): string
    {
        return Cryptography::hash($text, 'sha256');
    }

}