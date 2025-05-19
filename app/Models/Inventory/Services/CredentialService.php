<?php

namespace Models\Inventory\Services;

use Models\Inventory\Brokers\CredentialBroker;
use Models\Inventory\Entities\Credential;
use Models\Inventory\Validators\CredentialValidator;


class CredentialService
{
    private CredentialValidator $validator;
    private CredentialBroker $broker;
    public function __construct()
    {
        $this->broker = new CredentialBroker();
        $this->validator = new CredentialValidator();
    }

    public function createCredential(int $userId, $form): array {

        try {
            $this->validator->assert($form);
            $data = $form->buildObject();

            $credential = new Credential();
            $credential->user_id = $userId;
            $credential->title              = $data->title;
            $credential->url                = $data->url;
            $credential->login              = $data->login;
            $credential->password_encrypted  = $data->password;
            $credential->notes              = $data->notes;


            $this->broker->insertCredential($credential);
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

    public function updateCredential($form, $credential): array {

        try {
            $this->validator->assert($form);
            $data = $form->buildObject();

            $credential->title              = $data->title;
            $credential->url                = $data->url;
            $credential->login              = $data->login;
            $credential->password_encrypted  = $data->password;
            $credential->notes              = $data->notes;


            $this->broker->updateCredential($credential);
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