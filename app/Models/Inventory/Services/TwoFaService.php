<?php

namespace Models\Inventory\Services;

use Cassandra\Uuid;
use Models\Inventory\Brokers\TwoFaBroker;
use Models\Inventory\Brokers\UserBroker;
use Models\Inventory\Validators\TwoFaValidator;
use Nette\Mail\Message;
use Nette\Mail\SmtpMailer;
use Zephyrus\Application\Flash;
use Zephyrus\Application\Form;

class TwoFaService
{
    private TwoFaBroker $broker;
    private SmtpMailer $mailer;
    private TwoFaValidator $validator;
    private UserBroker $userBroker;

    public function __construct()
    {
        $this->validator = new TwoFaValidator();
        $this->broker = new TwoFaBroker();
        $this->userBroker = new UserBroker();

        $host = getenv('SMTP_HOST') ?: 'localhost';
        $port = (int)(getenv('SMTP_PORT') ?: 1025);

        $this->mailer = new SmtpMailer(
            host: 'localhost',
            username: '',
            password: '',
            port: (int)(getenv('SMTP_PORT') ?: 1025),
            encryption: null
        );
    }

    public function generateAndSendCode(int $userId, string $email): void
    {

        $code = str_pad((string)random_int(0, 999999), 6, '0', STR_PAD_LEFT);
        $expires = new \DateTimeImmutable('+5 minutes');
        $this->broker->insertCode($userId, $code, $expires);

        $mail = new Message();
        $mail->setFrom('no-reply@example.com')
            ->addTo($email)
            ->setSubject('Votre code de connexion')
            ->setBody("Votre code 2FA est : $code (valable 5 min).");
        $this->mailer->send($mail);
    }

    public function verifyCode(int $userId, Form $form): bool
    {
        $code = $form->getValue('code');

        $this->validator->validate($form);

        $row = $this->broker->findValidCode($userId, $code);
        if (!$row) {
            return false;
        }
        $this->broker->markUsed($row->id);
        return true;
    }

    public function resendCode(int $userId): bool
    {


        $user = $this->userBroker->findById($userId);
        if (!$user) {
            Flash::error("Utilisateur introuvable.");
            return false;
        }

        $this->generateAndSendCode($userId, $user->email);
        return true;
    }

}
