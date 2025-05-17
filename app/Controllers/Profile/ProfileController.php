<?php

namespace Controllers\Profile;

use Controllers\Controller;
use Models\Inventory\Services\ProfileService;
use Zephyrus\Application\Flash;
use Zephyrus\Core\Session;
use Zephyrus\Network\Response;
use Zephyrus\Network\Router\Get;
use Zephyrus\Network\Router\Post;



class ProfileController extends Controller
{
    private ProfileService $service;


    public function __construct()
    {
        $this->service = new ProfileService();
    }

    #[Get("/profile")]
    public function index(): Response
    {
        $userId = Session::get('userId') ?? null;

        if (!$userId) {
            return $this->redirect('/login');
        }

        try {
            $user = $this->service->getProfile($userId);
        } catch (\InvalidArgumentException $e) {
            return $this->redirect('/login');
        }

        return $this->render("profile", [
            'user'  => $user,
            'title' => 'Mon profil'
        ]);
    }


    #[Get("/profile/edit")]
    public function edit(): Response
    {
        $userId = Session::get('userId') ?? null;
        if (!$userId) {
            return $this->redirect('/login');
        }

        $form = $this->buildForm();
        $user = $this->service->getProfile($userId);

        return $this->render("profile/edit", [
            'user'  => $user,
            'form'  => $form,
            'title' => 'Modifier mon profil'
        ]);
    }

    #[Post("/profile/edit")]
    public function update(): Response
    {
        $userId = Session::get('userId') ?? null;
        if (!$userId) {
            return $this->redirect('/login');
        }
        $newUsername = (string) ($_POST['username'] ?? '');
        try {
            $this->service->updateUsername($userId, $newUsername);

            $_SESSION['username'] = $newUsername;
            Flash::success("Nom d’utilisateur mis à jour.");
            return $this->redirect('/profile');
        } catch (\Exception $e) {
            Flash::error($e->getMessage());
            $user = $this->service->getProfile($userId);
            return $this->render("profile/edit", [
                'user'   => $user,
                'title'  => 'Modifier mon profil',
                'errors' => [$e->getMessage()]
            ]);
        }
    }

    #[Get("/profile/change-password")]
    public function editPassword(): Response
    {

        $form = $this->buildForm();
        return $this->render("profile/change-password", [
            'form'  => $form,
            'title' => 'Changer mon mot de passe'
        ]);
    }


    #[Post("/profile/change-password")]
    public function updatePassword(): Response
    {
        $form = $this->buildForm();
        $userId = Session::get('userId') ?? null;
        if (!$userId) {
            return $this->redirect('/login');
        }


        $oldPwd  = $form->getValue('old_password');
        $newPwd  = $form->getValue('new_password');
        $confirm = $form->getValue('confirm_password');

        $data = $this->service->changePassword($form,$userId, $oldPwd, $newPwd, $confirm);

        $form = $data["form"];
        $errors = $data["errors"];


        if ($errors) {
            return $this->render("profile/change-password", ['form' => $form, 'errors' => $errors]);
        }

            return $this->redirect('/profile');

    }


}