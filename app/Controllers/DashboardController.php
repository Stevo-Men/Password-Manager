<?php

namespace Controllers;

use Models\Inventory\Brokers\CredentialBroker;
use Zephyrus\Core\Session;
use Zephyrus\Network\Router\Get;
use Zephyrus\Network\Response;
use Zephyrus\Network\Router\Post;

class DashboardController extends Controller
{
    private CredentialBroker $broker;

    public function __construct()
    {
        $this->broker = new CredentialBroker();
    }

    #[Get("/dashboard")]
    public function index(): Response
    {
        $userId = Session::get('userId') ?? null;
        $this->requireLogin($userId,'/login');

        $credentials = $this->broker->findByUser($userId);

        $username = $_SESSION['username'] ?? '';

        return $this->render("dashboard", [
            'title' => "Dashboard",
            'credentials' => $credentials,
            'username'    => $username
        ]);
    }

    #[Get("/logout")]
    public function logout(): Response
    {
        Session::destroy();
        return $this->redirect('/login');
    }
}
