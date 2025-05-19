<?php

namespace Models\Inventory\Services;
use Models\Inventory\Brokers\TwoFaBroker;
use Zephyrus\Core\Session;
use Models\Inventory\Services\Cryptography\CryptographyService;
use Models\Inventory\Validators\LoginValidator;
use Models\Inventory\Brokers\UserBroker;
use Zephyrus\Security\Cryptography;

class LoginService
{
    private UserBroker $broker;
    private LoginValidator $loginValidator;
    private CryptographyService $encryptionService;
    private TwoFaService $twoFaService;

    public function __construct()
    {
        $this->broker = new UserBroker();
        $this->loginValidator = new LoginValidator();
        $this->encryptionService = new CryptographyService();
        $this->twoFaService = new TwofaService();
    }

    public function login($form): array
    {
        try {
            $this->loginValidator->assert($form);

            $email= $form->getValue('email');
            $find = $this->encryptionService->simpleHash($email);
            $password= $form->getValue('password');

            $user = $this->broker->findByEmailHash($find);
            if (!$user || !Cryptography::verifyHashedPassword($password, $user->password_hash)) {
                $form->addError('username', "Identifiants incorrects");
                return [
                    'form' => $form,
                    'errors' => $form->getErrorMessages()
                ];
            }

            $key = $this->encryptionService->createUserKey($password, $user->salt);

            CryptographyService::setUserContext($user->id, $key);
            $this->twoFaService->generateAndSendCode($user->id, $user->email);
            $_SESSION['pending_2fa_user'] = $user->id;

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
}
