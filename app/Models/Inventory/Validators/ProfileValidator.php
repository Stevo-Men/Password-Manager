<?php

namespace Models\Inventory\Validators;

use Models\Exceptions\FormException;
use Models\Inventory\Entities\User;
use Zephyrus\Application\Form;
use Zephyrus\Application\Rule;
use Zephyrus\Security\Cryptography;

class ProfileValidator
{
    public static function ensureUserExists(?User $user): void
    {
        if (!$user) {
            throw new InvalidArgumentException("Utilisateur introuvable.");
        }
    }

    public static function validateUsername(Form $form): void
    {

        $form->field("username", [
            Rule::required("Le nom d'utilisateur est obligatoire"),
            Rule::minLength(4,"Longueur minimale de 4 caractères")
        ]);

        if (!$form->verify()) {
            throw new FormException($form);
        }

    }




    public static function assert(Form $form, $broker): void
    {
        $form->field("old_password", [
            Rule::required("L’ancien mot de passe est requis")
        ]);

        $form->field("new_password", [
            Rule::required("Le password est obligatoire"),
            Rule::minLength(4,"Longueur minimale de 4 caractères")
        ]);

        $form->field("confirm_password", [
            Rule::required("La confirmation est requise")
        ]);




//        if ($form->getValue('new_password') !== $form->getValue('password_confirm')) {
//            $form->addError('password_confirm', "Les deux mots de passe ne correspondent pas");
//        }

        if (!$form->verify()) {
            throw new FormException($form);
        }

    }
}