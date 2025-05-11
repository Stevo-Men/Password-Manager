<?php namespace Controllers\Home;

use Controllers\Controller;
use Zephyrus\Core\Session;
use Zephyrus\Network\Response;
use Zephyrus\Network\Router\Get;

class HomeController extends Controller
{
    #[Get("/")]
    public function index(): Response
    {
        $userId = Session::get('userId') ?? null;
        if (!$userId) {
            return $this->redirect('/login');
        }

        return $this->redirect('/dashboard');
    }
}
