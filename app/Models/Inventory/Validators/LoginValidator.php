<?php

namespace Models\Inventory\Validators;

use Models\Exceptions\FormException;
use Zephyrus\Application\Form;
use Zephyrus\Application\Rule;

class LoginValidator
{
    public static function assert(Form $form): void
    {
        $form->field("email", [
            Rule::required("Le courriel est obligatoire")
        ]);
        $form->field("password", [
            Rule::required("Le mot de passe est obligatoire"),
            Rule::minLength(4,"Longueur minimale de 4 caractères")
        ]);

        if (!$form->verify()) {
            throw new FormException($form);
        }
    }
}
