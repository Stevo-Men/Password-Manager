<?php

namespace Models\Inventory\Services;

use Models\Inventory\Brokers\ProfileBroker;
use Models\Inventory\Services\Encyryption\EncyrptionService;
use Models\Inventory\Validators\RegisterValidator;

class ProfileService
{
    public function __construct()
    {
        $this->broker = new ProfileBroker();
        $this->encrypt = new EncyrptionService();
        $this->validator = new ProfileValidator();
    }
}