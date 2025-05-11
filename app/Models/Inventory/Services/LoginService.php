<?php

namespace Models\Inventory\Services;
use Zephyrus\Core\Session;
use Models\Inventory\Services\Encyryption\EncyrptionService;
use Models\Inventory\Validators\LoginValidator;
use Models\Inventory\Brokers\UserBroker;
use Zephyrus\Security\Cryptography;

class LoginService
{
    private UserBroker $broker;
    private LoginValidator $loginValidator;
    private EncyrptionService $encyrptionService;

    public function __construct()
    {
        $this->broker = new UserBroker();
        $this->loginValidator = new LoginValidator();
        $this->encyrptionService = new EncyrptionService();
    }

    public function login($form): array
    {
        try {
            $this->loginValidator->assert($form);

            $email= $form->getValue('email');
            $find = $this->encyrptionService->simpleHash($email);
            $password= $form->getValue('password');

            $user = $this->broker->findByEmailHash($find);
            if (!$user || !Cryptography::verifyHashedPassword($password, $user->password_hash)) {
                $form->addError('username', "Identifiants incorrects");
                return [
                    'form' => $form,
                    'errors' => $form->getErrorMessages()
                ];
            }

            $key = $this->encyrptionService->createUserKey($password, $user->salt);

            EncyrptionService::setUserContext($user->id, $key);

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
