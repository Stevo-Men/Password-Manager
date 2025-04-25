<?php

namespace Controllers\Login;

use Zephyrus\Application\Controller;
use Zephyrus\Application\Rule;
use Zephyrus\Network\Router\Get;
use Zephyrus\Network\Router\Post;
use Zephyrus\Network\Response;
use Models\Inventory\Brokers\UserBroker;

class LoginController extends Controller
{
    private UserBroker $broker;

    public function __construct()
    {
        $this->broker = new UserBroker();
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

        $form->field('username', [Rule::required("Le nom d'utilisateur est requis")]);
        $form->field('password', [Rule::required("Le mot de passe est requis")]);

        if (!$form->verify()) {
            return $this->render("login", ['form' => $form,'title' => "Dashboard"]);
        }

        $data = $form->buildObject();
        $user = $this->broker->findByUsername($data->username);
        if (!$user || !password_verify($data->password, $user->password_hash)) {
            $form->addError('username', "Identifiants incorrects");
            return $this->render("login", ['form' => $form]);
        }

        $_SESSION['user_id']  = $user->id;
        $_SESSION['username'] = $user->username;


        return $this->redirect('/dashboard');
    }
}
