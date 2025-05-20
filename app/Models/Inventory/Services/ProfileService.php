<?php

namespace Models\Inventory\Services;

use Models\Inventory\Brokers\ProfileBroker;
use Models\Inventory\Entities\User;
use Models\Inventory\Services\Cryptography\CryptographyService;
use Models\Inventory\Validators\ProfileValidator;
use Zephyrus\Application\Flash;
use Zephyrus\Security\Cryptography;

class ProfileService
{
    private ProfileBroker $broker;
    private CryptographyService $cryptoService;
    private ProfileValidator $validator;

    public function __construct()
    {
        $this->broker = new ProfileBroker();
        $this->cryptoService = new CryptographyService();
        $this->validator = new ProfileValidator();
    }

    public function getProfile(int $userId): User
    {

        $user = $this->broker->findUserById($userId);
        $this->validator->ensureUserExists($user);

        $userKey = $this->cryptoService->getAesKey();
        if (!$userKey) {
            throw new \RuntimeException("Clé de chiffrement manquante en session.");
        }

        $user->decryptUserInfo($user,$userKey);

        return $user;
    }

    public function updateUsername(int $userId,$form): array
    {
        try {
            ProfileValidator::validateUsername($form);

            $userKey = $this->cryptoService->getAesKey();

            $encrypted = $this->cryptoService->encrypt($form->getValue('username'), $userKey);

            if (!$this->broker->updateUsername($userId, $encrypted)) {
                throw new \RuntimeException("Impossible de mettre à jour le nom d’utilisateur.");
            }

            $_SESSION['username'] = $form->getValue('username');
            Flash::success("Nom d’utilisateur mis à jour.");
            return [
                'form' => $form
            ];
        } catch (\Exception) {
            return [
                'form' => $form,
                'errors' => $form->getErrors()
            ];
        }
    }


    public function changePassword($form,int $userId, string $newPassword): array
    {
        try {
            $user = $this->broker->findUserById($userId);

            if (!Cryptography::verifyHashedPassword($form->getValue('old_password'), $user->password_hash)) {
                $form->addError('old_password', "Mot de passe actuel incorrect.");
                return [
                    'form' => $form,
                    'errors' => $form->getErrorMessages()
                ];
            }

        $this->validator->assert($form);



        $oldKey = $this->cryptoService->getAesKey();
        $plaintext = [
            'firstname' => $this->cryptoService->decrypt($user->firstname, $oldKey),
            'lastname'  => $this->cryptoService->decrypt($user->lastname,  $oldKey),
            'username'  => $this->cryptoService->decrypt($user->username,  $oldKey),
            'email'     => $this->cryptoService->decrypt($user->email,     $oldKey)
        ];


        $newSalt = $this->cryptoService->generateSalt();
        $newKey  = $this->cryptoService->createUserKey($newPassword, $newSalt);
        $newHash = Cryptography::hashPassword($newPassword);


        $user->firstname = $this->cryptoService->encrypt($plaintext['firstname'], $newKey);
        $user->lastname = $this->cryptoService->encrypt($plaintext['lastname'], $newKey);
        $user->username = $this->cryptoService->encrypt($plaintext['username'], $newKey);
        $user->email = $this->cryptoService->encrypt($plaintext['email'], $newKey);
        $user->password_hash = $newHash;
        $user->salt = $newSalt;
        $user->email_hash = $this->cryptoService->simpleHash($user->email);
        $user->salt = $newSalt;


        if ($this->broker->updateEncryptedProfile($user, $userId)){
            Flash::success("Nom d’utilisateur mis à jour.");
            CryptographyService::setUserContext($userId, $newKey);
        };




            return [
                'form' => $form
            ];
        } catch (\Exception) {
            return [
                'form' => $form,
                'errors' => $form->getErrors()
            ];
        }
    }

    public function updateAvatarPath(int $userId, string $path): void {


        $this->broker->updateAvatarPath($userId, $path);
    }

}