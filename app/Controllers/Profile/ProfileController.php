<?php

namespace Controllers\Profile;


use Controllers\Controller;
use Models\Inventory\Services\ProfileService;
use Zephyrus\Network\Response;
use Zephyrus\Network\Router\Get;
use Zephyrus\Network\Router\Post;



class ProfileController extends Controller
{
    private ProfileService $profileService;

    public function __construct()
    {
        $this->profileService = new ProfileService();
        $this->broker = new UserBroker();
    }



    #[Get("/profile")]
    public function index(): Response
    {
        $userId = $this->getSession('user_id');
        if (!$userId) {
            return $this->redirect('/login');
        }

        $user = $this->broker->findById($userId);
        if (!$user) {
            return $this->redirect('/login');
        }

        return $this->render("profile", [
            'user'  => $user,
            'title' => 'Mon profil'
        ]);
    }


    #[Post("/profile/edit")]
    public function updateInfo(): Response
    {
        $form = $this->buildForm();
        return $this->render("profile", ['form' => $form]);

    }


}