<?php

namespace Models\Inventory\Services;

use Models\Inventory\Entities\User;
use Models\Inventory\Services\Cryptography\CryptographyService;
use Models\Inventory\Validators\RegisterValidator;
use Models\Inventory\Brokers\UserBroker;
use Zephyrus\Security\Cryptography;
use Zephyrus\Network\Router\Post;

class RegisterService
{
    private UserBroker $broker;
    private CryptographyService $encrypt;
    private RegisterValidator $validator;

    public function __construct()
    {
        $this->broker = new UserBroker();
        $this->encrypt = new CryptographyService();
        $this->validator = new RegisterValidator();
    }

    #[Post("/register")]
    public function register($form): array
    {
        try {
            $this->validator->assert($form, $this->broker);

            if ($form->getErrors()) {
                return [
                    'form' => $form,
                    'errors' => $form->getErrorMessages()
                ];
            }

            $firstname = $form->getValue('firstname');
            $lastname = $form->getValue('lastname');
            $password = $form->getValue('password');
            $email = $form->getValue('email');
            $username = $form->getValue('username');

            $salt = $this->encrypt->generateSalt();
            $userKey = $this->encrypt->createUserKey($password, $salt);
            $hash = Cryptography::hashPassword($password);

            $user = new User();
            $user->firstname = $this->encrypt->encrypt($firstname, $userKey);
            $user->lastname = $this->encrypt->encrypt($lastname, $userKey);
            $user->username = $this->encrypt->encrypt($username, $userKey);
            $user->email = $this->encrypt->encrypt($email, $userKey);
            $user->password_hash = $hash;
            $user->salt = $salt;
            $user->email_hash = $this->encrypt->simpleHash($email);
            $newUserId = $this->broker->insert($user);


            CryptographyService::setUserContext($newUserId, $userKey);


            return [
                'form' => $form
            ];
        } catch (\Exception) {
            return [
                'form' => $form,
                'errors' => $form->getErrorMessages()
            ];
        }
    }
}
