<?php

namespace Controllers\Login;

use Zephyrus\Application\Controller;
use Zephyrus\Network\Response;
use Zephyrus\Network\Router\Get;

class RegisterController extends Controller
{

    #[Get("/register")]
    public function register(): Response
    {
        return $this->render("register", ['title' => 'Register']);
    }
}