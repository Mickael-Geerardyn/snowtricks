<?php

namespace App\Controller;

use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Csrf\CsrfToken;

class MailerController extends AbstractController
{
	private MailerInterface $mailer;

	public function __construct(MailerInterface $mailer)
	{
		$this->mailer = $mailer;
	}


    /**
     * @throws TransportExceptionInterface
     */
    #[Route('/registration-mailer', name: 'app_registration_mailer')]
    public function sendRegistrationMail(string $userEmail, string $userName, string $token): void
    {
            $email = (new TemplatedEmail())
                ->from('noreply@snowtricks.com')
                ->to($userEmail)
                ->subject('Validez votre inscription!')
                ->htmlTemplate('mailer/user-registration.html.twig')
                ->context([
                    'userName' => $userName,
                    'userEmail' => $userEmail,
                    'token' => $token
                ]);

            $this->mailer->send($email);
    }

	/**
	 * @throws TransportExceptionInterface
	 */
	#[Route('/forgot-password-mailer', name: 'app_forgot_password_mailer')]
	public function sendForgotPasswordMail(string $userEmail, string $userName, string $token): void
	{
		$email = (new TemplatedEmail())
			->from('noreply@snowtricks.com')
			->to($userEmail)
			->subject('Validez votre inscription!')
			->htmlTemplate('mailer/forgot-password.html.twig')
			->context([
						  'userName' => $userName,
						  'userEmail' => $userEmail,
						  'token' => $token
					  ]);

		$this->mailer->send($email);
	}
}
