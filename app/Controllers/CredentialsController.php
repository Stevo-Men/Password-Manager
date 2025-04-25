<?php

namespace Controllers;

use Models\Inventory\Brokers\CredentialBroker;
use Zephyrus\Application\Controller;
use Zephyrus\Application\Rule;
use Zephyrus\Network\Router\Get;
use Zephyrus\Network\Router\Post;
use Zephyrus\Network\Response;
use Zephyrus\Application\Flash;

class CredentialsController extends Controller
{
    private CredentialBroker $broker;

    public function __construct()
    {
        $this->broker = new CredentialBroker();
    }


    #[Get("/credentials/create")]
    public function create(): Response
    {
        $form = $this->buildForm();
        return $this->render("credentials/form", [
            'form'  => $form,
            'title' => 'Ajouter un credential'
        ]);
    }


    #[Post("/credentials")]
    public function store(): Response
    {
        $form = $this->buildForm();
        $form->field('title',    [Rule::required("Le titre est requis")]);
        $form->field('url',      []);
        $form->field('login',    [Rule::required("Le login est requis")]);
        $form->field('password',[Rule::required("Le mot de passe est requis")]);
        $form->field('notes',    []);

        if (!$form->verify()) {
            return $this->render("credentials/form", [
                'form'  => $form,
                'title' => 'Ajouter un credential'
            ]);
        }

        $data = $form->buildObject();
        $userId = $_SESSION['user_id'];


        $this->broker->insert((object)[
            'user_id'            => $userId,
            'title'              => $data->title,
            'url'                => $data->url,
            'login'              => $data->login,
            'password_encrypted' => $data->password,
            'notes'              => $data->notes
        ]);

        Flash::success("Credential « {$data->title} » ajouté avec succès !");
        return $this->redirect('/dashboard');
    }
}
