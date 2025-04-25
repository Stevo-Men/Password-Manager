<?php

namespace Controllers;

use Models\Inventory\Brokers\CredentialBroker;
use Zephyrus\Network\Router\Get;
use Zephyrus\Network\Response;

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
        $userId = $_SESSION['user_id'] ?? null;
        if (!$userId) {
            return $this->redirect('/login');
        }

        $credentials = $this->broker->findByUser($userId);

        $username = $_SESSION['username'] ?? '';

        return $this->render("dashboard", [
            'title' => "Dashboard",
            'credentials' => $credentials,
            'username'    => $username
        ]);
    }
}
