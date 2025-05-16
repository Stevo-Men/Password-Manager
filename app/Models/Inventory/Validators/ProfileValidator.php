<?php

namespace Models\Inventory\Validators;

use Models\Inventory\Entities\User;

class ProfileValidator
{
    public static function ensureUserExists(?User $user): void
    {
        //Change Exception
        if (!$user) {
            throw new InvalidArgumentException("Utilisateur introuvable.");
        }
    }

    public static function validateUsername(string $username): void
    {
        $username = trim($username);
        if ($username === '') {
            throw new InvalidArgumentException("Le nom d’utilisateur ne peut pas être vide.");
        }
        if (mb_strlen($username) < 3) {
            throw new InvalidArgumentException("Le nom d’utilisateur doit faire au moins 3 caractères.");
        }
        // Ajouter d’autres contrôles si besoin (caractères autorisés, etc.)
    }
}