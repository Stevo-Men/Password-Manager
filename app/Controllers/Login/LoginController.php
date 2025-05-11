<?php

namespace Controllers\Login;

use Models\Inventory\Services\LoginService;
use Zephyrus\Application\Controller;
use Zephyrus\Network\Router\Get;
use Zephyrus\Network\Router\Post;
use Zephyrus\Network\Response;

class LoginController extends Controller
{
    private LoginService $loginService;

    public function __construct()
    {
        $this->loginService = new LoginService();
    }

    #[Get("/login")]
    public function index(): Response
    {
        $form = $this->buildForm();
        return $this->render("login", [
            'form'  => $form,
            'title' => 'Connexion'
        ]);
    }

    #[Post("/login")]
    public function login(): Response
    {
        $form = $this->buildForm();

        $data = $this->loginService->login($form);

        $form = $data["form"];
        $errors = $data["errors"];

        if ($errors) {
            return $this->render("login", ['form' => $form]);
        }

        return $this->redirect('/dashboard');
    }
}
