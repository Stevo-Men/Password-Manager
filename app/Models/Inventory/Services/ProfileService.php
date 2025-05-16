<?php

namespace Models\Inventory\Services;

use Models\Inventory\Brokers\ProfileBroker;
use Models\Inventory\Entities\User;
use Models\Inventory\Services\Cryptography\CryptographyService;
use Models\Inventory\Validators\ProfileValidator;
use Zephyrus\Core\Session;

class ProfileService
{
    private ProfileBroker $broker;
    private CryptographyService $cryptoService;

    public function __construct()
    {
        $this->broker = new ProfileBroker();
        $this->cryptoService = new CryptographyService();
    }

    public function getProfile(int $userId): User
    {

        $user = $this->broker->findUserById($userId);
        ProfileValidator::ensureUserExists($user);

        $userKey = $this->cryptoService->getAesKey();
        if (!$userKey) {
            throw new \RuntimeException("Clé de chiffrement manquante en session.");
        }

        $user->decryptUserInfo($user,$userKey);

        return $user;
    }

    public function updateUsername(int $userId, string $newUsername): void
    {
        ProfileValidator::validateUsername($newUsername);

        $userKey = $this->cryptoService->getAesKey();
        if (!$userKey) {
            throw new \RuntimeException("Clé de chiffrement manquante en session.");
        }

        $encrypted = $this->cryptoService->encrypt($newUsername, $userKey);

        if (!$this->broker->updateUsername($userId, $encrypted)) {
            throw new \RuntimeException("Impossible de mettre à jour le nom d’utilisateur.");
        }
    }
}