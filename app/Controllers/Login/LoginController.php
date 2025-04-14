<?php namespace Controllers\Login;


use Controllers\Controller;
use Zephyrus\Network\Response;
use Zephyrus\Network\Router\Get;

class LoginController extends Controller
{

    #[Get("/login")]
    public function loginPage(): Response
    {
        return $this->render("login", ['title' => 'Login']);
    }
}