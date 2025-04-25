<?php

namespace Controllers;

use Models\Brokers\UserBroker;
use Zephyrus\Application\Rule;
use Zephyrus\Network\Router\Get;
use Zephyrus\Network\Router\Post;
use Zephyrus\Network\Response;

class UsersController extends Controller
{
    private UserBroker $broker;

    public function __construct()
    {
        $this->broker = new UserBroker();
    }

    #[Post("/users")]
    public function store(): Response
    {
        $form = $this->buildForm();
        $form->field('username', [Rule::required("Requis")]);
        $form->field('email',    [Rule::required("Requis"), Rule::email("Email invalide")]);
        $form->field('password', [Rule::passwordCompliant("Mot de passe trop faible")]);
        if (!$form->verify()) {
            return $this->render("users/form", ['errors' => $form->getErrors()]);
        }
        $data = $form->buildObject();
        $data->password_hash = password_hash($_POST['password'], PASSWORD_BCRYPT);
        $this->broker->insert($data);
        return $this->redirect("/users");
    }

    #[Get("/users/{id}/edit")]
    public function edit(int $id): Response
    {
        $user = $this->broker->findById($id);
        if (!$user) {
            return $this->abortNotFound();
        }
        return $this->render("users/form", ['user' => $user]);
    }

    #[Post("/users/{id}")]
    public function update(int $id): Response
    {
        $data = (object)[
            'id'       => $id,
            'username' => $_POST['username'],
            'email'    => $_POST['email']
        ];
        $this->broker->update($data);
        return $this->redirect("/users");
    }

    #[Post("/users/{id}/delete")]
    public function delete(int $id): Response
    {
        $this->broker->delete($id);
        return $this->redirect("/users");
    }
}