<?php namespace Controllers\Login;

use Zephyrus\Application\Controller;
use Zephyrus\Application\Rule;
use Zephyrus\Network\Router\Get;
use Zephyrus\Network\Router\Post;
use Zephyrus\Network\Response;
use Zephyrus\Application\Flash;
use Models\Inventory\Brokers\UserBroker;

class RegisterController extends Controller
{
    private UserBroker $broker;

    public function __construct()
    {
        $this->broker = new UserBroker();
    }


    #[Get("/register")]
    public function showForm(): Response
    {
        $form = $this->buildForm();
        return $this->render("register", ['form' => $form]);
    }


    #[Post("/register")]
    public function processForm(): Response
    {
        $form = $this->buildForm();

        $form->field('username',         [Rule::required("Le nom d'utilisateur est requis")]);
        $form->field('email',            [Rule::required("L’email est requis"), Rule::email("Email invalide")]);
        $form->field('password',         [Rule::required("Le mot de passe est requis"), Rule::passwordCompliant("Mot de passe trop faible. Doit être plus que 8 caractères et doit contenir au moins une minuscule, une majuscule et un chiffre.")]);
        $form->field('password_confirm', [Rule::required("La confirmation est requise")]);


        if (!$form->verify()) {
            return $this->render("register", ['form' => $form]);
        }


        $data = $form->buildObject();


        if ($data->password !== $data->password_confirm) {
            $form->addError('password_confirm', "Les deux mots de passe ne correspondent pas");
        }
        if ($this->broker->findByUsername($data->username)) {
            $form->addError('username', "Ce nom d'utilisateur est déjà pris");
        }
        if ($this->broker->findByEmail($data->email)) {
            $form->addError('email', "Cette adresse email est déjà utilisée");
        }

        if (!empty($form->getErrors())) {
            return $this->render("register", ['form' => $form]);
        }

        $hash = password_hash($data->password, PASSWORD_BCRYPT);
        $newUserId = $this->broker->insert((object)[
            'username'      => $data->username,
            'email'         => $data->email,
            'password_hash' => $hash
        ]);

        $_SESSION['user_id']  = $newUserId;
        $_SESSION['username'] = $data->username;
        Flash::success("Bienvenue, {$data->username} ! Votre compte a bien été créé.");


        return $this->redirect('/dashboard');
    }
}
