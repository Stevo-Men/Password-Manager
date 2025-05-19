<?php

namespace Controllers;

use Models\Inventory\Brokers\CredentialBroker;
use Models\Inventory\Entities\Credential;
use Models\Inventory\Services\CredentialService;

use Zephyrus\Application\Rule;
use Zephyrus\Core\Session;
use Zephyrus\Network\Router\Get;
use Zephyrus\Network\Router\Post;
use Zephyrus\Network\Response;
use Zephyrus\Application\Flash;

class CredentialsController extends Controller
{
    private CredentialBroker $broker;
    private CredentialService $service;

    public function __construct()
    {
        $this->broker = new CredentialBroker();
        $this->service = new CredentialService();
    }


    #[Get("/credentials/create")]
    public function create(): Response
    {
        $userId = Session::get('userId') ?? null;
        $this->requireLogin($userId,'/login');

        $form = $this->buildForm();
        return $this->render("credentials/form", [
            'form'  => $form,
            'title' => 'Ajout d\'identification'
        ]);
    }


    #[Post("/credentials")]
    public function store(): Response
    {
        $userId = Session::get('userId') ?? null;
        $this->requireLogin($userId, '/login');

        $form = $this->buildForm();
        $this->service->createCredential($userId, $form);

        if (!$form->verify()) {
            return $this->render("credentials/form", [
                'form'  => $form,
                'title' => 'Ajouter un credential'
            ]);
        }

        return $this->redirect('/dashboard');
    }






    #[Get("/credentials/{id}/edit")]
    public function edit(int $id): Response
    {
        $userId = Session::get('userId') ?? null;
        $this->requireLogin($userId,'/login');

        $credential = $this->broker->findById($id);
        if (!$credential || $credential->user_id != $userId) {
            Flash::error("Credential introuvable ou accès refusé.");
            return $this->redirect('/dashboard');
        }

        $form = $this->buildForm();

        return $this->render("credentials/edit", [
            'credential' => $credential,
            'form'       => $form,
            'title'      => 'Modifier un credential'
        ]);
    }

    #[Post("/credentials/{id}")]
    public function update(int $id): Response
    {
        $userId = Session::get('userId') ?? null;
        $this->requireLogin($userId,'/login');

        $form = $this->buildForm();

        $credential = $this->broker->findById($id);
        $this->service->updateCredential($form, $credential);


        if (!$form->verify()) {
            $credential = $this->broker->findById($id);
            return $this->render("credentials/edit", [
                'credential' => $credential,
                'form'       => $form,
                'title'      => 'Modifier un credential'
            ]);
        }

        Flash::success("Credential mis à jour avec succès !");
        return $this->redirect('/dashboard');
    }




    #[Post("/credentials/{id}/delete")]
    public function delete(int $id): Response
    {
        //TO-DO ajouté confirmation
        if ($this->broker->delete($id)) {
            Flash::success("Credential supprimé avec succès.");
        } else {
            Flash::error("Erreur lors de la suppression.");
        }
        return $this->redirect('/dashboard');
    }


}
