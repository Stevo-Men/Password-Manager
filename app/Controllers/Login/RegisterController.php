<?php namespace Controllers\Login;

use Models\Inventory\Services\RegisterService;
use Zephyrus\Application\Controller;
use Zephyrus\Network\Router\Get;
use Zephyrus\Network\Router\Post;
use Zephyrus\Network\Response;

class RegisterController extends Controller
{
    private RegisterService $service;

    public function __construct()
    {
        $this->service = new RegisterService();
    }

    #[Get("/register")]
    public function showForm(): Response
    {
        $form = $this->buildForm();
        return $this->render("register", ['form' => $form]);
    }

    #[Post("/register")]
    public function register(): Response
    {
        $form = $this->buildForm();

        $data = $this->service->register($form);

        $form = $data["form"];
        $errors = $data["errors"];

        if ($errors) {
            return $this->render("register", ['form' => $form]);
        }

        return $this->redirect('/login');
    }
}
