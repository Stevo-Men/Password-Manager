<?php

namespace Models\Inventory\Validators;

use Models\Exceptions\FormException;
use Models\Inventory\Services\Cryptography\CryptographyService;
use Zephyrus\Application\Form;
use Zephyrus\Application\Rule;

class RegisterValidator
{
    public static function assert(Form $form, $broker): void
    {
        $form->field("firstname", [
            Rule::required("Le prénom est obligatoire")
        ]);
        $form->field("lastname", [
            Rule::required("Le nom est obligatoire")
        ]);

        $form->field("username", [
            Rule::required("Le username est obligatoire")
        ]);
        $form->field("username", [
            Rule::required("Le username est obligatoire")
        ]);
        $form->field("password", [
            Rule::required("Le password est obligatoire"),
            Rule::minLength(8,"Longueur minimale de 8 caractères")
        ]);
        $form->field("password_confirm", [
            Rule::required("Le password est obligatoire"),
            Rule::minLength(8,"Longueur minimale de 8 caractères")
        ]);
        $form->field("email", [
            Rule::required("Le password est obligatoire"),
            Rule::email("Longueur minimale de 8 caractères")
        ]);

        $password = $form->getValue("password");
        $password_confirm = $form->getValue("password_confirm");
        $email = $form->getValue("email");
        $emailHash = (new CryptographyService)->simpleHash($email);
        $username = $form->getValue("username");

        if ($password !== $password_confirm) {
            $form->addError('password_confirm', "Les deux mots de passe ne correspondent pas");
        }
        if ($broker->findByUsername($username)) {
            $form->addError('username', "Ce nom d'utilisateur est déjà pris");
        }
        if ($broker->findByEmailHash($emailHash)) {
            $form->addError('email', "Cette adresse email est déjà utilisée");
        }

        if (!$form->verify()) {
            throw new FormException($form);
        }
    }
}