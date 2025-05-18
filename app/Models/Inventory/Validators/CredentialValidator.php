<?php

namespace Models\Inventory\Validators;

use Models\Exceptions\FormException;
use Zephyrus\Application\Form;
use Zephyrus\Application\Rule;

class CredentialValidator
{

    public static function assert(Form $form): void  {

        $form->field('title',    [Rule::required("Le titre est requis")]);
        $form->field('url',      []);
        $form->field('login',    [Rule::required("Le login est requis")]);
        $form->field('password', [Rule::required("Le mot de passe est requis")]);
        $form->field('notes',    []);

        if (!$form->verify()) {
            throw new FormException($form);
        }

    }


}