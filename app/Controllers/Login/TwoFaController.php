<?php

namespace Controllers\Login;

use Controllers\Controller;
use Models\Inventory\Services\TwoFaService;
use Zephyrus\Core\Session;
use Zephyrus\Network\Response;
use Zephyrus\Network\Router\Get;
use Zephyrus\Network\Router\Post;


class TwoFaController extends Controller
{

    private TwoFaService $twoFaService;

    public function __construct()
    {
        $this->twoFaService = new TwoFaService();
    }
    #[Get("/login/2fa")]
    public function twoFaForm(): Response
    {
        if (empty($_SESSION['pending_2fa_user'])) {
            return $this->redirect('/login');
        }
        $form = $this->buildForm();
        return $this->render('login/2fa', ['form'=>$form]);
    }

    #[Post("/login/2fa")]
    public function twoFaCheck(): Response
    {
        $form = $this->buildForm();


        $userId = Session::get('userId') ?? null;
        if ($this->twoFaService->verifyCode($userId,$form)) {
            unset($_SESSION['pending_2fa_user']);
            $_SESSION['userId'] = $userId;
            return $this->redirect('/dashboard');
        }

        $form->addError('code',"Code invalide ou expiré");
        if (!$form->verify()) {
            return $this->render('login/2fa',['form'=>$form]);
        }

        return $this->render('login/2fa',['form'=>$form]);
    }



}