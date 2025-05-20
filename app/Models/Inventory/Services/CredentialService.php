<?php

namespace Models\Inventory\Services;

use Models\Inventory\Brokers\CredentialBroker;
use Models\Inventory\Entities\Credential;
use Models\Inventory\Services\Cryptography\CryptographyService;
use Models\Inventory\Validators\CredentialValidator;
use Zephyrus\Application\Flash;


class CredentialService
{
    private CredentialValidator $validator;
    private CredentialBroker $broker;
    private CryptographyService $cryptoService;
    public function __construct()
    {
        $this->broker = new CredentialBroker();
        $this->validator = new CredentialValidator();
        $this->cryptoService = new CryptographyService();
    }

    public function createCredential(int $userId, $form, $userKey): array {

        try {
            $this->validator->assert($form);
            $data = $form->buildObject();

            $credential = new Credential();
            $credential->user_id = $userId;
            $credential->title              = $data->title;
            $credential->url                = $data->url;
            $credential->login              = $data->login;
            $credential->password_encrypted  = $this->cryptoService->encrypt($data->password,$userKey);
            $credential->notes              = $data->notes;


            $this->broker->insertCredential($credential);
            Flash::success("Credential « {$credential->title} » ajouté avec succès !");
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

    public function updateCredential($form, $credential, $userKey): array {

        try {
            $this->validator->assert($form);
            $data = $form->buildObject();

            $credential->title              = $data->title;
            $credential->url                = $data->url;
            $credential->login              = $data->login;
            $credential->password_encrypted  = $this->cryptoService->encrypt($data->password,$userKey);
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