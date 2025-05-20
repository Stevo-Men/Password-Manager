<?php

namespace Controllers\Login;

use Controllers\Controller;
use Models\Inventory\Services\TwoFaService;
use Zephyrus\Application\Flash;
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

        $form = $this->buildForm();
        return $this->render('login/2fa', ['form' => $form, 'title' => 'Vérification']);
    }

    #[Post("/login/2fa")]
    public function twoFaCheck(): Response
    {
        $form = $this->buildForm();
        $userId = Session::get('userId') ?? null;
        if (!$userId) {
            return $this->redirect('/login');
        }


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

    #[Get("/login/2fa/resend")]
    public function resend(): Response
    {
        $userId = Session::get('userId') ?? null;
        if (!$userId) {
            return $this->redirect('/login');
        }

        $this->twoFaService->resendCode($userId);
        if ($this->twoFaService->resendCode($userId)) {
            return $this->redirect('/login');
        }


        Flash::success("Un nouveau code vous a été envoyé par e-mail.");
        return $this->redirect('/login/2fa');
    }

}