<?php

namespace Models\Inventory\Validators;

use Models\Exceptions\FormException;
use Zephyrus\Application\Rule;

class TwoFaValidator
{

    public static function validate($form):void {
        $form->field('code',[Rule::required("Code requis")]);

        if (!$form->verify()) {
            throw new FormException($form);
        }

    }
}