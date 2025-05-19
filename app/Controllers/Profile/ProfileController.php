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
        $this->requireLogin($userId,'/login');

        $user = $this->service->getProfile($userId);


        return $this->render("profile", [
            'user'  => $user,
            'title' => 'Mon profil'
        ]);
    }


    #[Get("/profile/edit")]
    public function edit(): Response
    {
        $userId = Session::get('userId') ?? null;
        $this->requireLogin($userId,'/login');

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
        $this->requireLogin($userId,'/login');

        $form = $this->buildForm();

        $data = $this->service->updateUsername($userId, $form);


        $form = $data["form"];
        $errors = $data["errors"];
            $user = $this->service->getProfile($userId);
            if ($errors) {
                return $this->render("profile/edit", [
                    'user'   => $user,
                    'title'  => 'Modifier mon profil',
                    'form'   => $form,
                    'errors' => 'errors'
                ]);
            }

        return $this->redirect('/profile');
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
        $this->requireLogin($userId,'/login');

        $newPassword  = $form->getValue('new_password');

        $data = $this->service->changePassword($form,$userId, $newPassword);
        $form = $data["form"];
        $errors = $data["errors"];


        if ($errors) {
            return $this->render("profile/change-password", ['form' => $form, 'errors' => $errors]);
        }
            return $this->redirect('/profile');
    }

    #[Post("/profile/avatar")]
    public function uploadAvatar(): Response
    {
        $userId = Session::get('userId') ?? null;
        $this->requireLogin($userId,'/login');


        if (empty($_FILES['avatar']) || $_FILES['avatar']['error'] !== UPLOAD_ERR_OK) {
            Flash::error("Upload invalide");
            return $this->redirect('/profile');
        }

        $tmp      = $_FILES['avatar']['tmp_name'];
        $ext      = pathinfo($_FILES['avatar']['name'], PATHINFO_EXTENSION);
        $filename = "avatar_{$userId}_" . time() . ".{$ext}";


        $projectRoot = dirname(__DIR__, 3); // /var/www/html
        $uploadDir   = $projectRoot . '/public/assets/images/avatars/';
        if (!is_dir($uploadDir) && !mkdir($uploadDir, 0755, true)) {
            Flash::error("Impossible de créer le dossier d’upload");
            return $this->redirect('/profile');
        }

        $destination = $uploadDir . $filename;
        if (!move_uploaded_file($tmp, $destination)) {
            Flash::error("Échec du déplacement du fichier vers {$destination}");
            return $this->redirect('/profile');
        }

        $relativePath = 'assets/images/avatars/' . $filename;
        $this->service->updateAvatarPath($userId, $relativePath);

        Flash::success("Avatar mis à jour");
        return $this->redirect('/profile');
    }



}